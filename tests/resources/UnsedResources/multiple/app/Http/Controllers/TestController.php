<?php

namespace App\Http\Controllers;

class TestController extends Controller
{

    public function index()
    {
        __('test.test_key');
        return response();
    }
}
