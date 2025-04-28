<div>
    <div wire:poll.5s>
        @foreach($vehicles as $vehicle)
            <div class="p-4 mb-4 bg-white rounded shadow">
                <div class="vehicle-info">
                    <h3 class="font-bold text-lg">{{ $vehicle->name }}</h3>
                    <p class="text-gray-600">
                        Coordinates: {{ $vehicle->lat }}, {{ $vehicle->lng }}
                    </p>
                    <p>Speed: {{ $vehicle->speed }} km/h</p>
                    <p>Battery: {{ $vehicle->battery_level }}%</p>
                </div>
            </div>
        @endforeach
    </div>
</div>