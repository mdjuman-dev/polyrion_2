<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage pages,admin');
    }

    public function edit($key)
    {
        $page = Page::getByKey($key);
        if (!$page) {
            $page = Page::create([
                'page_key' => $key,
                'title' => $key === 'privacy_policy' ? 'Privacy Policy' : 'Terms of Use',
                'content' => '',
            ]);
        }
        return view('backend.pages.edit', compact('page'));
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $page = Page::getByKey($key);
        if (!$page) {
            $page = new Page();
            $page->page_key = $key;
        }

        $page->title = $request->title;
        $page->content = $request->content;
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully!',
        ]);
    }
}
