@extends('layouts.admin')

@section('title', 'Edit Pengumuman: ' . $announcement->title . ' — Admin FireAware')
@section('page_title', 'Edit Pengumuman')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.announcements.index') }}" class="text-decoration-none small text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengumuman
        </a>
        <h1 class="h4 mt-2 mb-1 fw-semibold">Edit Pengumuman Resmi</h1>
        <p class="text-secondary-soft mb-0">
            Perbarui data warta rilis, status tingkat kewaspadaan, atau wilayah terkait.
        </p>
    </div>

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.announcements._form', ['isEdit' => true])
    </form>
@endsection
