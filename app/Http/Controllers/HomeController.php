<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Mostrar la página de inicio
     */
    public function index(): View
    {
        $user = auth()->user();
        
        return view('home', compact('user'));
    }
}
