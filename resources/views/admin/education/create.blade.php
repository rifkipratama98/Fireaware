@extends('layouts.admin')

@section('title', 'Tambah Artikel Edukasi Baru — Admin FireAware')
@section('page_title', 'Tambah Edukasi')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.education.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Artikel Edukasi
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Tambah Artikel Edukasi Karhutla</h1>
        <p class="text-secondary-soft mb-0">
            Tulis materi literasi sains, kebakaran gambut, dan mitigasi lingkungan untuk masyarakat.
        </p>
    </div>

    <form action="{{ route('admin.education.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.education._form', ['isEdit' => false])
    </form>
@endsection
