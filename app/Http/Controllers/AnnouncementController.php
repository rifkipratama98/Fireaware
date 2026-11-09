<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Halaman publik: Daftar Informasi Kewaspadaan & Peringatan Dini Karhutla.
     *
     * Berisi aliran informasi resmi berkredibilitas tinggi,
     * filter wilayah provinsi, level kewaspadaan, kategori, dan pencarian.
     */
    public function index(Request $request): Response
    {
        $provinceSlug = trim((string) $request->query('provinsi', ''));
        $levelParam = $request->query('level');
        $categoryParam = trim((string) $request->query('kategori', ''));
        $search = trim((string) $request->query('q', ''));
        $sort = trim((string) $request->query('sort', 'terbaru'));

        $provinces = Province::query()->orderBy('name')->get(['id', 'name', 'slug', 'code']);
        $activeProvince = $provinceSlug !== '' ? $provinces->firstWhere('slug', $provinceSlug) : null;

        // Resolusi level: langsung dari param level atau dari alias kategori
        $levelInt = null;
        if (is_numeric($levelParam) && array_key_exists((int) $levelParam, Announcement::LEVELS)) {
            $levelInt = (int) $levelParam;
        } elseif ($categoryParam === 'darurat') {
            $levelInt = Announcement::LEVEL_DARURAT;
        } elseif ($categoryParam === 'waspada') {
            $levelInt = Announcement::LEVEL_WASPADA;
        } elseif ($categoryParam === 'info') {
            $levelInt = Announcement::LEVEL_INFO;
        }

        $query = Announcement::query()
            ->published()
            ->with('province')
            ->when($activeProvince, fn ($q) => $q->where('province_id', $activeProvince->id))
            ->level($levelInt)
            ->search($search);

        if ($sort === 'terlama') {
            $query->orderBy('published_at', 'asc')->orderBy('id', 'asc');
        } elseif ($sort === 'urgensi') {
            $query->orderByDesc('level')->orderByDesc('published_at');
        } else {
            $query->orderByDesc('published_at')->orderByDesc('id');
        }

        $paginated = $query->paginate(9)->withQueryString();

        // Transform data announcement untuk frontend
        $transformedAnnouncements = $paginated->through(function (Announcement $item) {
            return $this->formatAnnouncement($item);
        });

        // Hitung statistik seluruh rilis resmi yang berstatus published
        $publishedQuery = Announcement::query()->published();
        $stats = [
            'total' => (clone $publishedQuery)->count(),
            'info' => (clone $publishedQuery)->where('level', Announcement::LEVEL_INFO)->count(),
            'waspada' => (clone $publishedQuery)->where('level', Announcement::LEVEL_WASPADA)->count(),
            'darurat' => (clone $publishedQuery)->where('level', Announcement::LEVEL_DARURAT)->count(),
            'last_updated_at' => (clone $publishedQuery)->max('published_at'),
        ];

        // Format waktu pembaruan terakhir
        $lastUpdatedFormatted = null;
        if ($stats['last_updated_at']) {
            $dt = Carbon::parse($stats['last_updated_at'])->setTimezone('Asia/Makassar');
            $lastUpdatedFormatted = $dt->translatedFormat('d M Y, H:i') . ' WITA';
        }

        // Peringatan unggulan (Featured Alert): ambil yang paling darurat atau paling baru sesuai filter aktif
        $featuredItem = Announcement::query()
            ->published()
            ->with('province')
            ->when($activeProvince, fn ($q) => $q->where('province_id', $activeProvince->id))
            ->level($levelInt)
            ->search($search)
            ->orderByDesc('level')
            ->orderByDesc('published_at')
            ->first();

        $featured = $featuredItem ? $this->formatAnnouncement($featuredItem) : null;

        // Panel Status Kewaspadaan per Provinsi (5 Provinsi Kalimantan)
        $regionalStatus = $provinces->map(function ($prov) {
            $latest = Announcement::query()
                ->published()
                ->where('province_id', $prov->id)
                ->orderByDesc('published_at')
                ->first();

            $highestLevel = Announcement::query()
                ->published()
                ->where('province_id', $prov->id)
                ->max('level');

            $statusLabel = 'Kondusif';
            $statusBadge = 'bg-[#2A9D8F]/15 text-[#2A9D8F] border-[#2A9D8F]/30';
            $statusDot = 'bg-[#2A9D8F]';

            if ($highestLevel === Announcement::LEVEL_DARURAT) {
                $statusLabel = 'Siaga Darurat';
                $statusBadge = 'bg-[#E63946]/15 text-[#E63946] border-[#E63946]/30';
                $statusDot = 'bg-[#E63946]';
            } elseif ($highestLevel === Announcement::LEVEL_WASPADA) {
                $statusLabel = 'Waspada';
                $statusBadge = 'bg-[#F4A261]/15 text-[#F4A261] border-[#F4A261]/30';
                $statusDot = 'bg-[#F4A261]';
            } elseif ($highestLevel === Announcement::LEVEL_INFO) {
                $statusLabel = 'Informasi Aktif';
                $statusBadge = 'bg-[#2563EB]/15 text-[#2563EB] border-[#2563EB]/30';
                $statusDot = 'bg-[#2563EB]';
            }

            return [
                'id' => $prov->id,
                'name' => $prov->name,
                'slug' => $prov->slug,
                'code' => $prov->code,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'status_dot' => $statusDot,
                'latest_updated_at' => $latest?->published_at
                    ? Carbon::parse($latest->published_at)->setTimezone('Asia/Makassar')->translatedFormat('d M, H:i') . ' WITA'
                    : null,
            ];
        });

        // Kategori navigasi segmented
        $categories = [
            [
                'key' => 'semua',
                'label' => 'Semua Informasi',
                'count' => $stats['total'],
            ],
            [
                'key' => 'darurat',
                'label' => 'Siaga Darurat',
                'count' => $stats['darurat'],
            ],
            [
                'key' => 'waspada',
                'label' => 'Waspada',
                'count' => $stats['waspada'],
            ],
            [
                'key' => 'info',
                'label' => 'Informasi & Imbauan',
                'count' => $stats['info'],
            ],
        ];

        return Inertia::render('Public/Announcements/Index', [
            'announcements' => $transformedAnnouncements,
            'featured' => $featured,
            'provinces' => $provinces,
            'regionalStatus' => $regionalStatus,
            'activeProvince' => $activeProvince,
            'activeLevel' => $levelInt,
            'activeCategory' => $categoryParam ?: ($levelInt ? match ($levelInt) {
                Announcement::LEVEL_DARURAT => 'darurat',
                Announcement::LEVEL_WASPADA => 'waspada',
                Announcement::LEVEL_INFO => 'info',
                default => 'semua',
            } : 'semua'),
            'search' => $search,
            'sort' => $sort,
            'stats' => $stats,
            'lastUpdatedFormatted' => $lastUpdatedFormatted,
            'categories' => $categories,
            'levels' => Announcement::LEVELS,
        ]);
    }

    /**
     * Halaman publik: Detail Informasi Kewaspadaan Resmi.
     */
    public function show(string $slug): Response
    {
        $announcement = Announcement::query()
            ->published()
            ->with('province')
            ->where('slug', $slug)
            ->firstOrFail();

        $recentAnnouncements = Announcement::query()
            ->published()
            ->with('province')
            ->where('id', '!=', $announcement->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Announcement $item) => $this->formatAnnouncement($item));

        return Inertia::render('Public/Announcements/Show', [
            'announcement' => $this->formatAnnouncement($announcement, includeBody: true),
            'recentAnnouncements' => $recentAnnouncements,
            'back_label' => 'Kembali ke Daftar Pengumuman',
        ]);
    }

    /**
     * Memformat objek Announcement dengan metadata terstandarisasi untuk frontend.
     */
    private function formatAnnouncement(Announcement $item, bool $includeBody = false): array
    {
        $publishedAt = $item->published_at ?? $item->created_at ?? now();
        $carbonDate = Carbon::parse($publishedAt)->setTimezone('Asia/Makassar');

        $levelMap = [
            Announcement::LEVEL_DARURAT => [
                'label' => 'Siaga Darurat',
                'color' => '#E63946',
                'badge_class' => 'bg-[#E63946]/15 text-[#E63946] border-[#E63946]/30',
                'dot_class' => 'bg-[#E63946]',
                'icon' => 'ShieldAlert',
            ],
            Announcement::LEVEL_WASPADA => [
                'label' => 'Waspada',
                'color' => '#F4A261',
                'badge_class' => 'bg-[#F4A261]/15 text-[#F4A261] border-[#F4A261]/30',
                'dot_class' => 'bg-[#F4A261]',
                'icon' => 'AlertTriangle',
            ],
            Announcement::LEVEL_INFO => [
                'label' => 'Informasi',
                'color' => '#2A9D8F',
                'badge_class' => 'bg-[#2A9D8F]/15 text-[#2A9D8F] border-[#2A9D8F]/30',
                'dot_class' => 'bg-[#2A9D8F]',
                'icon' => 'Info',
            ],
        ];

        $levelMeta = $levelMap[$item->level] ?? $levelMap[Announcement::LEVEL_INFO];

        // Pilih aset fotografi yang sesuai konteks artikel
        $textToCheck = Str::lower($item->title . ' ' . $item->summary . ' ' . $item->body);
        $imageUrl = '/images/forest-canopy.jpg';
        $imageCaption = 'Kondisi kanopi hutan tropis Kalimantan';

        if (Str::contains($textToCheck, ['gambut', 'rawa', 'sekat kanal', 'sumur bor'])) {
            $imageUrl = '/images/peatland-gambut.jpg';
            $imageCaption = 'Lahan gambut rawan kekeringan di Kalimantan';
        } elseif (Str::contains($textToCheck, ['padam', 'manggala agni', 'water bombing', 'patroli', 'pemadaman', 'api'])) {
            $imageUrl = '/images/wildfire-firefighter.jpg';
            $imageCaption = 'Petugas Manggala Agni dan posko siaga pemadaman';
        } elseif (Str::contains($textToCheck, ['asap', 'hujan', 'kering', 'hth', 'cuaca', 'bmkg', 'udara', 'klimatologi'])) {
            $imageUrl = '/images/smoke-haze.jpg';
            $imageCaption = 'Kondisi kabut asap dan cuaca kering di Kalimantan';
        } elseif (Str::contains($textToCheck, ['sosialisasi', 'mpa', 'masyarakat', 'pelatihan', 'relawan', 'warga'])) {
            $imageUrl = '/images/conservation-action.jpg';
            $imageCaption = 'Partisipasi Masyarakat Peduli Api dan pencegahan';
        }

        // Estimasi waktu baca (~200 kata/menit)
        $wordCount = str_word_count(strip_tags((string) ($item->body ?: $item->summary ?: '')));
        $readingMinutes = max(1, (int) ceil($wordCount / 200));

        $data = [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'summary' => $item->summary ?: Str::limit(strip_tags((string) $item->body), 180),
            'level' => (int) $item->level,
            'level_label' => $levelMeta['label'],
            'level_color' => $levelMeta['color'],
            'level_badge' => $levelMeta['badge_class'],
            'level_dot' => $levelMeta['dot_class'],
            'level_icon' => $levelMeta['icon'],
            'source_name' => $item->source_name ?: 'Pemerintah Daerah / Otoritas Kebencanaan',
            'source_url' => $item->source_url,
            'is_demo' => (bool) $item->is_demo,
            'province' => $item->province ? [
                'id' => $item->province->id,
                'name' => $item->province->name,
                'slug' => $item->province->slug,
                'code' => $item->province->code,
            ] : null,
            'image_url' => $imageUrl,
            'image_caption' => $imageCaption,
            'reading_time' => $readingMinutes,
            'published_at' => $carbonDate->toIso8601String(),
            'published_date_formatted' => $carbonDate->translatedFormat('d F Y'),
            'published_time_formatted' => $carbonDate->translatedFormat('H:i') . ' WITA',
            'published_relative' => $carbonDate->diffForHumans(),
        ];

        if ($includeBody) {
            $data['body'] = $item->body;
            $data['paragraphs'] = $item->bodyParagraphs();
        }

        return $data;
    }
}
