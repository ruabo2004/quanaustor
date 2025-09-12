@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Xác thực địa chỉ Email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Một liên kết xác thực mới đã được gửi đến địa chỉ email của bạn.
                        </div>
                    @endif

                    <p>Trước khi tiếp tục, vui lòng kiểm tra email để lấy liên kết xác thực.</p>
                    <p>Nếu bạn không nhận được email, 
                    <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">nhấn vào đây để gửi lại</button>.
                    </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

