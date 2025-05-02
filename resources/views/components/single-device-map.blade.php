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

{{--
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>Uncaught Error: Container 'map' not found.
    Map map.ts:667
    <anonymous> 1:425

        function initializeMapForDevice(deviceId, initialLocations) {
            console.log(`Initializing Leaflet for ${deviceId}...`);
            const map = L.map(`map-${deviceId}`).setView(
                [initialLocations[deviceId]?.latitude || -6.774418233335669, initialLocations[deviceId]?.longitude || 39.241196125639995],
                15
            );

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            map.invalidateSize();

            const markers = {};

            function updateMarkers(locations) {
                console.log(`Updating markers for ${deviceId}:`, JSON.stringify(locations));
                const location = locations[deviceId];
                if (location && typeof location.latitude === 'number' && typeof location.longitude === 'number') {
                    const latLng = [location.latitude, location.longitude];
                    if (markers[deviceId]) {
                        markers[deviceId].setLatLng(latLng);
                        console.log(`Updated marker ${deviceId} to [${latLng[0]}, ${latLng[1]}]`);
                    } else {
                        markers[deviceId] = L.marker(latLng, {
                            icon: L.divIcon({
                                className: 'custom-marker',
                                html: '<div style="background-color: red; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white;"></div>',
                                iconSize: [15, 15],
                                iconAnchor: [7.5, 7.5]
                            })
                        })
                            .addTo(map)
                            .bindPopup(deviceId);
                        console.log(`Created marker ${deviceId} at [${latLng[0]}, ${latLng[1]}]`);
                    }
                    map.setView(latLng, 15);
                } else {
                    console.warn(`Invalid coordinates for ${deviceId}:`, location);
                }
            }

            const currentLocations = initialLocations || {};
            if (currentLocations[deviceId]) {
                currentLocations[deviceId].latitude = parseFloat(currentLocations[deviceId].latitude);
                currentLocations[deviceId].longitude = parseFloat(currentLocations[deviceId].longitude);
            }
            updateMarkers(currentLocations);

            window.Livewire.on('locationsUpdated', ({ locations }) => {
                console.log(`Livewire locationsUpdated received for ${deviceId}:`, JSON.stringify(locations));
                if (locations[deviceId]) {
                    locations[deviceId].latitude = parseFloat(locations[deviceId].latitude);
                    locations[deviceId].longitude = parseFloat(locations[deviceId].longitude);
                    currentLocations[deviceId] = locations[deviceId];
                    updateMarkers(currentLocations);
                }
            });

            return map;
        }

        function toggleMapSize(event, deviceId) {
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);
            mapContainer.classList.toggle(`expanded-${deviceId}`);
            backdrop.style.display = mapContainer.classList.contains(`expanded-${deviceId}`) ? 'block' : 'none';
            if (mapContainer.classList.contains(`expanded-${deviceId}`)) {
                const map = L.map(`map-${deviceId}`);
                map.invalidateSize();
            }
            event.stopPropagation();
        }

        document.addEventListener('click', function(event) {
            const mapContainer = document.getElementById(`map-@json($deviceId)`);
            const backdrop = document.querySelector(`.backdrop-@json($deviceId)`);
            if (mapContainer.classList.contains(`expanded-@json($deviceId)`) && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove(`expanded-@json($deviceId)`);
                backdrop.style.display = 'none';
            }
        });

        window.addEventListener('livewire:initialized', function () {
            initializeMapForDevice(@json($deviceId), @json($locations));
        });

        console.log('Map container:', document.getElementById(`map-@json($deviceId)`));

        console.log('Leaflet loaded:', typeof L);

        console.log('Classes:', document.getElementById(`map-@json($deviceId)`).classList);
    </script>
@endpush --}}


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


{{-- @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script>
        const maps = {}; // Track map instances by deviceId
        const markers = {}; // Track marker instances

        function initializeMapForDevice(deviceId, initialLocations) {
            const mapContainer = document.getElementById(`map-${deviceId}`);
            if (!mapContainer) {
                console.warn(`Map container for ${deviceId} not found.`);
                return;
            }

            // Remove old map instance if it exists
            // if (maps[deviceId]) {
            //     maps[deviceId].remove();
            //     delete maps[deviceId];
            //     delete markers[deviceId];
            // }

            const location = initialLocations[deviceId] || {};
            const latitude = parseFloat(location.latitude) || -6.774418;
            const longitude = parseFloat(location.longitude) || 39.241196;




            // leaflet maps
            const map = L.map(`map-${deviceId}`).setView([latitude, longitude], 10);
            maps[deviceId] = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            map.invalidateSize();

            markers[deviceId] = L.marker([latitude, longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: red; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white;"></div>',
                    iconSize: [15, 15],
                    iconAnchor: [7.5, 7.5]
                })
            }).addTo(map).bindPopup(deviceId);

            window.Livewire.on('locationsUpdated', ({
                locations
            }) => {
                const loc = locations[deviceId];
                if (loc && typeof loc.latitude === 'number' && typeof loc.longitude === 'number') {
                    const latLng = [parseFloat(loc.latitude), parseFloat(loc.longitude)];
                    markers[deviceId].setLatLng(latLng);
                    map.setView(latLng, 15);
                }
            });
        }

        function toggleMapSize(event, deviceId) {
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);

            mapContainer.classList.toggle(`expanded-${deviceId}`);
            const expanded = mapContainer.classList.contains(`expanded-${deviceId}`);
            backdrop.style.display = expanded ? 'block' : 'none';

            if (expanded && maps[deviceId]) {
                maps[deviceId].invalidateSize();
            }

            event.stopPropagation();
        }

        // Close expanded map if clicked outside
        document.addEventListener('click', function(event) {
            const deviceId = @json($deviceId);
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);

            if (mapContainer && mapContainer.classList.contains(`expanded-${deviceId}`) && !mapContainer.contains(
                    event.target)) {
                mapContainer.classList.remove(`expanded-${deviceId}`);
                if (backdrop) backdrop.style.display = 'none';
            }
        });

        // Init on first load
        window.addEventListener('DOMContentLoaded', function() {
            const deviceId = @json($deviceId);
            const locations = @json($locations);
            initializeMapForDevice(deviceId, locations);
        });

        // Re-init after Livewire updates
        Livewire.hook('message.processed', (message, component) => {
            const deviceId = @json($deviceId);
            const locations = @json($locations);
            initializeMapForDevice(deviceId, locations);
        });
    </script>
@endpush --}}


{{-- @push('scripts')
    <!-- Mapbox GL JS CDN -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet" />

    <script>
        const maps = {};
        const markers = {};

        function initializeMapForDevice(deviceId, initialLocations) {
            const containerId = `map-${deviceId}`;
            const mapContainer = document.getElementById(containerId);
            if (!mapContainer) {
                console.warn(`Map container #${containerId} not found.`);
                return;
            }

            // Remove old map instance if it exists
            if (maps[deviceId]) {
                maps[deviceId].remove();
                delete maps[deviceId];
                delete markers[deviceId];
            }

            const location = initialLocations[deviceId] || {};
            const latitude = parseFloat(location.latitude) || -6.774418;
            const longitude = parseFloat(location.longitude) || 39.241196;

            mapboxgl.accessToken = 'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';

            const map = new mapboxgl.Map({
                container: containerId,
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [longitude, latitude], // Mapbox uses [lng, lat]
                zoom: 15
            });

            maps[deviceId] = map;

            const marker = new mapboxgl.Marker({ color: 'red' })
                .setLngLat([longitude, latitude])
                .addTo(map);
            markers[deviceId] = marker;

            map.addControl(new mapboxgl.NavigationControl());
            map.on('style.load', () => map.setFog({}));

            // Handle real-time marker update via Livewire event
            window.Livewire.on('locationsUpdated', ({ locations }) => {
                const loc = locations[deviceId];
                if (loc && typeof loc.latitude === 'number' && typeof loc.longitude === 'number') {
                    const newLngLat = [parseFloat(loc.longitude), parseFloat(loc.latitude)];
                    if (markers[deviceId]) {
                        markers[deviceId].setLngLat(newLngLat);
                        maps[deviceId].setCenter(newLngLat);
                    }
                }
            });
        }

        function toggleMapSize(event, deviceId) {
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);

            mapContainer.classList.toggle(`expanded-${deviceId}`);
            const expanded = mapContainer.classList.contains(`expanded-${deviceId}`);
            backdrop.style.display = expanded ? 'block' : 'none';

            if (expanded && maps[deviceId]) {
                maps[deviceId].resize(); // mapbox equivalent of Leaflet's invalidateSize()
            }

            event.stopPropagation();
        }

        // Close expanded map if clicked outside
        document.addEventListener('click', function (event) {
            const deviceId = @json($deviceId);
            const mapContainer = document.getElementById(`map-${deviceId}`);
            const backdrop = document.querySelector(`.backdrop-${deviceId}`);

            if (mapContainer && mapContainer.classList.contains(`expanded-${deviceId}`) && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove(`expanded-${deviceId}`);
                if (backdrop) backdrop.style.display = 'none';
            }
        });

        // Init on first load
        window.addEventListener('DOMContentLoaded', function () {
            const deviceId = @json($deviceId);
            const locations = @json($locations);
            initializeMapForDevice(deviceId, locations);
        });

        // Re-init after Livewire updates
        Livewire.hook('message.processed', (message, component) => {
            const deviceId = @json($deviceId);
            const locations = @json($locations);
            initializeMapForDevice(deviceId, locations);
        });
    </script>
@endpush --}}
