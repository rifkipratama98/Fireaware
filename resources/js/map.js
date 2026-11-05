// FireAware — halaman Peta Hotspot (Leaflet + marker cluster)
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import L from 'leaflet';
import 'leaflet.markercluster';

const CFG = window.fireawareMap;

// --- Helper (cakupan modul) ---
function confidenceClass(c) {
    return c >= 80 ? 'conf-high' : c >= 60 ? 'conf-mid' : 'conf-low';
}

function circleColor(cls) {
    return cls === 'conf-high' ? '#B94035' : cls === 'conf-mid' ? '#D99227' : '#477B59';
}

function setText(field, value) {
    const el = document.querySelector(`[data-field="${field}"]`);
    if (el) el.textContent = value;
}

function toggleRow(row, show) {
    const el = document.querySelector(`[data-row="${row}"]`);
    if (el) el.classList.toggle('d-none', !show);
}

function fmtCoord(v) {
    return typeof v === 'number' ? Number(v).toFixed(4) : '—';
}

function fmtDateTime(iso) {
    if (!iso) return '—';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '—';
    return d.toLocaleString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

function sourceLabel(src) {
    if (src === 'demo') return 'Data DEMO internal';
    if (src === 'viirs') return 'Satelit VIIRS';
    if (src === 'modis') return 'Satelit MODIS';
    return src ? String(src) : 'Data DEMO internal';
}

function pushState(f) {
    const url = new URL(window.location.href);
    if (f.provinsi) url.searchParams.set('provinsi', f.provinsi); else url.searchParams.delete('provinsi');
    if (f.dari) url.searchParams.set('dari', f.dari); else url.searchParams.delete('dari');
    if (f.sampai) url.searchParams.set('sampai', f.sampai); else url.searchParams.delete('sampai');
    window.history.pushState({}, '', url.toString());
}

function initMapView() {
    const mapEl = document.getElementById('leafletMap');
    if (!mapEl || !CFG) {
        return;
    }
    const frame = mapEl.parentElement;
    const overlayLoading = frame.querySelector('[data-map-state="loading"]');
    const overlayError = frame.querySelector('[data-map-state="error"]');
    const overlayEmpty = frame.querySelector('[data-map-state="empty"]');

    const map = L.map(mapEl, { zoomControl: true }).setView([-1.5, 114.5], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | NASA FIRMS',
    }).addTo(map);

    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 48,
    });
    map.addLayer(cluster);

    // Tombol pusatkan peta
    const recenterBtn = document.getElementById('mapRecenterBtn');
    if (recenterBtn) {
        recenterBtn.addEventListener('click', () => {
            map.setView([-1.5, 114.5], 6);
        });
    }

    function setOverlay(type) {
        [overlayLoading, overlayError, overlayEmpty].forEach((el) => {
            if (el) {
                el.hidden = el.dataset.mapState !== type;
            }
        });
    }

    let activeRequest = null;
    let lastLoadedFilter = {};

    function updateStats(points) {
        const total = points.length;
        const high = points.filter((p) => p.confidence >= 80).length;
        const mid = points.filter((p) => p.confidence >= 60 && p.confidence < 80).length;
        const low = points.filter((p) => p.confidence < 60).length;

        const totalEl = document.querySelector('[data-stat="total"]');
        if (totalEl) totalEl.textContent = total.toLocaleString('id-ID');

        const highEl = document.querySelector('[data-stat="high"]');
        if (highEl) highEl.textContent = high.toLocaleString('id-ID');

        const midEl = document.querySelector('[data-stat="mid"]');
        if (midEl) midEl.textContent = mid.toLocaleString('id-ID');

        const lowEl = document.querySelector('[data-stat="low"]');
        if (lowEl) lowEl.textContent = low.toLocaleString('id-ID');
    }

    async function loadData(params) {
        if (activeRequest) {
            activeRequest.abort();
        }
        activeRequest = new AbortController();
        lastLoadedFilter = params;
        setOverlay('loading');

        const q = new URLSearchParams();
        if (params.provinsi) q.set('provinsi', params.provinsi);
        if (params.dari) q.set('dari', params.dari);
        if (params.sampai) q.set('sampai', params.sampai);

        try {
            const res = await fetch(`${CFG.endpoint}?${q.toString()}`, {
                signal: activeRequest.signal,
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            const json = await res.json();
            const points = json.data || [];
            renderMarkers(points);
            updateStats(points);
            updateMeta(json.meta || {});
            if (points.length === 0) {
                setOverlay('empty');
                resetDetail();
            } else {
                setOverlay(null);
            }
        } catch (err) {
            if (err.name === 'AbortError') {
                return;
            }
            updateStats([]);
            setOverlay('error');
        }
    }

    function renderMarkers(points) {
        cluster.clearLayers();
        points.forEach((p) => {
            const cls = confidenceClass(p.confidence);
            const marker = L.circleMarker([p.lat, p.lng], {
                radius: p.confidence >= 80 ? 9 : 7,
                color: '#ffffff',
                weight: 2,
                fillColor: circleColor(cls),
                fillOpacity: 0.88,
            });

            marker.bindTooltip(`<strong>${p.province ?? 'Kalimantan'}</strong> &bull; ${p.confidence ?? '?'}%`, {
                direction: 'top',
                opacity: 0.95,
                className: 'custom-map-tooltip',
            });

            const popupHtml = `
                <div class="map-popup-card">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-1 border-bottom">
                        <strong class="text-dark small">${p.province ?? 'Kalimantan'}</strong>
                        <span class="conf-chip ${cls}" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">${p.confidence ?? '?'}%</span>
                    </div>
                    <div class="small text-secondary mb-1">
                        <i class="bi bi-clock me-1"></i>${fmtDateTime(p.detected_at)}
                    </div>
                    <div class="small font-monospace text-secondary mb-1">
                        <i class="bi bi-geo-alt me-1"></i>${fmtCoord(p.lat)}, ${fmtCoord(p.lng)}
                    </div>
                    ${p.frp != null ? `<div class="small text-secondary mb-1"><strong>FRP:</strong> ${Number(p.frp).toLocaleString('id-ID')} MW</div>` : ''}
                    ${p.sensor ? `<div class="small text-secondary mb-1"><strong>Sensor:</strong> ${p.sensor}</div>` : ''}
                    <div class="small text-secondary-soft mt-1 pt-1 border-top" style="font-size: 0.72rem;">
                        Sumber: ${sourceLabel(p.source)}
                    </div>
                </div>
            `;
            marker.bindPopup(popupHtml, { maxWidth: 280, className: 'custom-leaflet-popup' });

            marker.on('click', () => showDetail(p, cls));
            cluster.addLayer(marker);
        });
    }

    function showDetail(p) {
        const placeholder = document.getElementById('detailPlaceholder');
        const content = document.getElementById('detailContent');
        if (placeholder) placeholder.hidden = true;
        if (content) content.hidden = false;

        setText('detected_at', fmtDateTime(p.detected_at));
        setText('coord', `${fmtCoord(p.lat)}, ${fmtCoord(p.lng)}`);
        setText('province', p.province ?? '—');
        setText('confidence', p.confidence != null ? `${p.confidence}%` : '—');
        setText('source', sourceLabel(p.source));

        toggleRow('sensor', Boolean(p.sensor));
        if (p.sensor) setText('sensor', p.sensor);

        toggleRow('frp', p.frp != null);
        if (p.frp != null) setText('frp', `${Number(p.frp).toLocaleString('id-ID')} MW`);
    }

    function resetDetail() {
        const placeholder = document.getElementById('detailPlaceholder');
        const content = document.getElementById('detailContent');
        if (placeholder) placeholder.hidden = false;
        if (content) content.hidden = true;
    }

    function updateMeta(meta) {
        const updatedAtEl = document.querySelector('[data-slot="updated-at"]');
        if (updatedAtEl) {
            updatedAtEl.textContent = meta.updated_at ? fmtDateTime(meta.updated_at) : '—';
        }
        const sourceEl = document.querySelector('[data-slot="source-label"]');
        if (sourceEl) {
            sourceEl.textContent = meta.source ? sourceLabel(meta.source) : (CFG.sourceLabel || 'Data DEMO internal');
        }

        // Indikator kesegaran data
        const stalenessEl = document.querySelector('[data-slot="staleness-badge"]');
        if (stalenessEl && meta.updated_at) {
            const updatedTime = new Date(meta.updated_at).getTime();
            const ageHours = (Date.now() - updatedTime) / (1000 * 60 * 60);
            if (ageHours > 48) {
                stalenessEl.innerHTML = `
                    <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 d-inline-flex align-items-center gap-1">
                        <i class="bi bi-clock-history"></i>Rekaman Historis
                    </span>
                `;
            } else {
                stalenessEl.innerHTML = `
                    <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 d-inline-flex align-items-center gap-1">
                        <span class="pulse-indicator"></span>Pembaruan Terkini
                    </span>
                `;
            }
        }
    }

    // Tombol coba lagi saat error & reset saat kosong
    const retryBtn = document.getElementById('mapRetryBtn');
    if (retryBtn) {
        retryBtn.addEventListener('click', () => loadData(lastLoadedFilter));
    }
    const emptyResetBtn = document.getElementById('mapEmptyResetBtn');
    if (emptyResetBtn) {
        emptyResetBtn.addEventListener('click', () => {
            const resetBtn = document.getElementById('mapFilterReset');
            if (resetBtn) resetBtn.click();
        });
    }

    // --- Form filter ---
    const form = document.getElementById('mapFilterForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const provinsi = document.getElementById('filterProvince')?.value ?? '';
            const dari = document.getElementById('filterFrom')?.value ?? '';
            const sampai = document.getElementById('filterTo')?.value ?? '';

            if (dari && sampai && dari > sampai) {
                alert('Tanggal "dari" tidak boleh setelah "sampai".');
                return;
            }

            loadData({ provinsi, dari, sampai });
            pushState({ provinsi, dari, sampai });

            const offcanvasEl = document.getElementById('filterOffcanvas');
            if (offcanvasEl && typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                const inst = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (inst) inst.hide();
            }
        });

        const resetBtn = document.getElementById('mapFilterReset');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                const selP = document.getElementById('filterProvince');
                const selF = document.getElementById('filterFrom');
                const selT = document.getElementById('filterTo');
                if (selP) selP.value = '';
                if (selF) selF.value = '';
                if (selT) selT.value = '';
                loadData({ provinsi: '', dari: '', sampai: '' });
                pushState({ provinsi: '', dari: '', sampai: '' });
            });
        }

        // Shortcut tanggal cepat
        const presetBtns = document.querySelectorAll('[data-date-preset]');
        presetBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const days = parseInt(btn.dataset.datePreset, 10);
                const toDate = new Date();
                const fromDate = new Date();
                fromDate.setDate(toDate.getDate() - days);

                const fmt = (d) => d.toISOString().split('T')[0];
                const selF = document.getElementById('filterFrom');
                const selT = document.getElementById('filterTo');
                if (selF) selF.value = fmt(fromDate);
                if (selT) selT.value = fmt(toDate);

                form.dispatchEvent(new Event('submit'));
            });
        });

        // Muat awal sesuai filter dari server (URL)
        loadData(CFG.initialFilter || {});
    } else {
        loadData(CFG.initialFilter || {});
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMapView);
} else {
    initMapView();
}
