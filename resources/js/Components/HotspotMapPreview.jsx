import React, { useEffect, useRef, useState } from 'react';
import { cn } from '@/Lib/utils';
import { Flame, Shield } from 'lucide-react';

export function HotspotMapPreview({
    hotspots = [],
    activeProvince = null,
    className,
}) {
    const mapContainerRef = useRef(null);
    const mapInstanceRef = useRef(null);
    const markersLayerRef = useRef(null);
    const [mapLoaded, setMapLoaded] = useState(false);
    const [mapError, setMapError] = useState(false);

    useEffect(() => {
        if (typeof window === 'undefined') return;

        let isMounted = true;

        async function initMap() {
            try {
                const LModule = await import('leaflet');
                const L = LModule.default || LModule;
                await import('leaflet/dist/leaflet.css');

                if (!mapContainerRef.current || !isMounted) return;

                // If already initialized, destroy before re-creating
                if (mapInstanceRef.current) {
                    mapInstanceRef.current.remove();
                    mapInstanceRef.current = null;
                }

                // Default center: Kalimantan center
                let center = [-0.7893, 113.9213];
                let zoom = 6;

                if (activeProvince && activeProvince.center_lat && activeProvince.center_lng) {
                    center = [parseFloat(activeProvince.center_lat), parseFloat(activeProvince.center_lng)];
                    zoom = 7;
                }

                const map = L.map(mapContainerRef.current, {
                    center,
                    zoom,
                    zoomControl: false,
                    scrollWheelZoom: false,
                    attributionControl: false,
                });

                // Zoom control at bottom right
                L.control.zoom({ position: 'bottomright' }).addTo(map);

                // OpenStreetMap standard tile layer (reliable, zero API key required, no watermarks)
                const tileLayer = L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors | NASA FIRMS',
                    }
                ).addTo(map);

                tileLayer.on('tileerror', () => {
                    if (isMounted) {
                        setMapError(true);
                    }
                });

                const markersLayer = L.layerGroup().addTo(map);
                markersLayerRef.current = markersLayer;
                mapInstanceRef.current = map;

                renderMarkers(L, map, markersLayer, hotspots);

                setMapLoaded(true);
                setMapError(false);

                // Ensure map container renders without grey tiles
                setTimeout(() => {
                    if (map && isMounted) {
                        map.invalidateSize();
                    }
                }, 300);
            } catch (err) {
                console.error('Gagal memuat peta Leaflet:', err);
                if (isMounted) {
                    setMapError(true);
                }
            }
        }

        initMap();

        return () => {
            isMounted = false;
            if (mapInstanceRef.current) {
                mapInstanceRef.current.remove();
                mapInstanceRef.current = null;
            }
        };
    }, [activeProvince]);

    // Update markers when hotspots array updates
    useEffect(() => {
        if (!mapInstanceRef.current || !markersLayerRef.current) return;

        import('leaflet').then((LModule) => {
            const L = LModule.default;
            if (markersLayerRef.current && mapInstanceRef.current) {
                markersLayerRef.current.clearLayers();
                renderMarkers(L, mapInstanceRef.current, markersLayerRef.current, hotspots);
            }
        });
    }, [hotspots]);

    function renderMarkers(L, map, layer, points) {
        if (!points || points.length === 0) return;

        points.forEach((point) => {
            const lat = parseFloat(point.lat || point.latitude);
            const lng = parseFloat(point.lng || point.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            const conf = point.confidence || 0;
            const color =
                conf >= 80 ? '#E05A2B' : conf >= 60 ? '#F4A261' : '#2A9D8F';

            const circle = L.circleMarker([lat, lng], {
                radius: conf >= 80 ? 7 : 5,
                fillColor: color,
                color: '#FFFFFF',
                weight: 1.5,
                opacity: 0.9,
                fillOpacity: 0.85,
            });

            const popupContent = `
                <div style="font-family: 'Inter', sans-serif; font-size: 12px; color: #1E242B; padding: 4px;">
                    <div style="font-weight: 700; color: #1B4332; margin-bottom: 2px;">
                        ${point.province || 'Titik Panas Kalimantan'}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 4px;">
                        ${lat.toFixed(4)}, ${lng.toFixed(4)}
                    </div>
                    <div style="display: inline-block; background: ${color}20; color: ${color}; font-weight: 700; font-size: 10px; padding: 2px 6px; border-radius: 9999px;">
                        Tingkat Kepercayaan: ${conf}%
                    </div>
                </div>
            `;

            circle.bindPopup(popupContent);
            circle.addTo(layer);
        });
    }

    return (
        <div className={cn('relative w-full h-[360px] sm:h-[440px] rounded-2xl sm:rounded-3xl overflow-hidden border border-black/10 shadow-xl bg-[#1E242B]', className)}>
            <div ref={mapContainerRef} className="w-full h-full z-10" />

            {/* Overlaid Map Legend & Status (No Bullet Dots) */}
            <div className="absolute top-4 left-4 z-20 pointer-events-none flex flex-col gap-2">
                <div className="pointer-events-auto inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/95 backdrop-blur-md border border-black/5 shadow-sm text-xs font-semibold text-[#1E242B]">
                    <Flame className="w-3.5 h-3.5 text-[#E05A2B]" />
                    <span>
                        {activeProvince ? activeProvince.name : 'Seluruh Wilayah Kalimantan'}
                    </span>
                    <span className="text-black/30 font-normal">|</span>
                    <span className="text-[#E05A2B] font-bold">
                        {hotspots.length} Titik Panas
                    </span>
                </div>
            </div>

            {/* Bottom Overlay Legend (No Bullet Dots) */}
            <div className="absolute bottom-4 left-4 z-20 pointer-events-none hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#1E242B]/90 text-white backdrop-blur-md border border-white/10 text-[11px]">
                <span className="px-2 py-0.5 rounded-md bg-[#E05A2B] text-white font-semibold">Tinggi ≥80%</span>
                <span className="px-2 py-0.5 rounded-md bg-[#F4A261] text-[#1E242B] font-semibold">Sedang 60-79%</span>
                <span className="px-2 py-0.5 rounded-md bg-[#2A9D8F] text-white font-semibold">Rendah &lt;60%</span>
            </div>

            {/* Error state if tiles / map failed to load */}
            {mapError && (
                <div className="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 bg-[#1E242B]/85 backdrop-blur-xs text-center text-white pointer-events-none">
                    <div className="w-12 h-12 rounded-2xl bg-white/10 text-[#E05A2B] flex items-center justify-center mb-3">
                        <Shield className="w-6 h-6 stroke-[2]" />
                    </div>
                    <p className="text-sm font-semibold">Gagal Memuat Lapisan Peta</p>
                    <p className="text-xs text-white/70 max-w-xs mt-1">
                        Koneksi ke lapisan peta mengalami kendala. Data telemetri titik panas tetap tercatat secara akurat di sistem.
                    </p>
                </div>
            )}

            {/* Empty fallback if no hotspots */}
            {!mapError && hotspots.length === 0 && mapLoaded && (
                <div className="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 bg-[#1E242B]/60 backdrop-blur-xs text-center text-white pointer-events-none">
                    <div className="w-12 h-12 rounded-2xl bg-white/10 text-[#52B788] flex items-center justify-center mb-3">
                        <Shield className="w-6 h-6 stroke-[2]" />
                    </div>
                    <p className="text-sm font-semibold">Tidak Ada Titik Panas Aktif</p>
                    <p className="text-xs text-white/70 max-w-xs mt-1">
                        Satelit tidak mendeteksi anomali termal dalam 7 hari terakhir di wilayah ini.
                    </p>
                </div>
            )}
        </div>
    );
}

