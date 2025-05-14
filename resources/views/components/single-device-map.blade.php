<!-- Map Container -->
<div wire:ignore id="map-{{ $deviceId }}"
    class="map-container flex flex-col rounded-sm w-full h-full p-1 shadow-sm cursor-pointer transition-all duration-500 ease-in-out"
    onclick="toggleMapSize(event, '{{ $deviceId }}')">


    <div wire:ignore class="backdrop backdrop-{{ $deviceId }}" onclick="toggleMapSize(event, '{{ $deviceId }}')">
    </div>
</div>


@push('styles')
    <style>
        .map-container {
            width: 100%;
            height: 100%;
        }

        .expanded-{{ $deviceId }} {
            position: fixed !important;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70vw;
            height: 70vh;
            z-index: 1000;
        }

        .backdrop-{{ $deviceId }} {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .expanded-{{ $deviceId }}~.backdrop-{{ $deviceId }} {
            display: block;
        }
    </style>
@endpush


{{-- gpt-1 --}}
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
        const maps = {}; // Store maps per device

        function initializeMapForDevice(deviceId, initialLocations) {
            const container = document.getElementById(`map-${deviceId}`);
            if (!container) {
                console.warn(`Map container #map-${deviceId} not found.`);
                return;
            }

            const lat = parseFloat(initialLocations[deviceId]?.latitude ?? -6.774418);
            const lng = parseFloat(initialLocations[deviceId]?.longitude ?? 39.241196);

            const map = L.map(container).setView([lat, lng], 10);
            maps[deviceId] = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: red; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white;"></div>',
                    iconSize: [15, 15],
                    iconAnchor: [7.5, 7.5]
                })
            }).addTo(map).bindPopup(deviceId);

            map.invalidateSize();

            window.Livewire.on('locationsUpdated', ({ locations }) => {
                const loc = locations[deviceId];
                if (loc && typeof loc.latitude === 'number' && typeof loc.longitude === 'number') {
                    const latLng = [parseFloat(loc.latitude), parseFloat(loc.longitude)];
                    marker.setLatLng(latLng);
                    map.setView(latLng, 15);
                }
            });
        }

        function toggleMapSize(event, deviceId) {
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);

            mapContainer.classList.toggle(`expanded-${deviceId}`);
            const isExpanded = mapContainer.classList.contains(`expanded-${deviceId}`);
            backdrop.style.display = isExpanded ? 'block' : 'none';

            if (isExpanded) {
                setTimeout(() => {
                    maps[deviceId]?.invalidateSize();
                }, 300);
            }

            event.stopPropagation();
        }

        // Hide map if clicked outside
        document.addEventListener('click', function(event) {
            const mapContainer = document.getElementById(`map-{{ $deviceId }}`);
            const backdrop = document.querySelector(`.backdrop-{{ $deviceId }}`);
            if (mapContainer?.classList.contains(`expanded-{{ $deviceId }}`) && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove(`expanded-{{ $deviceId }}`);
                backdrop.style.display = 'none';
            }
        });

        window.addEventListener('load', function () {
            initializeMapForDevice(`{{ $deviceId }}`, @json($locations));
        });
    </script>
@endpush
