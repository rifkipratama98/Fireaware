<?php

namespace App\Services\Information;

use App\Models\InformationItem;
use App\Models\InformationSource;
use App\Models\Province;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Sumber data halaman Informasi Kewaspadaan.
 *
 * Semua data berasal dari basis data (dikelola admin) atau dari ringkasan yang
 * disimpan beserta identitas sumbernya. Service ini tidak pernah:
 * - menggabungkan beberapa sumber menjadi satu informasi;
 * - mengarang status yang tidak diberikan sumber;
 * - menyimpulkan bahwa kebakaran pasti terjadi.
 */
class InformationService
{
    /** Jumlah informasi per halaman. */
    public const PER_PAGE = 8;

    /**
     * Daftar provinsi untuk filter (urut nama).
     *
     * @return Collection<int, Province>
     */
    public function provinces(): Collection
    {
        return Province::query()->orderBy('name')->get(['id', 'name', 'slug']);
    }

    /**
     * Sumber yang punya informasi terbit — hanya sumber ini yang ditawarkan
     * sebagai filter agar pilihan tidak pernah kosong.
     *
     * @return Collection<int, InformationSource>
     */
    public function sources(): Collection
    {
        return InformationSource::query()
            ->whereHas('items', fn (Builder $q) => $q->published())
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'kind']);
    }

    /**
     * Daftar informasi terbit sesuai filter.
     *
     * @param  array{wilayah?:?string,status?:?string,sumber?:?string,q?:?string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 50));

        return $this->query($filters)
            ->with(['source:id,name,slug,kind,organization,homepage_url,verified_at', 'provinces:id,name,slug'])
            ->latestFirst()
            ->paginate($perPage)
            // Filter tetap terbawa saat berpindah halaman
            ->withQueryString();
    }

    /**
     * Query dasar daftar informasi (belum diurutkan/dipaginasi).
     *
     * @param  array{wilayah?:?string,status?:?string,sumber?:?string,q?:?string}  $filters
     * @return Builder<InformationItem>
     */
    public function query(array $filters = []): Builder
    {
        $query = InformationItem::query()->published();

        if (filled($filters['wilayah'] ?? null)) {
            $query->whereHas('provinces', fn (Builder $q) => $q->where('provinces.slug', $filters['wilayah']));
        }

        if (filled($filters['sumber'] ?? null)) {
            $query->whereHas('source', fn (Builder $q) => $q->where('information_sources.slug', $filters['sumber']));
        }

        if (filled($filters['status'] ?? null)) {
            $status = $filters['status'];

            if ($status === 'with_status') {
                $query->whereNotNull('status_label');
            } elseif (InformationStatus::isFilterable($status)) {
                $query->where('status_key', $status);
            }
        }

        if (filled($filters['q'] ?? null)) {
            $keyword = '%' . $this->escapeLike((string) $filters['q']) . '%';

            $query->where(function (Builder $q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('summary', 'like', $keyword)
                    ->orWhere('body', 'like', $keyword);
            });
        }

        return $query;
    }

    /**
     * Detail satu informasi yang sudah terbit.
     * Mengembalikan null bila slug tidak ada / belum terbit (404 di controller).
     */
    public function findPublished(string $slug): ?InformationItem
    {
        return InformationItem::query()
            ->published()
            ->where('slug', $slug)
            ->with(['source', 'provinces:id,name,slug'])
            ->first();
    }

    /** Netralkan wildcard LIKE pada input pengguna. */
    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
