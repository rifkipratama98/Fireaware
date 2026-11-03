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

    const map = L.map(mapEl, { zoomControl: true }).setView([-1.7, 114], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 48,
    });
    map.addLayer(cluster);

    function setOverlay(type) {
        [overlayLoading, overlayError, overlayEmpty].forEach((el) => {
            if (el) {
                el.hidden = el.dataset.mapState !== type;
            }
        });
    }

    let activeRequest = null;

    async function loadData(params) {
        if (activeRequest) {
            activeRequest.abort();
        }
        activeRequest = new AbortController();
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
            setOverlay('error');
        }
    }

    function renderMarkers(points) {
        cluster.clearLayers();
        points.forEach((p) => {
            const cls = confidenceClass(p.confidence);
            const marker = L.circleMarker([p.lat, p.lng], {
                radius: p.confidence >= 80 ? 8 : 6,
                color: '#ffffff',
                weight: 1.5,
                fillColor: circleColor(cls),
                fillOpacity: 0.85,
            });

            marker.bindTooltip(`${p.province ?? '?'} · ${p.confidence ?? '?'}%`, {
                direction: 'top',
                opacity: 0.95,
            });
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
            if (offcanvasEl) {
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
