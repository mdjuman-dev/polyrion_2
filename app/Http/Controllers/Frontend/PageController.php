<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        $page = Page::getByKey('privacy_policy');
        return view('frontend.pages.show', [
            'page' => $page,
            'title' => $page ? $page->title : 'Privacy Policy',
            'content' => $page ? $page->content : '',
        ]);
    }

    public function termsOfUse()
    {
        $page = Page::getByKey('terms_of_use');
        return view('frontend.pages.show', [
            'page' => $page,
            'title' => $page ? $page->title : 'Terms of Use',
            'content' => $page ? $page->content : '',
        ]);
    }
}
