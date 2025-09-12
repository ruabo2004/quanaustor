@extends('admin.layouts.admin')

@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm')
@section('breadcrumb', 'Quản lý / Sản phẩm / Thêm mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('admin.products') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-900">Thêm sản phẩm mới</h2>
            </div>
            
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                        <select id="category_id" name="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                                required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Giá (VNĐ)</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                               required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500" 
                               required>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Chi tiết sản phẩm -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Chi tiết sản phẩm</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="measurements" class="block text-sm font-medium text-gray-700 mb-2">Số đo</label>
                            <input type="text" id="measurements" name="measurements" value="{{ old('measurements') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="length" class="block text-sm font-medium text-gray-700 mb-2">Chiều dài</label>
                            <input type="text" id="length" name="length" value="{{ old('length') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="garment_size" class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                            <input type="text" id="garment_size" name="garment_size" value="{{ old('garment_size') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="style" class="block text-sm font-medium text-gray-700 mb-2">Phong cách</label>
                            <input type="text" id="style" name="style" value="{{ old('style') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="fit" class="block text-sm font-medium text-gray-700 mb-2">Kiểu dáng</label>
                            <input type="text" id="fit" name="fit" value="{{ old('fit') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="material" class="block text-sm font-medium text-gray-700 mb-2">Chất liệu</label>
                            <input type="text" id="material" name="material" value="{{ old('material') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="leg_style" class="block text-sm font-medium text-gray-700 mb-2">Kiểu chân</label>
                        <input type="text" id="leg_style" name="leg_style" value="{{ old('leg_style') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.products') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-primary text-white px-4 py-2 rounded-md">
                        <i class="fas fa-save mr-2"></i>
                        Tạo sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
