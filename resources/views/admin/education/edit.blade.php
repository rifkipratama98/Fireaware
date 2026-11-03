@extends('layouts.admin')

@section('title', 'Edit Artikel: ' . $article->title . ' — Admin FireAware')
@section('page_title', 'Edit Edukasi')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.education.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Artikel Edukasi
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Edit Artikel Edukasi</h1>
        <p class="text-secondary-soft mb-0">
            Perbarui konten literasi, gambar ilustrasi, atau status publikasi artikel.
        </p>
    </div>

    <form action="{{ route('admin.education.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.education._form', ['isEdit' => true])
    </form>
@endsection
