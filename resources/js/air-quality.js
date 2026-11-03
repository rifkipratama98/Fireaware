// FireAware — grafik tren PM2.5 (Chart.js)
// Script ini hanya dimuat bila backend menyatakan riwayat cukup untuk divisualisasikan.
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    TimeScale,
    CategoryScale,
    Filler,
    Tooltip,
} from 'chart.js';

Chart.register(LineController, LineElement, PointElement, LinearScale, TimeScale, CategoryScale, Filler, Tooltip);

const PRIMARY = '#214D3A';
const BORDER = '#DCE3DC';
const TEXT_SECONDARY = '#68736B';

function initAirQualityChart() {
    const canvas = document.getElementById('aqTrendChart');
    const dataEl = document.getElementById('aq-history-data');

    if (!canvas || !dataEl) {
        return;
    }

    let points = [];
    try {
        points = JSON.parse(dataEl.textContent || '[]');
    } catch {
        return;
    }

    if (!Array.isArray(points) || points.length < 3) {
        return;
    }

    const labels = points.map((p) =>
        new Date(p.t).toLocaleString('id-ID', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
        })
    );
    const values = points.map((p) => p.v);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'PM2.5 (µg/m³)',
                    data: values,
                    borderColor: PRIMARY,
                    backgroundColor: 'rgba(33, 77, 58, 0.08)',
                    borderWidth: 2,
                    pointRadius: 2.5,
                    pointHoverRadius: 4,
                    pointBackgroundColor: PRIMARY,
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#202923',
                    bodyColor: '#202923',
                    borderColor: BORDER,
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y} µg/m³`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: TEXT_SECONDARY,
                        font: { size: 11 },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 6,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(220, 227, 220, 0.7)' },
                    ticks: {
                        color: TEXT_SECONDARY,
                        font: { size: 11 },
                        callback: (v) => `${v}`,
                    },
                },
            },
        },
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAirQualityChart);
} else {
    initAirQualityChart();
}
