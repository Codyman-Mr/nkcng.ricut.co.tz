<!-- resources/views/livewire/multi-location-tracker.blade.php -->
<div>

    <div wire:ignore id="map" style="height: 400px; width: 800px; margin: auto;"></div>
</div>

@push('scripts')
    <script>
        window.addEventListener('livewire:initialized', function () {
            console.log('Initializing map...');
            var map = L.map('map').setView([-6.774458643439982, 39.241475047706615], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var markers = {};
            var currentLocations = @json($locations);

            // Ensure numeric coordinates
            for (var deviceId in currentLocations) {
                currentLocations[deviceId].latitude = parseFloat(currentLocations[deviceId].latitude);
                currentLocations[deviceId].longitude = parseFloat(currentLocations[deviceId].longitude);
            }

            function updateMarkers(locations) {
                console.log('Updating markers:', JSON.stringify(locations));
                for (var deviceId in locations) {
                    var location = locations[deviceId];
                    if (typeof location.latitude === 'number' && typeof location.longitude === 'number') {
                        if (markers[deviceId]) {
                            markers[deviceId].setLatLng([location.latitude, location.longitude]);
                            console.log(`Updated marker ${deviceId} to [${location.latitude}, ${location.longitude}]`);
                        } else {
                            markers[deviceId] = L.marker([location.latitude, location.longitude])
                                .addTo(map)
                                .bindPopup(deviceId);
                            console.log(`Created marker ${deviceId} at [${location.latitude}, ${location.longitude}]`);
                        }
                    } else {
                        console.warn(`Invalid coordinates for ${deviceId}:`, location);
                    }
                }
            }

            updateMarkers(currentLocations);

            window.Livewire.on('locationsUpdated', ({ locations }) => {
                console.log('Livewire locationsUpdated received:', locations);
                for (var deviceId in locations) {
                    locations[deviceId].latitude = parseFloat(locations[deviceId].latitude);
                    locations[deviceId].longitude = parseFloat(locations[deviceId].longitude);
                }
                currentLocations = { ...currentLocations, ...locations };
                updateMarkers(currentLocations);
            });
        });
    </script>
@endpush
