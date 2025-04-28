<div>

    @if (is_array($deviceId))
        @foreach ($deviceId as $id)
            @include('components.single-device-map', ['deviceId' => $id, 'locations' => $locations])
        @endforeach
    @else
        @include('components.single-device-map', ['deviceId' => $deviceId, 'locations' => $locations])
    @endif
</div>
