import React, { useState, useEffect, useRef, useMemo, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'motion/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { Button } from '@/Components/Button';
import { cn } from '@/Lib/utils';
import {
    Flame,
    MapPin,
    RotateCcw,
    Satellite,
    Maximize2,
    Minimize2,
    Compass,
    Clock,
    AlertTriangle,
    Shield,
    CheckCircle2,
    Copy,
    Check,
    X,
    Sliders,
    ChevronRight,
    ArrowUpRight,
    RefreshCw,
    Crosshair,
    Plus,
    Minus,
} from 'lucide-react';

// Static imports to guarantee zero dynamic-import bundling race conditions
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import L from 'leaflet';
import 'leaflet.markercluster';

if (typeof window !== 'undefined') {
    window.L = L;
}

// Geographic Boundary of the 5 Kalimantan Provinces (WGS84 / EPSG:4326)
// Southwest: [-4.4, 108.6] (South of Kalsel/Kalteng, West of Kalbar)
// Northeast: [4.4, 119.2]  (North of Kaltara border, East of Kaltim coast)
const KALIMANTAN_BOUNDS = [
    [-4.4, 108.6],
    [4.4, 119.2],
];
const KALIMANTAN_CENTER = [-0.5, 114.0];

export default function HotspotMap({
    provinces = [],
    filter = {},
    activeProvince = null,
    kalimantanBounds = KALIMANTAN_BOUNDS,
    lastUpdatedAt = null,
    syncStatus = {
        code: 'latest',
        label: 'Data terbaru',
        description: 'Telemetri satelit termutakhir.',
        lastSyncAt: null,
        lastObservationAt: null,
    },
    maxPoints = 3000,
    apiEndpoint = '/api/hotspot-map/data',
}) {
    // Filter State
    const [selectedProvince, setSelectedProvince] = useState(filter.provinsi || '');
    const [fromDate, setFromDate] = useState(filter.dari || '');
    const [toDate, setToDate] = useState(filter.sampai || '');
    const [selectedConfidence, setSelectedConfidence] = useState('all'); // all | high | nominal | low
    const [datePreset, setDatePreset] = useState(null);

    // Data & UI State
    const [hotspots, setHotspots] = useState([]);
    const [rawPointsCache, setRawPointsCache] = useState([]);
    const [meta, setMeta] = useState({ total: 0, updated_at: lastUpdatedAt, source: 'NASA FIRMS' });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedHotspot, setSelectedHotspot] = useState(null);
    const [mobileFilterOpen, setMobileFilterOpen] = useState(false);
    const [mobileDetailOpen, setMobileDetailOpen] = useState(false);
    const [copied, setCopied] = useState(false);
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [activeBasemap, setActiveBasemap] = useState('satellite'); // satellite | osm | topo

    // Leaflet Refs
    const mapContainerRef = useRef(null);
    const mapInstanceRef = useRef(null);
    const clusterGroupRef = useRef(null);
    const baseLayersRef = useRef({});
    const markerMapRef = useRef(new Map());
    const abortControllerRef = useRef(null);

    // Date & Number Format Helpers
    const formatDateTime = useCallback((isoString) => {
        if (!isoString) return '—';
        try {
            const d = new Date(isoString);
            if (Number.isNaN(d.getTime())) return '—';
            return d.toLocaleString('id-ID', {
                timeZone: 'Asia/Makassar',
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch {
            return '—';
        }
    }, []);

    const formatDateOnly = (isoString) => {
        if (!isoString) return '—';
        try {
            const d = new Date(isoString);
            if (Number.isNaN(d.getTime())) return '—';
            return d.toLocaleDateString('id-ID', {
                timeZone: 'Asia/Makassar',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        } catch {
            return '—';
        }
    };

    const formatTimeOnly = (isoString) => {
        if (!isoString) return '—';
        try {
            const d = new Date(isoString);
            if (Number.isNaN(d.getTime())) return '—';
            return (
                d.toLocaleTimeString('id-ID', {
                    timeZone: 'Asia/Makassar',
                    hour: '2-digit',
                    minute: '2-digit',
                }) + ' WITA'
            );
        } catch {
            return '—';
        }
    };

    const formatCoord = (val) => (typeof val === 'number' ? Number(val).toFixed(4) : '—');

    // NASA FIRMS Standard Confidence Classification (High, Nominal, Low)
    const getConfidenceCategory = useCallback((conf) => {
        if (typeof conf === 'string') {
            const lower = conf.trim().toLowerCase();
            if (lower === 'high' || lower === 'h') return { key: 'high', label: 'High Confidence', color: '#E05A2B', minPct: 80 };
            if (lower === 'nominal' || lower === 'n' || lower === 'med') return { key: 'nominal', label: 'Nominal Confidence', color: '#F4A261', minPct: 60 };
            if (lower === 'low' || lower === 'l') return { key: 'low', label: 'Low Confidence', color: '#52B788', minPct: 0 };
        }
        const num = Number(conf);
        if (!Number.isNaN(num)) {
            if (num >= 80) return { key: 'high', label: 'High Confidence', color: '#E05A2B', minPct: 80 };
            if (num >= 60) return { key: 'nominal', label: 'Nominal Confidence', color: '#F4A261', minPct: 60 };
            return { key: 'low', label: 'Low Confidence', color: '#52B788', minPct: 0 };
        }
        return { key: 'nominal', label: 'Nominal Confidence', color: '#F4A261', minPct: 60 };
    }, []);

    const sourceLabel = (src) => {
        if (src === 'demo') return 'Telemetri Satelit Terpadu (NASA FIRMS / BMKG)';
        if (src === 'viirs') return 'Satelit NASA VIIRS (Resolusi 375m)';
        if (src === 'modis') return 'Satelit NASA MODIS (Resolusi 1km)';
        return src ? String(src) : 'Telemetri Satelit NASA FIRMS';
    };

    // Calculate Accurate Telemetry Statistics
    const stats = useMemo(() => {
        const total = hotspots.length;
        const high = hotspots.filter((p) => getConfidenceCategory(p.confidence).key === 'high').length;
        const nominal = hotspots.filter((p) => getConfidenceCategory(p.confidence).key === 'nominal').length;
        const low = hotspots.filter((p) => getConfidenceCategory(p.confidence).key === 'low').length;
        return { total, high, nominal, low };
    }, [hotspots, getConfidenceCategory]);

    // Handle Quick Date Presets
    const handlePresetClick = (days) => {
        setDatePreset(days);
        const today = new Date();
        const past = new Date();
        past.setDate(today.getDate() - days);

        const formatDateStr = (d) => d.toISOString().split('T')[0];

        setToDate(formatDateStr(today));
        setFromDate(days === 0 ? formatDateStr(today) : formatDateStr(past));
    };

    // Copy Coordinates to Clipboard
    const handleCopyCoord = (lat, lng) => {
        const text = `${formatCoord(lat)}, ${formatCoord(lng)}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    // Confidence filter utility
    const filterPointsByConfidence = useCallback(
        (points, confKey) => {
            if (confKey === 'high') {
                return points.filter((p) => getConfidenceCategory(p.confidence).key === 'high');
            }
            if (confKey === 'nominal') {
                return points.filter((p) => getConfidenceCategory(p.confidence).key === 'nominal');
            }
            if (confKey === 'low') {
                return points.filter((p) => getConfidenceCategory(p.confidence).key === 'low');
            }
            return points;
        },
        [getConfidenceCategory]
    );

    // Select Hotspot Interaction (Synchronized with Marker DOM & Detail Panel)
    const handleSelectHotspot = useCallback((p) => {
        setSelectedHotspot(p);
        setMobileDetailOpen(true);

        if (mapInstanceRef.current && p) {
            mapInstanceRef.current.panTo([Number(p.lat), Number(p.lng)], {
                animate: true,
                duration: 0.6,
            });
        }

        // Dynamically toggle .is-selected class on marker DOM elements
        markerMapRef.current.forEach((markerInstance, id) => {
            const el = markerInstance.getElement();
            if (el) {
                if (id === p.id) {
                    el.classList.add('is-selected');
                } else {
                    el.classList.remove('is-selected');
                }
            }
        });
    }, []);

    // Clear Selection
    const handleClearSelection = useCallback(() => {
        setSelectedHotspot(null);
        markerMapRef.current.forEach((markerInstance) => {
            markerInstance.getElement()?.classList.remove('is-selected');
        });
    }, []);

    // Update Markers on Leaflet Map
    const updateMapMarkers = useCallback(
        (points) => {
            if (!clusterGroupRef.current) return;
            clusterGroupRef.current.clearLayers();
            markerMapRef.current.clear();

            points.forEach((p) => {
                const lat = Number(p.lat);
                const lng = Number(p.lng);
                if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                const conf = getConfidenceCategory(p.confidence);
                const isSelected = selectedHotspot && selectedHotspot.id === p.id;

                // Create Custom GIS Thermal Hotspot Marker (Authentic Earth Observation Point)
                const customDivIcon = L.divIcon({
                    className: `gis-hotspot-container ${isSelected ? 'is-selected' : ''}`,
                    html: `
                        <div class="gis-marker-reticle"></div>
                        <div class="gis-marker-dot ${conf.key}">
                            <span class="gis-marker-pip"></span>
                        </div>
                    `,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13],
                    popupAnchor: [0, -13],
                });

                const marker = L.marker([lat, lng], {
                    icon: customDivIcon,
                    riseOnHover: true,
                });

                // Tooltip on Hover
                const tooltipHtml = `
                    <div style="font-family: var(--font-sans); min-width: 175px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 4px;">
                            <strong style="color: #F8F9FA; font-size: 12px;">${p.province ?? 'Kalimantan'}</strong>
                            <span style="font-size: 9.5px; font-weight: 800; padding: 1.5px 6px; border-radius: 9999px; background: ${conf.color}25; color: ${conf.color}; border: 1px solid ${conf.color}50;">
                                ${conf.label}
                            </span>
                        </div>
                        <div style="font-size: 11px; color: #e2e8f0; margin-bottom: 3px;">
                            <span style="color: rgba(255,255,255,0.5);">Waktu:</span> <strong>${formatDateTime(p.detected_at)} WITA</strong>
                        </div>
                        <div style="font-size: 10px; font-family: monospace; color: #cbd5e1;">
                            <span style="color: rgba(255,255,255,0.5);">Posisi:</span> ${formatCoord(lat)}, ${formatCoord(lng)}
                        </div>
                        ${p.frp != null ? `<div style="font-size: 10.5px; color: #F4A261; margin-top: 3px;"><span style="color: rgba(255,255,255,0.5);">FRP:</span> <strong>${Number(p.frp).toLocaleString('id-ID')} MW</strong></div>` : ''}
                    </div>
                `;

                marker.bindTooltip(tooltipHtml, {
                    direction: 'top',
                    opacity: 0.95,
                    className: 'custom-map-tooltip',
                    offset: [0, -18],
                });

                // Popup with rich information
                const popupContent = `
                    <div style="font-family: 'Inter', sans-serif; font-size: 12px; color: #F8F9FA; min-width: 230px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.14); padding-bottom: 6px;">
                            <div>
                                <span style="font-size: 10px; color: rgba(255,255,255,0.5); font-weight: 700; text-transform: uppercase;">Hotspot #${p.id}</span>
                                <strong style="display: block; color: #F8F9FA; font-size: 13px; font-weight: 700;">${p.province ?? 'Kalimantan'}</strong>
                            </div>
                            <span style="background: ${conf.color}25; color: ${conf.color}; font-weight: 800; font-size: 10px; padding: 2px 7px; border-radius: 9999px; border: 1px solid ${conf.color}55;">
                                ${conf.label}
                            </span>
                        </div>
                        <div style="font-size: 11px; color: #e2e8f0; margin-bottom: 4px;">
                            <span style="color: rgba(255,255,255,0.5);">Waktu:</span> <strong>${formatDateTime(p.detected_at)} WITA</strong>
                        </div>
                        <div style="font-size: 11px; color: #e2e8f0; font-family: monospace; margin-bottom: 4px;">
                            <span style="color: rgba(255,255,255,0.5);">Koordinat:</span> ${formatCoord(lat)}, ${formatCoord(lng)}
                        </div>
                        <div style="font-size: 11px; color: #e2e8f0; margin-bottom: 4px;">
                            <span style="color: rgba(255,255,255,0.5);">Keyakinan:</span> <strong style="color: ${conf.color};">${p.confidence ?? '?'}%</strong>
                        </div>
                        ${p.frp != null ? `<div style="font-size: 11px; color: #F4A261; margin-bottom: 4px;"><span style="color: rgba(255,255,255,0.5);">FRP Panas:</span> <strong>${Number(p.frp).toLocaleString('id-ID')} MW</strong></div>` : ''}
                        ${p.brightness != null ? `<div style="font-size: 11px; color: #e2e8f0; margin-bottom: 4px;"><span style="color: rgba(255,255,255,0.5);">Suhu Sensor:</span> <strong>${Number(p.brightness).toFixed(1)} K</strong></div>` : ''}
                        ${p.sensor ? `<div style="font-size: 10px; color: #94a3b8; margin-top: 6px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.1);">Sensor: ${p.sensor}</div>` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 290,
                    className: 'custom-leaflet-popup',
                });

                // Click selection interaction
                marker.on('click', () => {
                    handleSelectHotspot(p);
                });

                markerMapRef.current.set(p.id, marker);
                clusterGroupRef.current.addLayer(marker);
            });
        },
        [getConfidenceCategory, selectedHotspot, formatDateTime, handleSelectHotspot]
    );

    // Fetch Data from Backend API
    const fetchData = useCallback(
        async (params) => {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
            abortControllerRef.current = new AbortController();
            setLoading(true);
            setError(null);

            const q = new URLSearchParams();
            if (params.provinsi) q.set('provinsi', params.provinsi);
            if (params.dari) q.set('dari', params.dari);
            if (params.sampai) q.set('sampai', params.sampai);

            try {
                const res = await fetch(`${apiEndpoint}?${q.toString()}`, {
                    signal: abortControllerRef.current.signal,
                    headers: { Accept: 'application/json' },
                });

                if (!res.ok) {
                    const errJson = await res.json().catch(() => ({}));
                    throw new Error(errJson.error || `HTTP ${res.status}`);
                }

                const json = await res.json();
                const rawPoints = json.data || [];
                setRawPointsCache(rawPoints);

                // Filter points by confidence level
                const filtered = filterPointsByConfidence(rawPoints, params.confidence);

                setHotspots(filtered);
                setMeta(json.meta || {});
                setLoading(false);

                // Render markers on map
                updateMapMarkers(filtered);

                // Update URL search params cleanly without reload
                const url = new URL(window.location.href);
                if (params.provinsi) url.searchParams.set('provinsi', params.provinsi);
                else url.searchParams.delete('provinsi');
                if (params.dari) url.searchParams.set('dari', params.dari);
                else url.searchParams.delete('dari');
                if (params.sampai) url.searchParams.set('sampai', params.sampai);
                else url.searchParams.delete('sampai');
                window.history.replaceState({}, '', url.toString());
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error('Error fetching hotspot map data:', err);
                setError(err.message || 'Terjadi gangguan saat mengambil data titik panas.');
                setLoading(false);
            }
        },
        [apiEndpoint, filterPointsByConfidence, updateMapMarkers]
    );

    // Initialize Leaflet Map (Ensuring single initialization and clean lifecycle)
    useEffect(() => {
        if (typeof window === 'undefined') return;
        if (!mapContainerRef.current) return;
        if (mapInstanceRef.current) return;

        let isMounted = true;

        try {
            // Create Map with fixed Center and Zoom to guarantee Kalimantan is immediately visible
            const map = L.map(mapContainerRef.current, {
                center: KALIMANTAN_CENTER,
                zoom: 6,
                minZoom: 5,
                maxZoom: 18,
                maxBounds: [
                    [-7.0, 106.0], // South limit (above Java/Australia)
                    [7.0, 122.0],  // North limit (above Borneo)
                ],
                maxBoundsViscosity: 0.9,
                zoomControl: false,
                scrollWheelZoom: true,
                attributionControl: false,
            });

            // Base Tile Layers (Default: Authentic Satellite Hybrid View)
            const satelliteImagery = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 18,
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                }
            );

            const satelliteLabels = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 18,
                    opacity: 0.9,
                }
            );

            const satelliteHybrid = L.layerGroup([satelliteImagery, satelliteLabels]);

            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors | NASA FIRMS',
            });

            const topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                maxZoom: 17,
                attribution: 'Map data: &copy; OpenStreetMap contributors, SRTM | Map style: &copy; OpenTopoMap',
            });

            baseLayersRef.current = {
                satellite: satelliteHybrid,
                osm: osmLayer,
                topo: topoLayer,
            };

            // Add default Satellite layer
            satelliteHybrid.addTo(map);

            // Configure Custom Hotspot Marker Cluster Group (Authentic Cartographic Density)
            const cluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                maxClusterRadius: 36, // Compact clustering so regencies remain distinct
                spiderfyOnMaxZoom: true,
                zoomToBoundsOnClick: true,
                animate: true,
                animateAddingMarkers: false,
                disableClusteringAtZoom: 12,
                spiderLegPolylineOptions: {
                    weight: 1.5,
                    color: '#FFFFFF',
                    opacity: 0.9,
                    dashArray: '4, 3',
                },
                iconCreateFunction: function (c) {
                    const count = c.getChildCount();
                    let sizeClass = 'gis-cluster-sm';
                    let size = 32;

                    if (count >= 50) {
                        sizeClass = 'gis-cluster-lg';
                        size = 44;
                    } else if (count >= 10) {
                        sizeClass = 'gis-cluster-md';
                        size = 38;
                    }

                    return L.divIcon({
                        html: `
                            <div class="gis-cluster-wrapper">
                                <div class="gis-cluster-badge">
                                    <span>${count.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                        `,
                        className: `gis-cluster-container ${sizeClass}`,
                        iconSize: L.point(size, size),
                        iconAnchor: L.point(size / 2, size / 2),
                    });
                },
            });

            map.addLayer(cluster);
            clusterGroupRef.current = cluster;
            mapInstanceRef.current = map;

            // Invalidate size and apply accurate fitBounds once container is painted
            requestAnimationFrame(() => {
                if (!isMounted || !map) return;
                map.invalidateSize();

                if (activeProvince && activeProvince.center_lat && activeProvince.center_lng) {
                    map.setView([parseFloat(activeProvince.center_lat), parseFloat(activeProvince.center_lng)], 7);
                } else {
                    map.fitBounds(KALIMANTAN_BOUNDS, {
                        padding: [24, 24],
                        maxZoom: 7,
                    });
                }
            });

            // Container ResizeObserver to maintain map layout
            const resizeObserver = new ResizeObserver(() => {
                if (mapInstanceRef.current) {
                    mapInstanceRef.current.invalidateSize();
                }
            });
            resizeObserver.observe(mapContainerRef.current);

            // Initial Data Fetch
            fetchData({
                provinsi: selectedProvince,
                dari: fromDate,
                sampai: toDate,
                confidence: selectedConfidence,
            });

            return () => {
                isMounted = false;
                resizeObserver.disconnect();
                if (abortControllerRef.current) {
                    abortControllerRef.current.abort();
                }
                if (mapInstanceRef.current) {
                    mapInstanceRef.current.remove();
                    mapInstanceRef.current = null;
                }
            };
        } catch (err) {
            console.error('Failed to initialize Leaflet Map:', err);
            setError('Gagal memuat modul peta spasial. Silakan muat ulang halaman.');
            setLoading(false);
        }
    }, []); // eslint-disable-line react-hooks/exhaustive-deps

    // Switch Base Layers
    const handleSwitchBasemap = (layerKey) => {
        if (!mapInstanceRef.current || !baseLayersRef.current[layerKey]) return;
        Object.values(baseLayersRef.current).forEach((layer) => {
            if (mapInstanceRef.current.hasLayer(layer)) {
                mapInstanceRef.current.removeLayer(layer);
            }
        });
        baseLayersRef.current[layerKey].addTo(mapInstanceRef.current);
        setActiveBasemap(layerKey);
    };

    // Zoom Controls
    const handleZoomIn = () => {
        mapInstanceRef.current?.zoomIn();
    };

    const handleZoomOut = () => {
        mapInstanceRef.current?.zoomOut();
    };

    // Focus Kalimantan: Smoothly fits the 5 provinces of Kalimantan
    const handleFocusKalimantan = () => {
        if (!mapInstanceRef.current) return;
        mapInstanceRef.current.fitBounds(KALIMANTAN_BOUNDS, {
            padding: [24, 24],
            maxZoom: 7,
            duration: 1.0,
        });
    };

    // Reset View: Returns back to the initial view of Kalimantan
    const handleResetView = () => {
        if (!mapInstanceRef.current) return;
        handleClearSelection();
        mapInstanceRef.current.fitBounds(KALIMANTAN_BOUNDS, {
            padding: [24, 24],
            maxZoom: 7,
            duration: 1.0,
        });
    };

    // Focus on specific hotspot marker
    const handleFocusHotspot = (h) => {
        if (!mapInstanceRef.current || !h) return;
        mapInstanceRef.current.flyTo([Number(h.lat), Number(h.lng)], 13, { duration: 1.4 });

        const marker = markerMapRef.current.get(h.id);
        if (marker) {
            setTimeout(() => {
                marker.openPopup();
            }, 600);
        }
    };

    // Apply Filter form submission
    const handleApplyFilter = (e) => {
        if (e) e.preventDefault();
        setMobileFilterOpen(false);

        fetchData({
            provinsi: selectedProvince,
            dari: fromDate,
            sampai: toDate,
            confidence: selectedConfidence,
        });

        // Pan map to province center if selected
        if (selectedProvince && mapInstanceRef.current) {
            const found = provinces.find((p) => p.slug === selectedProvince);
            if (found && found.center_lat && found.center_lng) {
                mapInstanceRef.current.flyTo([parseFloat(found.center_lat), parseFloat(found.center_lng)], 7, {
                    duration: 1.2,
                });
            }
        } else if (!selectedProvince && mapInstanceRef.current) {
            handleFocusKalimantan();
        }
    };

    // Reset Filter completely
    const handleResetFilter = () => {
        setSelectedProvince('');
        setFromDate('');
        setToDate('');
        setSelectedConfidence('all');
        setDatePreset(null);
        handleClearSelection();
        setMobileFilterOpen(false);

        fetchData({
            provinsi: '',
            dari: '',
            sampai: '',
            confidence: 'all',
        });

        if (mapInstanceRef.current) {
            mapInstanceRef.current.fitBounds(KALIMANTAN_BOUNDS, {
                padding: [24, 24],
                maxZoom: 7,
                duration: 1.0,
            });
        }
    };

    // Direct Confidence Filter Click on Stat Cards
    const handleConfidenceChange = (newConf) => {
        setSelectedConfidence(newConf);
        const filtered = filterPointsByConfidence(rawPointsCache, newConf);
        setHotspots(filtered);
        updateMapMarkers(filtered);
    };

    // Toggle Fullscreen Container
    const handleToggleFullscreen = () => {
        if (!mapContainerRef.current) return;
        const container = mapContainerRef.current.parentElement;
        if (!document.fullscreenElement) {
            container?.requestFullscreen().catch(() => {});
            setIsFullscreen(true);
        } else {
            document.exitFullscreen().catch(() => {});
            setIsFullscreen(false);
        }
        setTimeout(() => {
            mapInstanceRef.current?.invalidateSize();
        }, 300);
    };

    // Status sync badge helper
    const getSyncBadge = (status) => {
        switch (status.code) {
            case 'latest':
                return {
                    pillClass: 'bg-[#2A9D8F]/15 text-[#2A9D8F] border-[#2A9D8F]/25',
                    dotClass: 'bg-[#2A9D8F]',
                    showPing: true,
                };
            case 'outdated':
                return {
                    pillClass: 'bg-[#F4A261]/20 text-[#a85817] border-[#F4A261]/35',
                    dotClass: 'bg-[#F4A261]',
                    showPing: false,
                };
            case 'pending':
                return {
                    pillClass: 'bg-[#E05A2B]/15 text-[#E05A2B] border-[#E05A2B]/25',
                    dotClass: 'bg-[#E05A2B]',
                    showPing: true,
                };
            case 'failed':
                return {
                    pillClass: 'bg-[#E63946]/15 text-[#E63946] border-[#E63946]/25',
                    dotClass: 'bg-[#E63946]',
                    showPing: false,
                };
            default:
                return {
                    pillClass: 'bg-black/10 text-[#1E242B]/70 border-black/15',
                    dotClass: 'bg-[#1E242B]/40',
                    showPing: false,
                };
        }
    };

    const syncBadge = getSyncBadge(syncStatus);

    return (
        <PublicLayout title="Peta Sebaran Titik Panas — FireAware Kalimantan">
            <div className="min-h-screen bg-[#F6F5F2] pt-24 sm:pt-28 pb-16 selection:bg-[#E05A2B]/20 selection:text-[#E05A2B]">
                {/* 1. COMPACT GEOSPATIAL PAGE HEADER */}
                <header className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    {/* Breadcrumbs */}
                    <nav className="flex items-center gap-2 text-xs text-[#1E242B]/60 mb-3" aria-label="Breadcrumb">
                        <Link href="/" className="hover:text-[#E05A2B] transition-colors">
                            Beranda
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 opacity-40" />
                        <span className="font-semibold text-[#1E242B]">Peta Hotspot</span>
                    </nav>

                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-black/10">
                        <div>
                            <div className="flex flex-wrap items-center gap-2 mb-2">
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#1B4332]/10 text-[#1B4332] border border-[#1B4332]/20">
                                    <Satellite className="w-3.5 h-3.5" />
                                    <span>Telemetri Satelit NASA VIIRS &amp; MODIS</span>
                                </span>

                                {/* Honest Synchronization Status Badge */}
                                <div
                                    className={cn(
                                        'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border',
                                        syncBadge.pillClass
                                    )}
                                    title={syncStatus.description}
                                >
                                    <span className="relative flex h-2 w-2">
                                        {syncBadge.showPing && (
                                            <span
                                                className={cn(
                                                    'animate-ping absolute inline-flex h-full w-full rounded-full opacity-75',
                                                    syncBadge.dotClass
                                                )}
                                            />
                                        )}
                                        <span className={cn('relative inline-flex rounded-full h-2 w-2', syncBadge.dotClass)} />
                                    </span>
                                    <span>{syncStatus.label}</span>
                                </div>
                            </div>

                            <h1 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight font-display text-[#1E242B]">
                                Peta Sebaran Titik Panas Kalimantan
                            </h1>

                            <p className="mt-1.5 text-xs sm:text-sm text-[#1E242B]/70 max-w-3xl leading-relaxed">
                                Pemantauan spasial anomali termal berbasis sensor polar satelit di 5 provinsi Kalimantan. Tingkat keyakinan (confidence) mencerminkan kepastian sensor satelit — <strong>bukan ukuran tingkat bahaya atau kebakaran lahan aktif</strong>.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-2.5 self-start lg:self-auto shrink-0">
                            {/* Observation Timestamp */}
                            <div
                                className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-black/10 shadow-xs text-xs text-[#1E242B]/80 font-medium"
                                title={syncStatus.description}
                            >
                                <Clock className="w-3.5 h-3.5 text-[#F4A261]" />
                                <span>
                                    Observasi: <strong className="text-[#1E242B]">{formatDateTime(meta.updated_at || lastUpdatedAt)} WITA</strong>
                                </span>
                            </div>

                            {/* Mobile Filter Toggle Button */}
                            <button
                                type="button"
                                onClick={() => setMobileFilterOpen(true)}
                                className="lg:hidden inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#E05A2B] text-white font-semibold text-xs shadow-sm hover:bg-[#c94e24] transition-all cursor-pointer"
                            >
                                <Sliders className="w-4 h-4" />
                                <span>Filter &amp; Wilayah</span>
                            </button>
                        </div>
                    </div>
                </header>

                {/* 2. SPATIAL TELEMETRY STATS BAR */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6" aria-label="Statistik Titik Panas">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        {/* Total Card */}
                        <div
                            onClick={() => handleConfidenceChange('all')}
                            className={cn(
                                'p-4 rounded-2xl bg-white border shadow-xs flex flex-col justify-between transition-all cursor-pointer hover:shadow-sm',
                                selectedConfidence === 'all' ? 'border-[#1E242B] ring-2 ring-[#1E242B]/10' : 'border-black/5'
                            )}
                        >
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-semibold text-[#1E242B]/65">Total Titik Panas</span>
                                <div className="w-7 h-7 rounded-lg bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center">
                                    <Flame className="w-4 h-4" />
                                </div>
                            </div>
                            <div className="flex items-baseline gap-1.5">
                                <span className="text-2xl sm:text-3xl font-extrabold text-[#1E242B] font-display">
                                    {loading ? '…' : stats.total.toLocaleString('id-ID')}
                                </span>
                                <span className="text-xs text-[#1E242B]/50 font-medium">titik</span>
                            </div>
                            <span className="text-[10px] text-[#1E242B]/45 mt-1">Sesuai filter aktif</span>
                        </div>

                        {/* High Confidence */}
                        <div
                            onClick={() => handleConfidenceChange(selectedConfidence === 'high' ? 'all' : 'high')}
                            className={cn(
                                'p-4 rounded-2xl bg-white border shadow-xs flex flex-col justify-between transition-all cursor-pointer hover:shadow-sm',
                                selectedConfidence === 'high' ? 'border-[#E05A2B] ring-2 ring-[#E05A2B]/20' : 'border-black/5'
                            )}
                        >
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-semibold text-[#1E242B]/65">High Confidence</span>
                                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E05A2B]/10 text-[#E05A2B]">
                                    ≥ 80%
                                </span>
                            </div>
                            <div className="flex items-baseline gap-1.5">
                                <span className="text-2xl sm:text-3xl font-extrabold text-[#E05A2B] font-display">
                                    {loading ? '…' : stats.high.toLocaleString('id-ID')}
                                </span>
                                <span className="text-xs text-[#E05A2B]/70 font-medium">titik</span>
                            </div>
                            <span className="text-[10px] text-[#1E242B]/45 mt-1">Keyakinan sensor tinggi</span>
                        </div>

                        {/* Nominal Confidence */}
                        <div
                            onClick={() => handleConfidenceChange(selectedConfidence === 'nominal' ? 'all' : 'nominal')}
                            className={cn(
                                'p-4 rounded-2xl bg-white border shadow-xs flex flex-col justify-between transition-all cursor-pointer hover:shadow-sm',
                                selectedConfidence === 'nominal' ? 'border-[#F4A261] ring-2 ring-[#F4A261]/20' : 'border-black/5'
                            )}
                        >
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-semibold text-[#1E242B]/65">Nominal Confidence</span>
                                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F4A261]/20 text-[#a85817]">
                                    60–79%
                                </span>
                            </div>
                            <div className="flex items-baseline gap-1.5">
                                <span className="text-2xl sm:text-3xl font-extrabold text-[#F4A261] font-display">
                                    {loading ? '…' : stats.nominal.toLocaleString('id-ID')}
                                </span>
                                <span className="text-xs text-[#F4A261]/70 font-medium">titik</span>
                            </div>
                            <span className="text-[10px] text-[#1E242B]/45 mt-1">Deteksi kondisi standar</span>
                        </div>

                        {/* Low Confidence */}
                        <div
                            onClick={() => handleConfidenceChange(selectedConfidence === 'low' ? 'all' : 'low')}
                            className={cn(
                                'p-4 rounded-2xl bg-white border shadow-xs flex flex-col justify-between transition-all cursor-pointer hover:shadow-sm',
                                selectedConfidence === 'low' ? 'border-[#52B788] ring-2 ring-[#52B788]/20' : 'border-black/5'
                            )}
                        >
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-semibold text-[#1E242B]/65">Low Confidence</span>
                                <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#52B788]/15 text-[#1B4332]">
                                    &lt; 60%
                                </span>
                            </div>
                            <div className="flex items-baseline gap-1.5">
                                <span className="text-2xl sm:text-3xl font-extrabold text-[#52B788] font-display">
                                    {loading ? '…' : stats.low.toLocaleString('id-ID')}
                                </span>
                                <span className="text-xs text-[#52B788]/70 font-medium">titik</span>
                            </div>
                            <span className="text-[10px] text-[#1E242B]/45 mt-1">Keyakinan deteksi rendah</span>
                        </div>
                    </div>
                </section>

                {/* 3. MAIN GEOSPATIAL MAP WORKSPACE */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        {/* LEFT: LEAFLET MAP VIEWPORT (Expansive 8/9 cols) */}
                        <div className="lg:col-span-8 xl:col-span-9 order-1">
                            <div className="relative w-full h-[540px] sm:h-[620px] lg:h-[720px] rounded-3xl overflow-hidden border border-black/10 shadow-xl bg-[#1E242B]">
                                {/* The Leaflet Map Container */}
                                <div ref={mapContainerRef} className="w-full h-full z-10" />

                                {/* FLOATING HUD CONTROLS (Top Right) */}
                                <div className="absolute top-4 right-4 z-20 flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    {/* Basemap Switcher */}
                                    <div className="hidden sm:inline-flex items-center p-1 rounded-xl bg-white/95 text-[#1E242B] shadow-md border border-black/10 backdrop-blur-md">
                                        <button
                                            type="button"
                                            onClick={() => handleSwitchBasemap('satellite')}
                                            className={cn(
                                                'px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer',
                                                activeBasemap === 'satellite' ? 'bg-[#1E242B] text-white shadow-xs' : 'text-[#1E242B]/70 hover:text-[#1E242B]'
                                            )}
                                        >
                                            Satelit
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleSwitchBasemap('osm')}
                                            className={cn(
                                                'px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer',
                                                activeBasemap === 'osm' ? 'bg-[#1E242B] text-white shadow-xs' : 'text-[#1E242B]/70 hover:text-[#1E242B]'
                                            )}
                                        >
                                            Peta Jalan
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleSwitchBasemap('topo')}
                                            className={cn(
                                                'px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer',
                                                activeBasemap === 'topo' ? 'bg-[#1E242B] text-white shadow-xs' : 'text-[#1E242B]/70 hover:text-[#1E242B]'
                                            )}
                                        >
                                            Topografi
                                        </button>
                                    </div>

                                    {/* Zoom In & Zoom Out Buttons */}
                                    <div className="inline-flex items-center rounded-xl bg-white/95 text-[#1E242B] shadow-md border border-black/10 backdrop-blur-md overflow-hidden">
                                        <button
                                            type="button"
                                            onClick={handleZoomIn}
                                            className="p-2 hover:bg-black/5 active:bg-black/10 transition-colors border-r border-black/10 cursor-pointer"
                                            title="Perbesar Peta"
                                            aria-label="Perbesar Peta"
                                        >
                                            <Plus className="w-4 h-4 text-[#1E242B]" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={handleZoomOut}
                                            className="p-2 hover:bg-black/5 active:bg-black/10 transition-colors cursor-pointer"
                                            title="Perkecil Peta"
                                            aria-label="Perkecil Peta"
                                        >
                                            <Minus className="w-4 h-4 text-[#1E242B]" />
                                        </button>
                                    </div>

                                    {/* Focus Kalimantan Button */}
                                    <button
                                        type="button"
                                        onClick={handleFocusKalimantan}
                                        className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/95 text-[#1E242B] font-semibold text-xs shadow-md border border-black/10 hover:bg-white active:scale-95 transition-all backdrop-blur-md cursor-pointer"
                                        title="Fokuskan tampilan ke seluruh Kalimantan"
                                    >
                                        <Compass className="w-4 h-4 text-[#E05A2B]" />
                                        <span className="hidden sm:inline">Fokus Kalimantan</span>
                                    </button>

                                    {/* Reset View Button */}
                                    <button
                                        type="button"
                                        onClick={handleResetView}
                                        className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/95 text-[#1E242B] font-semibold text-xs shadow-md border border-black/10 hover:bg-white active:scale-95 transition-all backdrop-blur-md cursor-pointer"
                                        title="Kembalikan peta ke tampilan awal wilayah pemantauan"
                                    >
                                        <RotateCcw className="w-4 h-4 text-[#1E242B]" />
                                        <span className="hidden sm:inline">Reset View</span>
                                    </button>

                                    {/* Fullscreen Toggle */}
                                    <button
                                        type="button"
                                        onClick={handleToggleFullscreen}
                                        className="p-2 rounded-xl bg-white/95 text-[#1E242B] font-semibold text-xs shadow-md border border-black/10 hover:bg-white active:scale-95 transition-all backdrop-blur-md cursor-pointer"
                                        title={isFullscreen ? 'Keluar Layar Penuh' : 'Layar Penuh'}
                                        aria-label="Layar Penuh"
                                    >
                                        {isFullscreen ? <Minimize2 className="w-4 h-4" /> : <Maximize2 className="w-4 h-4" />}
                                    </button>
                                </div>

                                {/* FLOATING FILTER ACTIVE SUMMARY (Top Left) */}
                                <div className="absolute top-4 left-4 z-20 pointer-events-none flex flex-col gap-2 max-w-[280px] sm:max-w-md">
                                    <div className="pointer-events-auto inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#1E242B]/90 text-white backdrop-blur-md border border-white/15 shadow-md text-xs font-semibold">
                                        <span className="w-2 h-2 rounded-full bg-[#E05A2B] animate-pulse" />
                                        <span className="truncate">
                                            {selectedProvince
                                                ? provinces.find((p) => p.slug === selectedProvince)?.name || selectedProvince
                                                : 'Seluruh 5 Provinsi Kalimantan'}
                                        </span>
                                        <span className="text-white/30 font-normal">|</span>
                                        <span className="text-[#F4A261] font-bold shrink-0">
                                            {hotspots.length} Titik
                                        </span>
                                    </div>
                                </div>

                                {/* FLOATING MAP LEGEND (Bottom Left Capsule) */}
                                <div className="absolute bottom-4 left-4 z-20 pointer-events-none hidden sm:flex items-center gap-2 p-1.5 rounded-2xl bg-[#1E242B]/90 text-white backdrop-blur-md border border-white/15 text-[11px] shadow-lg">
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded-xl bg-white/10">
                                        <span className="w-2.5 h-2.5 rounded-full bg-[#E05A2B] border border-white shadow-xs" />
                                        <span className="font-semibold text-white">High (≥80%)</span>
                                    </div>
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded-xl bg-white/10">
                                        <span className="w-2.5 h-2.5 rounded-full bg-[#F4A261] border border-white shadow-xs" />
                                        <span className="font-semibold text-white">Nominal (60–79%)</span>
                                    </div>
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded-xl bg-white/10">
                                        <span className="w-2.5 h-2.5 rounded-full bg-[#52B788] border border-white shadow-xs" />
                                        <span className="font-semibold text-white">Low (&lt;60%)</span>
                                    </div>
                                    <div className="flex items-center gap-1.5 px-2 py-1 rounded-xl bg-white/10">
                                        <span className="w-3.5 h-3.5 rounded-full bg-[#E05A2B] border border-white flex items-center justify-center text-[9px] font-bold shadow-xs">K</span>
                                        <span className="text-white/80">Klaster Kerapatan</span>
                                    </div>
                                </div>

                                {/* MAP OVERLAY: LOADING STATE */}
                                <AnimatePresence>
                                    {loading && (
                                        <motion.div
                                            initial={{ opacity: 0 }}
                                            animate={{ opacity: 1 }}
                                            exit={{ opacity: 0 }}
                                            className="absolute inset-0 z-30 flex flex-col items-center justify-center bg-[#1E242B]/80 backdrop-blur-sm text-white"
                                        >
                                            <div className="p-6 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md flex flex-col items-center shadow-xl">
                                                <RefreshCw className="w-8 h-8 text-[#E05A2B] animate-spin mb-3" />
                                                <div className="font-bold text-sm">Menarik Telemetri Satelit…</div>
                                                <div className="text-xs text-white/70 mt-1">Menyinkronkan koordinat sensor polar</div>
                                            </div>
                                        </motion.div>
                                    )}
                                </AnimatePresence>

                                {/* MAP OVERLAY: ERROR STATE */}
                                {error && !loading && (
                                    <div className="absolute inset-0 z-30 flex flex-col items-center justify-center p-6 bg-[#1E242B]/90 backdrop-blur-md text-white text-center">
                                        <div className="w-12 h-12 rounded-2xl bg-[#E63946]/20 text-[#E63946] flex items-center justify-center mb-3">
                                            <AlertTriangle className="w-6 h-6 stroke-[2]" />
                                        </div>
                                        <h3 className="font-bold text-base mb-1">Kendala Pengambilan Data Spasial</h3>
                                        <p className="text-xs text-white/70 max-w-sm mb-4 leading-relaxed">
                                            {error}
                                        </p>
                                        <Button
                                            type="button"
                                            onClick={() =>
                                                fetchData({
                                                    provinsi: selectedProvince,
                                                    dari: fromDate,
                                                    sampai: toDate,
                                                    confidence: selectedConfidence,
                                                })
                                            }
                                            variant="primary"
                                            size="sm"
                                        >
                                            <RefreshCw className="w-3.5 h-3.5 mr-1.5" />
                                            <span>Muat Ulang Data</span>
                                        </Button>
                                    </div>
                                )}

                                {/* MAP OVERLAY: EMPTY STATE */}
                                {!loading && !error && hotspots.length === 0 && (
                                    <div className="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 bg-[#1E242B]/75 backdrop-blur-xs text-white text-center pointer-events-none">
                                        <div className="w-12 h-12 rounded-2xl bg-white/10 text-[#52B788] flex items-center justify-center mb-3">
                                            <CheckCircle2 className="w-6 h-6 stroke-[2]" />
                                        </div>
                                        <h3 className="font-bold text-base mb-1">Tidak Ditemukan Titik Panas</h3>
                                        <p className="text-xs text-white/70 max-w-sm mb-4 leading-relaxed">
                                            Tidak ada anomali termal yang terdeteksi untuk kriteria wilayah dan rentang waktu yang sedang dipilih.
                                        </p>
                                        <button
                                            type="button"
                                            onClick={handleResetFilter}
                                            className="pointer-events-auto inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white text-[#1E242B] text-xs font-bold shadow-md hover:bg-[#F6F5F2] active:scale-95 transition-all cursor-pointer"
                                        >
                                            <RotateCcw className="w-3.5 h-3.5" />
                                            <span>Reset Filter Parameter</span>
                                        </button>
                                    </div>
                                )}
                            </div>

                            {/* Scientific Disclaimer & Projection Note */}
                            <div className="flex flex-wrap items-center justify-between gap-3 mt-3 px-1 text-xs text-[#1E242B]/65">
                                <div className="flex items-center gap-1.5">
                                    <Shield className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Telemetri NASA FIRMS NRT diproyeksikan menggunakan datum WGS 84 (EPSG:4326).</span>
                                </div>
                                <span className="font-mono text-[11px] text-[#1E242B]/50">Proyeksi Web Mercator</span>
                            </div>
                        </div>

                        {/* RIGHT: DESKTOP CONTROL SIDEBAR (Filter & Detail Panels) */}
                        <aside className="lg:col-span-4 xl:col-span-3 order-2 space-y-5">
                            {/* 1. FILTER PANEL */}
                            <div className="p-5 sm:p-6 rounded-3xl bg-white border border-black/5 shadow-xs">
                                <form onSubmit={handleApplyFilter} aria-label="Filter Parameter Spasial">
                                    <div className="flex items-center justify-between pb-3 mb-4 border-b border-black/5">
                                        <div className="flex items-center gap-2">
                                            <Sliders className="w-4 h-4 text-[#E05A2B]" />
                                            <h2 className="text-sm font-bold text-[#1E242B] font-display">
                                                Filter Parameter
                                            </h2>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={handleResetFilter}
                                            className="text-xs text-[#1E242B]/50 hover:text-[#E05A2B] inline-flex items-center gap-1 font-semibold transition-colors cursor-pointer"
                                        >
                                            <RotateCcw className="w-3 h-3" />
                                            <span>Reset</span>
                                        </button>
                                    </div>

                                    {/* Province Selection */}
                                    <div className="mb-4">
                                        <label htmlFor="filterProvince" className="block text-xs font-semibold text-[#1E242B]/80 mb-1.5">
                                            Wilayah Pemantauan
                                        </label>
                                        <select
                                            id="filterProvince"
                                            value={selectedProvince}
                                            onChange={(e) => setSelectedProvince(e.target.value)}
                                            className="w-full text-xs font-semibold text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] cursor-pointer"
                                        >
                                            <option value="">Seluruh 5 Provinsi Kalimantan</option>
                                            {provinces.map((p) => (
                                                <option key={p.id} value={p.slug}>
                                                    {p.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    {/* Quick Date Presets */}
                                    <div className="mb-4">
                                        <label className="block text-xs font-semibold text-[#1E242B]/80 mb-1.5">
                                            Preset Waktu Deteksi
                                        </label>
                                        <div className="grid grid-cols-4 gap-1.5">
                                            {[
                                                { label: 'Hari Ini', days: 0 },
                                                { label: '3 Hari', days: 3 },
                                                { label: '7 Hari', days: 7 },
                                                { label: '14 Hari', days: 14 },
                                            ].map((preset) => (
                                                <button
                                                    key={preset.days}
                                                    type="button"
                                                    onClick={() => handlePresetClick(preset.days)}
                                                    className={cn(
                                                        'py-1.5 text-[11px] font-semibold rounded-lg border transition-all cursor-pointer text-center',
                                                        datePreset === preset.days
                                                            ? 'bg-[#1E242B] text-white border-[#1E242B] shadow-xs'
                                                            : 'bg-[#F6F5F2] text-[#1E242B]/70 border-black/5 hover:bg-black/5'
                                                    )}
                                                >
                                                    {preset.label}
                                                </button>
                                            ))}
                                        </div>
                                    </div>

                                    {/* Manual Date Range */}
                                    <div className="grid grid-cols-2 gap-2 mb-4">
                                        <div>
                                            <label htmlFor="filterFrom" className="block text-[11px] font-medium text-[#1E242B]/70 mb-1">
                                                Dari Tanggal
                                            </label>
                                            <input
                                                type="date"
                                                id="filterFrom"
                                                value={fromDate}
                                                onChange={(e) => {
                                                    setFromDate(e.target.value);
                                                    setDatePreset(null);
                                                }}
                                                className="w-full text-xs text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-[#E05A2B]"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="filterTo" className="block text-[11px] font-medium text-[#1E242B]/70 mb-1">
                                                Sampai Tanggal
                                            </label>
                                            <input
                                                type="date"
                                                id="filterTo"
                                                value={toDate}
                                                onChange={(e) => {
                                                    setToDate(e.target.value);
                                                    setDatePreset(null);
                                                }}
                                                className="w-full text-xs text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-[#E05A2B]"
                                            />
                                        </div>
                                    </div>

                                    {/* Confidence Category Filter */}
                                    <div className="mb-5">
                                        <div className="flex items-center justify-between mb-1.5">
                                            <label htmlFor="filterConfidence" className="block text-xs font-semibold text-[#1E242B]/80">
                                                Tingkat Keyakinan Sensor
                                            </label>
                                            <span className="text-[10px] text-[#1E242B]/50 font-medium">NASA FIRMS</span>
                                        </div>
                                        <select
                                            id="filterConfidence"
                                            value={selectedConfidence}
                                            onChange={(e) => setSelectedConfidence(e.target.value)}
                                            className="w-full text-xs font-semibold text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] cursor-pointer"
                                        >
                                            <option value="all">Semua Tingkat Keyakinan (0–100%)</option>
                                            <option value="high">High Confidence (≥ 80%)</option>
                                            <option value="nominal">Nominal Confidence (60–79%)</option>
                                            <option value="low">Low Confidence (&lt; 60%)</option>
                                        </select>
                                        <p className="mt-1.5 text-[10px] text-[#1E242B]/55 leading-tight">
                                            Menunjukkan kepastian sensor mendeteksi anomali suhu permukaan.
                                        </p>
                                    </div>

                                    {/* Submit Button */}
                                    <Button
                                        type="submit"
                                        variant="primary"
                                        size="md"
                                        loading={loading}
                                        className="w-full shadow-md shadow-[#E05A2B]/20 font-bold"
                                    >
                                        <Sliders className="w-4 h-4 mr-1.5" />
                                        <span>Terapkan Parameter</span>
                                    </Button>
                                </form>
                            </div>

                            {/* 2. DETAIL HOTSPOT PANEL (DESKTOP) */}
                            <div className="hidden lg:block p-5 sm:p-6 rounded-3xl bg-white border border-black/5 shadow-xs">
                                <div className="flex items-center justify-between pb-3 mb-4 border-b border-black/5">
                                    <div className="flex items-center gap-2">
                                        <MapPin className="w-4 h-4 text-[#E05A2B]" />
                                        <h2 className="text-sm font-bold text-[#1E242B] font-display">
                                            Detail Titik Hotspot
                                        </h2>
                                    </div>
                                    {selectedHotspot && (
                                        <button
                                            type="button"
                                            onClick={handleClearSelection}
                                            className="text-[#1E242B]/40 hover:text-[#1E242B] p-1 rounded-lg transition-colors cursor-pointer"
                                            aria-label="Tutup detail"
                                            title="Tutup detail seleksi"
                                        >
                                            <X className="w-4 h-4" />
                                        </button>
                                    )}
                                </div>

                                {selectedHotspot ? (
                                    <div className="space-y-3 text-xs">
                                        {/* Hotspot ID & Province Header */}
                                        {(() => {
                                            const conf = getConfidenceCategory(selectedHotspot.confidence);
                                            return (
                                                <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5 flex items-center justify-between">
                                                    <div>
                                                        <span className="text-[10px] font-bold text-[#E05A2B] uppercase tracking-wider block">
                                                            Hotspot #{selectedHotspot.id}
                                                        </span>
                                                        <strong className="text-sm text-[#1E242B] font-display">
                                                            {selectedHotspot.province || 'Kalimantan'}
                                                        </strong>
                                                    </div>
                                                    <span
                                                        className="px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                        style={{
                                                            background: `${conf.color}15`,
                                                            color: conf.color,
                                                            borderColor: `${conf.color}35`,
                                                        }}
                                                    >
                                                        {conf.label} ({selectedHotspot.confidence ?? '?'}%)
                                                    </span>
                                                </div>
                                            );
                                        })()}

                                        {/* Coordinates with Copy Button */}
                                        <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5 flex items-center justify-between">
                                            <div>
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    Koordinat Geografis (Lat, Lng)
                                                </span>
                                                <span className="font-mono text-xs font-semibold text-[#1E242B]">
                                                    {formatCoord(Number(selectedHotspot.lat))}, {formatCoord(Number(selectedHotspot.lng))}
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => handleCopyCoord(Number(selectedHotspot.lat), Number(selectedHotspot.lng))}
                                                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-black/10 text-[11px] font-semibold text-[#1E242B] hover:bg-black/5 active:scale-95 transition-all cursor-pointer"
                                                title="Salin koordinat"
                                            >
                                                {copied ? <Check className="w-3 h-3 text-[#2A9D8F]" /> : <Copy className="w-3 h-3 text-[#E05A2B]" />}
                                                <span>{copied ? 'Tersalin' : 'Salin'}</span>
                                            </button>
                                        </div>

                                        {/* Date and Time in WITA */}
                                        <div className="grid grid-cols-2 gap-2">
                                            <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    Tanggal Deteksi
                                                </span>
                                                <span className="font-semibold text-[#1E242B] truncate block">
                                                    {formatDateOnly(selectedHotspot.detected_at)}
                                                </span>
                                            </div>
                                            <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    Waktu (WITA)
                                                </span>
                                                <span className="font-semibold text-[#1E242B] truncate block">
                                                    {formatTimeOnly(selectedHotspot.detected_at)}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Sensor & FRP */}
                                        <div className="grid grid-cols-2 gap-2">
                                            <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    Sensor Satelit
                                                </span>
                                                <span className="font-semibold text-[#1E242B] truncate block">
                                                    {selectedHotspot.sensor || 'VIIRS / MODIS'}
                                                </span>
                                            </div>
                                            <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    FRP Daya Panas
                                                </span>
                                                <span className="font-semibold text-[#E05A2B] block">
                                                    {selectedHotspot.frp != null ? `${Number(selectedHotspot.frp).toLocaleString('id-ID')} MW` : '—'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Brightness Temperature (Only if available) */}
                                        {selectedHotspot.brightness != null && (
                                            <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                                <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                                    Suhu Kecerahan (Brightness)
                                                </span>
                                                <span className="font-semibold text-[#1E242B] block">
                                                    {Number(selectedHotspot.brightness).toFixed(1)} Kelvin
                                                </span>
                                            </div>
                                        )}

                                        {/* Data Source & Focus Button */}
                                        <div className="pt-2 flex items-center justify-between text-[11px] text-[#1E242B]/60">
                                            <span className="truncate max-w-[150px]" title={sourceLabel(selectedHotspot.source)}>
                                                Sumber: {sourceLabel(selectedHotspot.source)}
                                            </span>
                                            <button
                                                type="button"
                                                onClick={() => handleFocusHotspot(selectedHotspot)}
                                                className="text-[#E05A2B] font-bold hover:underline inline-flex items-center gap-1 cursor-pointer"
                                            >
                                                <span>Fokuskan Lokasi</span>
                                                <ArrowUpRight className="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="py-8 text-center text-[#1E242B]/60 flex flex-col items-center justify-center">
                                        <Crosshair className="w-8 h-8 text-[#1E242B]/30 mb-2 stroke-[1.5]" />
                                        <p className="text-xs font-medium max-w-[200px] leading-relaxed">
                                            Pilih salah satu titik panas pada peta untuk membedah telemetri sensor satelit secara mendalam.
                                        </p>
                                    </div>
                                )}
                            </div>
                        </aside>
                    </div>
                </main>
            </div>

            {/* 4. MOBILE OFFCANVAS FILTER DRAWER */}
            <AnimatePresence>
                {mobileFilterOpen && (
                    <div className="fixed inset-0 z-50 lg:hidden">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setMobileFilterOpen(false)}
                            className="fixed inset-0 bg-[#1E242B]/75 backdrop-blur-sm"
                            aria-hidden="true"
                        />
                        <motion.div
                            initial={{ x: '100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '100%' }}
                            transition={{ type: 'spring', damping: 28, stiffness: 300 }}
                            className="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto"
                        >
                            <div>
                                <div className="flex items-center justify-between pb-4 mb-5 border-b border-black/10">
                                    <div className="flex items-center gap-2">
                                        <Sliders className="w-5 h-5 text-[#E05A2B]" />
                                        <h3 className="font-bold text-base font-display text-[#1E242B]">
                                            Filter Parameter Spasial
                                        </h3>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setMobileFilterOpen(false)}
                                        className="p-1.5 rounded-xl text-[#1E242B]/60 hover:text-[#1E242B] hover:bg-black/5 cursor-pointer"
                                        aria-label="Tutup filter"
                                    >
                                        <X className="w-5 h-5" />
                                    </button>
                                </div>

                                <form onSubmit={handleApplyFilter} className="space-y-4">
                                    <div>
                                        <label className="block text-xs font-semibold text-[#1E242B]/80 mb-1">
                                            Wilayah Provinsi
                                        </label>
                                        <select
                                            value={selectedProvince}
                                            onChange={(e) => setSelectedProvince(e.target.value)}
                                            className="w-full text-xs font-semibold text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-3 py-2.5"
                                        >
                                            <option value="">Seluruh 5 Provinsi Kalimantan</option>
                                            {provinces.map((p) => (
                                                <option key={p.id} value={p.slug}>
                                                    {p.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-[#1E242B]/80 mb-1">
                                            Preset Waktu Cepat
                                        </label>
                                        <div className="grid grid-cols-4 gap-1.5">
                                            {[
                                                { label: 'Hari Ini', days: 0 },
                                                { label: '3 Hari', days: 3 },
                                                { label: '7 Hari', days: 7 },
                                                { label: '14 Hari', days: 14 },
                                            ].map((preset) => (
                                                <button
                                                    key={preset.days}
                                                    type="button"
                                                    onClick={() => handlePresetClick(preset.days)}
                                                    className={cn(
                                                        'py-1.5 text-[11px] font-semibold rounded-lg border text-center cursor-pointer',
                                                        datePreset === preset.days
                                                            ? 'bg-[#1E242B] text-white border-[#1E242B]'
                                                            : 'bg-[#F6F5F2] text-[#1E242B]/70 border-black/5'
                                                    )}
                                                >
                                                    {preset.label}
                                                </button>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <label className="block text-[11px] font-medium text-[#1E242B]/70 mb-1">Dari Tanggal</label>
                                            <input
                                                type="date"
                                                value={fromDate}
                                                onChange={(e) => setFromDate(e.target.value)}
                                                className="w-full text-xs text-[#1E242B] bg-[#F6F5F2] border border-black/10 rounded-xl p-2"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-[11px] font-medium text-[#1E242B]/70 mb-1">Sampai</label>
                                            <input
                                                type="date"
                                                value={toDate}
                                                onChange={(e) => setToDate(e.target.value)}
                                                className="w-full text-xs text-[#1E242B] bg-[#F6F5F2] border border-black/10 rounded-xl p-2"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-semibold text-[#1E242B]/80 mb-1">
                                            Tingkat Keyakinan (Confidence)
                                        </label>
                                        <select
                                            value={selectedConfidence}
                                            onChange={(e) => setSelectedConfidence(e.target.value)}
                                            className="w-full text-xs font-semibold text-[#1E242B] bg-[#F6F5F2] border border-black/10 rounded-xl p-2.5"
                                        >
                                            <option value="all">Semua Ambang Batas (0–100%)</option>
                                            <option value="high">High Confidence (≥ 80%)</option>
                                            <option value="nominal">Nominal Confidence (60–79%)</option>
                                            <option value="low">Low Confidence (&lt; 60%)</option>
                                        </select>
                                    </div>

                                    <div className="pt-4 flex flex-col gap-2">
                                        <Button type="submit" variant="primary" size="md" className="w-full font-bold">
                                            <span>Terapkan Parameter</span>
                                        </Button>
                                        <Button type="button" onClick={handleResetFilter} variant="outline" size="md" className="w-full">
                                            <span>Reset Default</span>
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            {/* 5. MOBILE DETAIL BOTTOM SHEET */}
            <AnimatePresence>
                {mobileDetailOpen && selectedHotspot && (
                    <div className="fixed inset-0 z-50 lg:hidden">
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setMobileDetailOpen(false)}
                            className="fixed inset-0 bg-[#1E242B]/60 backdrop-blur-xs"
                            aria-hidden="true"
                        />
                        <motion.div
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', damping: 28, stiffness: 300 }}
                            className="fixed inset-x-0 bottom-0 max-h-[85vh] bg-white rounded-t-3xl shadow-2xl p-6 overflow-y-auto"
                        >
                            <div className="w-12 h-1 bg-black/15 rounded-full mx-auto mb-4" />
                            <div className="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
                                <div className="flex items-center gap-2">
                                    <MapPin className="w-4 h-4 text-[#E05A2B]" />
                                    <h3 className="font-bold text-base font-display text-[#1E242B]">
                                        Telemetri Titik Panas
                                    </h3>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setMobileDetailOpen(false)}
                                    className="p-1 rounded-lg text-[#1E242B]/60 hover:text-[#1E242B]"
                                    aria-label="Tutup detail"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            <div className="space-y-3 text-xs">
                                {(() => {
                                    const conf = getConfidenceCategory(selectedHotspot.confidence);
                                    return (
                                        <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5 flex items-center justify-between">
                                            <div>
                                                <span className="text-[10px] font-bold text-[#E05A2B] uppercase tracking-wider block">
                                                    Hotspot #{selectedHotspot.id}
                                                </span>
                                                <strong className="text-sm text-[#1E242B] font-display">
                                                    {selectedHotspot.province || 'Kalimantan'}
                                                </strong>
                                            </div>
                                            <span
                                                className="px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                style={{
                                                    background: `${conf.color}15`,
                                                    color: conf.color,
                                                    borderColor: `${conf.color}35`,
                                                }}
                                            >
                                                {conf.label} ({selectedHotspot.confidence ?? '?'}%)
                                            </span>
                                        </div>
                                    );
                                })()}

                                <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5 flex items-center justify-between">
                                    <div>
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            Koordinat Geografis
                                        </span>
                                        <span className="font-mono text-xs font-semibold text-[#1E242B]">
                                            {formatCoord(Number(selectedHotspot.lat))}, {formatCoord(Number(selectedHotspot.lng))}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => handleCopyCoord(Number(selectedHotspot.lat), Number(selectedHotspot.lng))}
                                        className="px-3 py-1.5 rounded-lg bg-white border border-black/10 text-xs font-semibold text-[#1E242B] active:scale-95 transition-all cursor-pointer"
                                    >
                                        {copied ? 'Tersalin!' : 'Salin'}
                                    </button>
                                </div>

                                <div className="grid grid-cols-2 gap-2">
                                    <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            Tanggal Deteksi
                                        </span>
                                        <span className="font-semibold text-[#1E242B] truncate block">
                                            {formatDateOnly(selectedHotspot.detected_at)}
                                        </span>
                                    </div>
                                    <div className="p-3.5 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            Waktu (WITA)
                                        </span>
                                        <span className="font-semibold text-[#1E242B] truncate block">
                                            {formatTimeOnly(selectedHotspot.detected_at)}
                                        </span>
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-2">
                                    <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            Sensor
                                        </span>
                                        <span className="font-semibold text-[#1E242B] truncate block">
                                            {selectedHotspot.sensor || 'VIIRS / MODIS'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            FRP Panas
                                        </span>
                                        <span className="font-semibold text-[#E05A2B] block">
                                            {selectedHotspot.frp != null ? `${Number(selectedHotspot.frp).toLocaleString('id-ID')} MW` : '—'}
                                        </span>
                                    </div>
                                </div>

                                {selectedHotspot.brightness != null && (
                                    <div className="p-3 rounded-2xl bg-[#F6F5F2] border border-black/5">
                                        <span className="text-[10px] font-bold text-[#1E242B]/50 uppercase tracking-wider block">
                                            Suhu Kecerahan
                                        </span>
                                        <span className="font-semibold text-[#1E242B] block">
                                            {Number(selectedHotspot.brightness).toFixed(1)} Kelvin
                                        </span>
                                    </div>
                                )}

                                <div className="pt-2 flex items-center justify-between">
                                    <span className="text-[11px] text-[#1E242B]/60 truncate max-w-[150px]">
                                        Sumber: {sourceLabel(selectedHotspot.source)}
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setMobileDetailOpen(false);
                                            handleFocusHotspot(selectedHotspot);
                                        }}
                                        className="text-[#E05A2B] font-bold hover:underline inline-flex items-center gap-1"
                                    >
                                        <span>Fokuskan Lokasi</span>
                                        <ArrowUpRight className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </PublicLayout>
    );
}
