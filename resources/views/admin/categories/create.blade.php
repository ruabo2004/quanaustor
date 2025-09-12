@extends('admin.layouts.admin')

@section('title', 'Thêm danh mục')
@section('page-title', 'Thêm danh mục')
@section('breadcrumb', 'Quản lý / Danh mục / Thêm mới')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('admin.categories') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-900">Thêm danh mục mới</h2>
            </div>
            
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.categories') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-primary text-white px-4 py-2 rounded-md">
                        <i class="fas fa-save mr-2"></i>
                        Tạo danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
