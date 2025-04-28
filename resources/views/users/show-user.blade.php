@extends('layouts.app')
@section('title', 'User' . ' - ' . $user->first_name . ' ' . $user->last_name)

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('main-content')
    @livewire('show-user-component', ['userId' => $user->id])

        <!-- Expanded Style and Backdrop -->
    <style>
        .expanded {
            position: fixed;
            top: 50%;
            /* Move the top edge to the middle of the screen */
            left: 55%;
            /* Move the left edge to the middle of the screen */
            transform: translate(-50%, -50%);
            /* Shift the map back by 50% of its own width and height */
            width: 70vw;
            /* 70% of viewport width */
            height: 70vh;
            /* 70% of viewport height */
            z-index: 1000;
            /* Ensure the map is on top of other elements */
        }

        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black */
            z-index: 999;
            /* Ensure the backdrop is below the map */
            display: none;
            /* Hidden by default */
        }

        .expanded+.backdrop {
            display: block;
            /* Show the backdrop when the map is expanded */
        }
    </style>

    <!-- Initialize Map and Toggle Expanded State -->
    <script>
        mapboxgl.accessToken =
            'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [39.241196125639995, -6.774418233335669],
            zoom: 15
        });

        new mapboxgl.Marker({
                color: 'red'
            })
            .setLngLat([39.241196125639995, -6.774418233335669])
            .addTo(map);

        map.addControl(new mapboxgl.NavigationControl());

        map.on('style.load', () => {
            map.setFog({}); // Set the default atmosphere style
        });

        function toggleMapSize(event) {
            const mapContainer = document.getElementById('map');
            const backdrop = document.querySelector('.backdrop');

            mapContainer.classList.toggle('expanded');
            backdrop.style.display = mapContainer.classList.contains('expanded') ? 'block' : 'none';

            // Resize the map to fit the new container size
            if (mapContainer.classList.contains('expanded')) {
                map.resize();
            }

            // Stop event propagation to avoid triggering the backdrop click listener immediately
            event.stopPropagation();
        }

        // Detect clicks outside the map to return it to its original size
        document.addEventListener('click', function(event) {
            const mapContainer = document.getElementById('map');
            const backdrop = document.querySelector('.backdrop');

            if (mapContainer.classList.contains('expanded') && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove('expanded');
                backdrop.style.display = 'none';
            }
        });
    </script>
    @endsection
