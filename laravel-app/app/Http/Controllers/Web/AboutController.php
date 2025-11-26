<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\UiSettings;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __invoke(Request $request)
    {
        $preferences = UiSettings::preferences($request);
        $strings = UiSettings::translations('home');

        return view('about', [
            'preferences' => $preferences,
            'strings' => $strings,
        ]);
    }
}
