<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Dashboard', [
            'products' => Inertia::scroll(fn () => Product::latest()->paginate(9)),
        ]);
    }
}
