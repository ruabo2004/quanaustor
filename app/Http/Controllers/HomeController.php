<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Yêu cầu auth cho home page (handled by route middleware)
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Product::with(['category', 'sizes'])->latest()->get();
        $categories = \App\Models\Category::with('products')->get();
        
        // Tính số lượng thực tế cho từng danh mục
        foreach ($categories as $category) {
            if ($category->name === 'Quần âu nam') {
                $category->actual_product_count = Product::where('name', 'like', '%nam%')->count();
            } elseif ($category->name === 'Quần âu nữ') {
                $category->actual_product_count = Product::where('name', 'like', '%nữ%')->count();
            } else {
                $category->actual_product_count = $category->products->count();
            }
        }
        
        return view('home', compact('products', 'categories'));
    }
}
