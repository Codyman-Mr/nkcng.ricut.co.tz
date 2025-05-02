<!-- resources/views/livewire/user-location-tracker.blade.php -->
<div>
    <h2>User Location Tracker</h2>
    @if($deviceId)
        <div wire:ignore class="bg-white dark:bg-gray-700 text-[#2E4057] dark:text-white rounded-lg p-1 shadow-sm">
            <div id="map-{{ $userId }}" class="mini-map rounded-2xl w-full h-[200px] p-1 shadow-sm cursor-pointer transition-all duration-500 ease-in-out"
                 onclick="toggleMapSize('map-{{ $userId }}')"></div>
        </div>
        <div id="backdrop-{{ $userId }}" class="backdrop" style="display: none;" onclick="toggleMapSize('map-{{ $userId }}')"></div>
    @else
        <p>No GPS device assigned to this user.</p>
    @endif
</div>



@push('scripts')
    <script>
        mapboxgl.accessToken = '{{ env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg') }}';

        window.addEventListener('livewire:initialized', function () {
            @if($deviceId)
                console.log('Initializing map for user {{ $userId }}...');
                var map = new mapboxgl.Map({
                    container: 'map-{{ $userId }}',
                    style: document.documentElement.classList.contains('dark') ? 'mapbox://styles/mapbox/dark-v11' : 'mapbox://styles/mapbox/streets-v12',
                    center: [{{ $locations[$deviceId]['longitude'] ?? 0 }}, {{ $locations[$deviceId]['latitude'] ?? 0 }}],
                    zoom: 14
                });

                map.addControl(new mapboxgl.NavigationControl());
                map.on('style.load', () => map.setFog({}));
                map._mapbox = map;

                var marker = null;
                var currentLocations = @json($locations);

                for (var deviceId in currentLocations) {
                    currentLocations[deviceId].latitude = parseFloat(currentLocations[deviceId].latitude);
                    currentLocations[deviceId].longitude = parseFloat(currentLocations[deviceId].longitude);
                }

                function updateMarker(locations) {
                    var deviceId = '{{ $deviceId }}';
                    var location = locations[deviceId];
                    console.log('Updating marker for ' + deviceId + ':', JSON.stringify(location));
                    if (location && typeof location.latitude === 'number' && typeof location.longitude === 'number') {
                        if (marker) {
                            marker.setLngLat([location.longitude, location.latitude]);
                            console.log(`Updated marker ${deviceId} to [${location.longitude}, ${location.latitude}]`);
                        } else {
                            marker = new mapboxgl.Marker({ color: 'red' })
                                .setLngLat([location.longitude, location.latitude])
                                .setPopup(new mapboxgl.Popup().setText(deviceId))
                                .addTo(map);
                            console.log(`Created marker ${deviceId} at [${location.longitude}, ${location.latitude}]`);
                        }
                        map.setCenter([location.longitude, location.latitude]);
                    } else {
                        console.warn(`Invalid coordinates for ${deviceId}:`, location);
                    }
                }

                updateMarker(currentLocations);

                window.Livewire.on('locationsUpdated', ({ locations }) => {
                    console.log('Livewire locationsUpdated received:', locations);
                    for (var deviceId in locations) {
                        locations[deviceId].latitude = parseFloat(locations[deviceId].latitude);
                        locations[deviceId].longitude = parseFloat(locations[deviceId].longitude);
                    }
                    updateMarker(locations);
                });
            @endif
        });

        function toggleMapSize(mapId) {
            const mapContainer = document.getElementById(mapId);
            const backdrop = document.getElementById('backdrop-' + mapId.split('-')[1]);
            const isExpanding = !mapContainer.classList.contains('expanded');

            mapContainer.classList.toggle('expanded');
            backdrop.style.display = isExpanding ? 'block' : 'none';

            setTimeout(() => {
                if (mapContainer._mapbox) {
                    mapContainer._mapbox.resize();
                    console.log('Map resized');
                }
            }, 350);
        }
    </script>
@endpush