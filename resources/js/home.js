// FireAware — interaksi halaman Beranda (vanilla JS, tanpa dependency)

function initHomePage() {
    const select = document.querySelector('[data-js="province-select"]');
    const mapEl = document.getElementById('hotspotMap');
    const errorBox = document.querySelector('[data-js="summary-error"]');

    if (!select && !mapEl) {
        return;
    }

    // --- Data titik panas dari Blade (JSON, dibangun server-side)
    const mapDataEl = document.getElementById('map-hotspots-data');
    let points = [];
    if (mapDataEl) {
        try {
            points = JSON.parse(mapDataEl.textContent || '[]');
        } catch {
            points = [];
        }
    }

    // --- Peta sederhana: gambar titik panas pada kanvas SVG Kalimantan
    if (mapEl && points.length > 0) {
        renderSimpleMap(mapEl, points);
    }

    // --- Pemilih wilayah: simpan pilihan lalu muat ulang halaman
    if (select) {
        select.addEventListener('change', () => {
            const slug = select.value;
            const url = new URL(window.location.href);
            if (slug) {
                url.searchParams.set('provinsi', slug);
            } else {
                url.searchParams.delete('provinsi');
            }
            window.location.assign(url.toString());
        });
    }
}

/**
 * Render peta persebaran sederhana memakai proyeksi equirectangular
 * pada kotak SVG dengan latar Mist Green. Tanpa library eksternal.
 */
function renderSimpleMap(container, points) {
    const BOUNDS = { minLat: -4.4, maxLat: 4.6, minLng: 108.8, maxLng: 119.2 };
    const W = 800;
    const H = 460;

    const toX = (lng) => ((lng - BOUNDS.minLng) / (BOUNDS.maxLng - BOUNDS.minLng)) * W;
    const toY = (lat) => H - ((lat - BOUNDS.minLat) / (BOUNDS.maxLat - BOUNDS.minLat)) * H;

    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('class', 'w-100 h-100');
    svg.setAttribute('role', 'presentation');

    // Latar halus: grid garis tipis sebagai acuan ruang
    for (let lat = Math.ceil(BOUNDS.minLat); lat <= BOUNDS.maxLat; lat += 2) {
        const line = document.createElementNS(ns, 'line');
        line.setAttribute('x1', 0);
        line.setAttribute('x2', W);
        line.setAttribute('y1', toY(lat));
        line.setAttribute('y2', toY(lat));
        line.setAttribute('stroke', 'rgba(33, 77, 58, 0.08)');
        line.setAttribute('stroke-width', 1);
        svg.appendChild(line);
    }
    for (let lng = Math.ceil(BOUNDS.minLng); lng <= BOUNDS.maxLng; lng += 2) {
        const line = document.createElementNS(ns, 'line');
        line.setAttribute('y1', 0);
        line.setAttribute('y2', H);
        line.setAttribute('x1', toX(lng));
        line.setAttribute('x2', toX(lng));
        line.setAttribute('stroke', 'rgba(33, 58, 45, 0.08)');
        line.setAttribute('stroke-width', 1);
        svg.appendChild(line);
    }

    // Titik hotspot
    for (const p of points) {
        const circle = document.createElementNS(ns, 'circle');
        circle.setAttribute('cx', toX(p.lng));
        circle.setAttribute('cy', toY(p.lat));
        circle.setAttribute('r', p.confidence >= 80 ? 7 : 5);
        circle.setAttribute('fill', p.confidence >= 80 ? '#B94035' : '#D99227');
        circle.setAttribute('fill-opacity', '0.75');
        circle.setAttribute('stroke', '#fff');
        circle.setAttribute('stroke-width', 1.5);
        const title = document.createElementNS(ns, 'title');
        title.textContent = `${p.province ?? '—'} · ${new Date(p.detected_at).toLocaleString('id-ID')}`;
        circle.appendChild(title);
        svg.appendChild(circle);
    }

    container.appendChild(svg);
}

document.addEventListener('DOMContentLoaded', initHomePage);
