@extends('layouts.admin')

@section('title', 'Tambah Pengumuman Baru — Admin FireAware')
@section('page_title', 'Tambah Pengumuman')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.announcements.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Tambah Pengumuman / Rilis Resmi</h1>
        <p class="text-secondary-soft mb-0">
            Terbitkan warta resmi peringatan cuaca, status kewaspadaan, atau instruksi darurat karhutla.
        </p>
    </div>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        @include('admin.announcements._form', ['isEdit' => false])
    </form>
@endsection
