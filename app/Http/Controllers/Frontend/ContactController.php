<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $supportEmail = GlobalSetting::getValue('support_email', '');
        $supportPhone = GlobalSetting::getValue('support_phone', '');
        $supportDescription = GlobalSetting::getValue('support_description', '');
        
        return view('frontend.contact.index', compact('supportEmail', 'supportPhone', 'supportDescription'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully! We will get back to you soon.',
        ]);
    }
}
