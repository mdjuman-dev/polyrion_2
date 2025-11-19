<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Sanitize and validate input
        $emailOrNumber = trim($input['email_or_number'] ?? '');
        $name = trim($input['name'] ?? '');

        // Early validation for empty required fields
        if (empty($emailOrNumber)) {
            Validator::make([], [
                'email_or_number' => 'required',
            ])->validate();
        }

        // Determine if input is email or phone number
        $isEmail = filter_var($emailOrNumber, FILTER_VALIDATE_EMAIL);

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email_or_number' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ];

        $validator = Validator::make($input, $rules);

        // Validate based on input type
        $validator->after(function ($validator) use ($emailOrNumber, $isEmail) {
            if (empty($emailOrNumber)) {
                $validator->errors()->add('email_or_number', 'Email বা Phone Number অবশ্যই দিতে হবে।');
                return;
            }

            if ($isEmail) {
                // Validate as email
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
                // Validate as phone number
                // Normalize phone number for validation
                $normalizedNumber = preg_replace('/[\s\-\(\)\+]/', '', $emailOrNumber);

                // Validate phone number format and length
                if (strlen($normalizedNumber) < 10 || strlen($normalizedNumber) > 20) {
                    $validator->errors()->add('email_or_number', 'Phone Number must be between 10 and 20 digits.');
                    return;
                }

                // Check uniqueness
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

        // Prepare user data
        $userData = [
            'name' => $name,
            'password' => $input['password'],
        ];

        // Save based on input type
        if ($isEmail) {
            $userData['email'] = strtolower($emailOrNumber); // Normalize email to lowercase
        } else {
            $userData['number'] = $emailOrNumber;
        }

        return User::create($userData);
    }
}
