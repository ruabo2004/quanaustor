@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chỉnh sửa sản phẩm</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Use PUT method for updates --}}

        <div class="form-group">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="form-group">
            <label for="description">Mô tả:</label>
            <textarea class="form-control" id="description" name="description">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">Giá:</label>
            <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="measurements">Số đo:</label>
            <input type="text" class="form-control" id="measurements" name="measurements" value="{{ old('measurements', $product->measurements) }}">
        </div>
        <div class="form-group">
            <label for="length">Độ dài:</label>
            <input type="text" class="form-control" id="length" name="length" value="{{ old('length', $product->length) }}">
        </div>
        <div class="form-group">
            <label for="garment_size">Kích thước quần áo:</label>
            <input type="text" class="form-control" id="garment_size" name="garment_size" value="{{ old('garment_size', $product->garment_size) }}">
        </div>
        <div class="form-group">
            <label for="style">Kiểu dáng:</label>
            <input type="text" class="form-control" id="style" name="style" value="{{ old('style', $product->style) }}">
        </div>
        <div class="form-group">
            <label for="fit">Form quần:</label>
            <input type="text" class="form-control" id="fit" name="fit" value="{{ old('fit', $product->fit) }}">
        </div>
        <div class="form-group">
            <label for="material">Chất liệu:</label>
            <input type="text" class="form-control" id="material" name="material" value="{{ old('material', $product->material) }}">
        </div>
        <div class="form-group">
            <label for="leg_style">Ống quần:</label>
            <input type="text" class="form-control" id="leg_style" name="leg_style" value="{{ old('leg_style', $product->leg_style) }}">
        </div>
        <div class="form-group">
            <label for="image">Ảnh sản phẩm:</label>
            <input type="file" class="form-control-file" id="image" name="image">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail mt-2" style="max-width: 150px;">
            @else
                <p class="mt-2">Chưa có ảnh</p>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
    </form>
</div>
@endsection
