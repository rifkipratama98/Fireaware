@extends('layouts.admin')

@section('title', 'Tambah Panduan Keselamatan Baru — Admin FireAware')
@section('page_title', 'Tambah Panduan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.guides.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Panduan
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Tambah Panduan Keselamatan Baru</h1>
        <p class="text-secondary-soft mb-0">
            Tulis artikel panduan langkah aman mitigasi kabut asap dan kewaspadaan kebakaran hutan.
        </p>
    </div>

    <form action="{{ route('admin.guides.store') }}" method="POST">
        @csrf
        @include('admin.guides._form', ['isEdit' => false])
    </form>
@endsection
