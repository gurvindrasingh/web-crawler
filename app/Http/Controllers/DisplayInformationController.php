<?php

namespace App\Http\Controllers;

class DisplayInformationController extends Controller
{
    /**
     * Display the crawled information.
     *
     * @return view
     */
    public function __invoke()
    {
        return view('display-information');
    }
}
