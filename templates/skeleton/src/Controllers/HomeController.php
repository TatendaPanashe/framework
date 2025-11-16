<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        return view('home', [
            'title' => 'Welcome to Kodomo Framework',
            'message' => 'Build amazing web applications with ease!'
        ]);
    }

    public function about()
    {
        return view('about', [
            'title' => 'About Kodomo Framework'
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'title' => 'Contact Us'
        ]);
    }
}