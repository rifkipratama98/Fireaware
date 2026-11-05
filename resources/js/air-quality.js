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
const BORDER = '#DCE4DC';
const TEXT_SECONDARY = '#516257';

function getIspuLabel(value) {
    if (value <= 50) return 'Baik';
    if (value <= 100) return 'Sedang';
    if (value <= 200) return 'Tidak Sehat';
    if (value <= 300) return 'Sangat Tidak Sehat';
    return 'Berbahaya';
}

function getIspuColor(value) {
    if (value <= 50) return '#2E7D32';
    if (value <= 100) return '#214D3A';
    if (value <= 200) return '#D99227';
    if (value <= 300) return '#B94035';
    return '#1F2937';
}

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
    const pointColors = values.map((v) => getIspuColor(v));

    const ctx = canvas.getContext('2d');
    let gradient = 'rgba(33, 77, 58, 0.1)';
    if (ctx) {
        const h = canvas.height || 260;
        const g = ctx.createLinearGradient(0, 0, 0, h);
        g.addColorStop(0, 'rgba(33, 77, 58, 0.22)');
        g.addColorStop(1, 'rgba(33, 77, 58, 0.01)');
        gradient = g;
    }

    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'PM2.5 (µg/m³)',
                    data: values,
                    borderColor: PRIMARY,
                    backgroundColor: gradient,
                    borderWidth: 2.4,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: pointColors,
                    pointBorderWidth: 2.2,
                    pointHoverBackgroundColor: pointColors,
                    pointHoverBorderColor: '#FFFFFF',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: prefersReducedMotion ? false : { duration: 450 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#FFFFFF',
                    titleColor: '#1C2822',
                    bodyColor: '#516257',
                    borderColor: BORDER,
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 4,
                    usePointStyle: true,
                    displayColors: false,
                    callbacks: {
                        title: (items) => {
                            if (!items.length) return '';
                            return `Waktu: ${items[0].label} WITA`;
                        },
                        label: (ctx) => {
                            const val = ctx.parsed.y;
                            const cat = getIspuLabel(val);
                            return `Konsentrasi PM2.5: ${val} µg/m³ (${cat})`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: TEXT_SECONDARY,
                        font: { size: 11, family: 'Inter, sans-serif' },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 6,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(220, 228, 220, 0.75)',
                    },
                    ticks: {
                        color: TEXT_SECONDARY,
                        font: { size: 11, family: 'Inter, sans-serif' },
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

