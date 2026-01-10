<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch the application locale
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            abort(400, 'Invalid locale');
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);

        // Redirect back to previous page
        return redirect()->back();
    }
}
