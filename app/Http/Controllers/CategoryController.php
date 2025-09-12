<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        // Logic đặc biệt cho danh mục "Quần âu nam" và "Quần âu nữ"
        if ($category->name === 'Quần âu nam') {
            // Hiển thị tất cả sản phẩm có từ "nam" trong tên
            $products = \App\Models\Product::with('sizes')
                ->where('name', 'like', '%nam%')
                ->get();
        } elseif ($category->name === 'Quần âu nữ') {
            // Hiển thị tất cả sản phẩm có từ "nữ" trong tên
            $products = \App\Models\Product::with('sizes')
                ->where('name', 'like', '%nữ%')
                ->get();
        } else {
            // Các danh mục khác hiển thị theo category_id như bình thường
            $products = $category->products()->with('sizes')->get();
        }
        
        // Load danh mục với số lượng sản phẩm để debug
        $category->load('products');
        
        return view('categories.show', compact('category', 'products'));
    }
}
