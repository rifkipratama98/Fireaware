@extends('layouts.admin')

@section('title', 'Edit Panduan: ' . $guide->title . ' — Admin FireAware')
@section('page_title', 'Edit Panduan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.guides.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Panduan
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Edit Panduan Keselamatan</h1>
        <p class="text-secondary-soft mb-0">
            Perbarui konten, rujukan sumber, atau status publikasi artikel edukasi keselamatan.
        </p>
    </div>

    <form action="{{ route('admin.guides.update', $guide) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.guides._form', ['isEdit' => true])
    </form>
@endsection
