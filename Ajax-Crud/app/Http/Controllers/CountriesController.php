<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function index()
    {
        //COUNTRİES LİST

        return view('countries-list');
    }
}
