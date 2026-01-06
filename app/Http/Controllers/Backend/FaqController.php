<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage faqs,admin');
    }

    public function index()
    {
        $faqs = Faq::ordered()->get();
        return view('backend.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('backend.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $maxOrder = Faq::max('order') ?? 0;
        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully!');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('backend.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully!');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully!');
    }
}
