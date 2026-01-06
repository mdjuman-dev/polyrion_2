<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage contact,admin');
    }

    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return view('backend.contact.index', compact('messages'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        return view('backend.contact.show', compact('message'));
    }

    public function settings()
    {
        $supportEmail = GlobalSetting::getValue('support_email', '');
        $supportPhone = GlobalSetting::getValue('support_phone', '');
        $supportDescription = GlobalSetting::getValue('support_description', '');
        
        return view('backend.contact.settings', compact('supportEmail', 'supportPhone', 'supportDescription'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:50',
            'support_description' => 'nullable|string',
        ]);

        GlobalSetting::setValue('support_email', $request->support_email);
        GlobalSetting::setValue('support_phone', $request->support_phone);
        GlobalSetting::setValue('support_description', $request->support_description);

        return redirect()->route('admin.contact.settings')->with('success', 'Support settings updated successfully!');
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contact.index')->with('success', 'Message deleted successfully!');
    }
}
