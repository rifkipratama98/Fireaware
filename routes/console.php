<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Penjadwalan Sinkronisasi Data Eksternal FireAware Kalimantan
|--------------------------------------------------------------------------
|
| Dokumentasi konfigurasi cron server:
| Tambahkan entri berikut pada crontab server produksi (crontab -e):
| * * * * * cd /path-to-fireaware-project && php artisan schedule:run >> /dev/null 2>&1
|
| Pada Windows Task Scheduler:
| Trigger: Every 1 minute
| Action: Start a program -> php.exe artisan schedule:run
|
*/

// 1. NASA FIRMS (Titik Panas Satelit VIIRS/MODIS) — Diperbarui tiap 3 jam
Schedule::command('fireaware:sync firms')
    ->everyThreeHours()
    ->withoutOverlapping(60)
    ->runInBackground()
    ->description('Sinkronisasi data titik panas satelit NASA FIRMS untuk Pulau Kalimantan');

// 2. BMKG Karhutla (Peringatan Cuaca Ekstrem, Indeks FWI & HTH) — Diperbarui tiap 4 jam
Schedule::command('fireaware:sync bmkg')
    ->everyFourHours()
    ->withoutOverlapping(60)
    ->runInBackground()
    ->description('Sinkronisasi rilis peringatan dini dan status cuaca BMKG Karhutla');

// 3. Kualitas Udara (OpenAQ / Sensor ISPU Daerah) — Diperbarui tiap jam
Schedule::command('fireaware:sync airquality')
    ->hourly()
    ->withoutOverlapping(30)
    ->runInBackground()
    ->description('Sinkronisasi data pengukuran PM2.5 stasiun kualitas udara');

