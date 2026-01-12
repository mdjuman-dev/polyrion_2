<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // Verify Cloudflare reCAPTCHA if enabled
        if (\App\Services\CloudflareRecaptchaService::isEnabled()) {
            $token = $input['cf_turnstile_response'] ?? null;
            if (!\App\Services\CloudflareRecaptchaService::verify($token, request()->ip())) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cf_turnstile_response' => 'reCAPTCHA verification failed. Please try again.',
                ]);
            }
        }

        $emailOrNumber = trim($input['email_or_number'] ?? '');
        $name = trim($input['name'] ?? '');

        if (empty($emailOrNumber)) {
            Validator::make([], [
                'email_or_number' => 'required',
            ])->validate();
        }

        $isEmail = filter_var($emailOrNumber, FILTER_VALIDATE_EMAIL);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email_or_number' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ];

        $validator = Validator::make($input, $rules);

        $validator->after(function ($validator) use ($emailOrNumber, $isEmail) {
            if (empty($emailOrNumber)) {
                $validator->errors()->add('email_or_number', 'Email or Phone Number is required');
                return;
            }

            if ($isEmail) {
                $emailValidator = Validator::make(
                    ['email' => $emailOrNumber],
                    [
                        'email' => [
                            'required',
                            'string',
                            'email:rfc,dns',
                            'max:255',
                            Rule::unique(User::class),
                        ],
                    ]
                );

                if ($emailValidator->fails()) {
                    foreach ($emailValidator->errors()->get('email') as $error) {
                        $validator->errors()->add('email_or_number', $error);
                    }
                }
            } else {
                $normalizedNumber = preg_replace('/[\s\-\(\)\+]/', '', $emailOrNumber);

                if (strlen($normalizedNumber) < 10 || strlen($normalizedNumber) > 20) {
                    $validator->errors()->add('email_or_number', 'Phone Number must be between 10 and 20 digits.');
                    return;
                }

                $numberValidator = Validator::make(
                    ['number' => $emailOrNumber],
                    [
                        'number' => [
                            'required',
                            'string',
                            'max:20',
                            Rule::unique(User::class, 'number'),
                        ],
                    ]
                );

                if ($numberValidator->fails()) {
                    foreach ($numberValidator->errors()->get('number') as $error) {
                        $validator->errors()->add('email_or_number', $error);
                    }
                }
            }
        });

        $validator->validate();

        $username = $this->generateUsername($name);

        $userData = [
            'name' => $name,
            'username' => $username,
            'password' => $input['password'],
        ];

        if ($isEmail) {
            $userData['email'] = strtolower($emailOrNumber);
        } else {
            $userData['number'] = $emailOrNumber;
        }

        // Handle referral code from session (set by referral link)
        if (session()->has('referrer_id')) {
            $referrerId = session()->get('referrer_id');
            $referrer = User::find($referrerId);
            
            // Only set referrer if valid and not self-referral
            if ($referrer && $referrer->id) {
                $userData['referrer_id'] = $referrer->id;
            }
            
            // Clear the session after use
            session()->forget('referrer_id');
        }

        return User::create($userData);
    }

    protected function generateUsername(string $name): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'user';
        }

        do {
            $suffix = (string) random_int(1000000000, 9999999999);
            $username = $base . '-' . $suffix;
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
