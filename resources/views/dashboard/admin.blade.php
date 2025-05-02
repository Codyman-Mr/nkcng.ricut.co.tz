@php
    use Carbon\Carbon;
@endphp

@section('main-content')
    <div class=" bg-transparent rounded-md">
        <main class="content px-2 py-4">
            <div class="container-fluid">
                <div class="mb-3">

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-flow-row grid-rows-auto gap-2.5 ">
                        <!-- Card 1 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-md w-full  shadow-l border border-gray-300">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">Total Amount</div>
                                        <img src="{{ asset('svg/money-stack.svg') }}" alt="money stack"
                                            class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($totalLoanAmount) }} <strong class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-md w-full border border-gray-300 shadow-l">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">Paid Amount</div>
                                        <img src="{{ asset('svg/money-in.svg') }}" alt="money in"
                                            class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($payments->sum('paid_amount')) }} <strong
                                            class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="sm:col-span-1">
                            <div class="flex flex-col rounded-md w-full border border-gray-300 shadow-sm">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">Due Amount</div>
                                        <img src="{{ asset('svg/money-out.svg') }}" alt="money out"
                                            class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        {{ number_format($totalLoanAmount - $payments->sum('paid_amount')) }} <strong
                                            class="text-sm">Tshs</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="sm:col-span-1 md:col-start-1 md:row-start-2">
                            <div class="flex flex-col rounded-md w-full border border-gray-300 shadow-l">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">All Users</div>
                                        <img src="{{ asset('svg/users-many.svg') }}" alt="many users"
                                            class="w-8 h-8 object-cover">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        <p>
                                            {{ number_format($user->count()) }} <strong class="text-sm">People</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 5 -->
                        <div class="sm:col-span-1 md:col-start-2 md:row-start-2">
                            <div class="flex flex-col rounded-md w-full border border-gray-300 shadow-l">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">Customers with Loans</div>
                                        <img src="{{ asset('svg/user-not-paid.svg') }}" alt="loan"
                                            class="w-8 h-8 object-cover ">
                                    </span>
                                    <div class="text-xl fw-bold text-[#374151] py-4">
                                        @php
                                            $count = 0;

                                            // Loop through all users
                                            foreach ($users as $user) {
                                                foreach ($user->loans as $loan) {
                                                    $totalAmount = $loan->loan_required_amount;
                                                    $paidAmount = $loan->payments->sum('paid_amount');
                                                    if ($totalAmount > $paidAmount) {
                                                        $count++; // Increment count if there's a due loan
                                                        break; // Break to avoid counting the same user multiple times
                                                    }
                                                }
                                            }
                                        @endphp
                                        <p>
                                            {{ $count }} <strong class="text-sm">People</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 6 -->
                        <div class="sm:col-span-1 md:col-start-3 md:row-start-2">
                            <div class="flex flex-col rounded-md w-full border border-gray-300 shadow-l">
                                <div class="flex flex-col p-4">
                                    <span class="flex justify-between items-center pb-1 border-b border-nkgreen">
                                        <div class="text-sm font-bold text-[#374151] ">Fully Paid Customers</div>
                                        <img src="{{ asset('svg/user-paid.svg') }}" alt="paid user"
                                            class="w-8 h-8 filter-green">
                                    </span>
                                    <div class="text-2xl fw-bold text-[#374151] py-6">
                                        @php
                                            $fullyPaidCount = 0;

                                            // Iterate through all users to check their loans
                                            foreach ($users as $user) {
                                                foreach ($user->loans as $loan) {
                                                    $totalAmount = $loan->loan_required_amount;
                                                    $paidAmount = $loan->payments->sum('paid_amount');

                                                    // Check if the loan is fully paid
                                                    if ($paidAmount >= $totalAmount) {
                                                        $fullyPaidCount++; // Increment count for loans with pending amount of 0
                                                        break; // No need to check further loans for this user
                                                    }
                                                }
                                            }
                                        @endphp

                                        <p>
                                            {{ $fullyPaidCount }} <strong class="text-sm">People</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>





                        <div
                            class="sm:col-span-2 md:col-span-3 lg:col-span-2 lg:col-start-4 lg:row-span-2 lg:row-start-1 border border-gray-400 rounded-2xl flex flex-col justify-between shadow-sm">
                            <div id="map"
                                class="flex flex-col rounded-sm w-full h-64 sm:h-80 md:h-96 lg:h-[90%] p-1 shadow-sm cursor-pointer transition-all duration-500 ease-in-out"
                                onclick="toggleMapSize(event)">
                                <!-- Map content goes here -->
                            </div>
                        </div>
                        <div class="backdrop" onclick="toggleMapSize(event)"></div>


                    </div>

                </div>


                <div class="row">
                    <div class="col-12">
                        @if ($nearEndLoans->isEmpty())
                            <p>No loans nearing end date.</p>
                        @else
                            <div class="mt-1">
                                <section class="py-3 sm:py-5">
                                    <div class=" border border-gray-300 rounded-md ">
                                        <div class=" overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                                            <div
                                                class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                                                <div class="flex items-center flex-1 space-x-4">
                                                    <h5 class="mb-2 fw-bold">Customers with Near Loan End Date</h5>
                                                </div>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">

                                                    <thead
                                                        class="text-xs text-gray-700 uppercase bg-gray-100  dark:bg-gray-700 dark:text-gray-400">
                                                        <tr>

                                                            <th scope="col" class="px-4 py-3">Name</th>
                                                            <th scope="col" class="px-4 py-3">Required</th>
                                                            <th scope="col" class="px-4 py-3">Pending Amount</th>
                                                            <th scope="col" class="px-4 py-3">Days Remaining</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($nearEndLoans as $loan)
                                                            <tr
                                                                class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">

                                                                {{-- name --}}
                                                                <td class="w-auto px-2 py-3">
                                                                    <span
                                                                        class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                                        {{ $loan->user->first_name }}
                                                                        {{ $loan->user->last_name }}
                                                                    </span>
                                                                </td>

                                                                {{-- loan required amount --}}
                                                                <td class="w-auto px-2 py-3">
                                                                    {{ number_format($loan->loan_required_amount) }} Tsh
                                                                </td>

                                                                {{-- loan amount --}}
                                                                <td class="w-auto px-2 py-3">
                                                                    {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }}
                                                                    Tsh</td>
                                                                {{-- days remaining --}}
                                                                <td class="w-auto px-6 py-3 flex items-center">
                                                                    <div class="flex items-center">
                                                                        @php

                                                                            $now = Carbon::now();
                                                                            $daysRemaining = $now->floatDiffInDays(
                                                                                $loan->loan_end_date,
                                                                                true,
                                                                            );

                                                                            if ($daysRemaining < 1) {
                                                                                $hours = round($daysRemaining * 24);
                                                                                echo $hours . ' hours';
                                                                            } else {
                                                                                $days = round($daysRemaining);
                                                                                echo $days .
                                                                                    ' day' .
                                                                                    ($days !== 1 ? 's' : '');
                                                                            }
                                                                        @endphp
                                                                    </div>
                                                                </td>

                                                            </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination Links -->
                                            <div class="my-4 ">
                                                {{ $nearEndLoans->onEachSide(1)->links() }}
                                            </div>

                                        </div>
                                    </div>
                                </section>

                            </div>
                        @endif
                    </div>
                </div>




            </div>
    </div>



    </main>

    </div>
    <style>
        .expanded {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70vw;
            /* Increase width on smaller screens */
            height: 80vh;
            /* Increase height for better coverage */
            min-height: 400px;
            /* Ensure a minimum height on very small screens */
            max-height: 80vh;
            /* Prevent overflow on smaller screens */
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 640px) {
            .expanded {
                width: 95vw;
                /* Almost full width on small screens */
                height: 85vh;
                /* Slightly less height to account for browser UI */
                min-height: 350px;
            }
        }

        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .expanded+.backdrop {
            display: block;
        }

        #marker {
            background-image: url('https://docs.mapbox.com/mapbox-gl-js/assets/washington-monument.jpg');
            background-size: cover;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }

        .mapboxgl-popup {
            max-width: 200px;
        }

        /* Ensure the map container inside .expanded takes full height */
        .expanded #map {
            height: 100% !important;
            width: 100% !important;
        }
    </style>

    <script>
        mapboxgl.accessToken =
            'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';

        const position = [39.241196125639995, -6.774418233335669];

        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: position,
            zoom: 15
        });

        const popup = new mapboxgl.Popup({
            offset: 25
        }).setText('This is the location of the customer');

        const el = document.createElement('div');
        el.id = 'marker';

        new mapboxgl.Marker(el)
            .setLngLat(position)
            .setPopup(popup)
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

            // Stop event propagation to avoid triggering the backdrop click listener immediately
            event.stopPropagation();

            // Force a repaint and resize
            if (mapContainer.classList.contains('expanded')) {
                // Add a small delay to ensure the CSS transition completes
                setTimeout(() => {
                    mapContainer.style.height = '100%'; // Force height to match parent
                    mapContainer.style.width = '100%'; // Force width to match parent
                    map.resize();
                    // Force a repaint of the map canvas
                    map.triggerRepaint();
                }, 300); // Match the transition duration in CSS
            } else {
                // Reset to original size and resize immediately
                adjustMapHeight();
                map.resize();
            }
        }

        function adjustMapHeight() {
            const cards = document.querySelectorAll('.flex.flex-col.rounded-md');
            const map = document.getElementById('map');
            const cardHeight = cards[0]?.offsetHeight || 300; // Fallback height
            if (!map.classList.contains('expanded')) {
                map.style.height = `${cardHeight}px`;
            }
        }

        window.addEventListener('resize', () => {
            adjustMapHeight();
            if (document.getElementById('map').classList.contains('expanded')) {
                setTimeout(() => map.resize(), 300);
            }
        });
        window.addEventListener('load', adjustMapHeight);

        // Detect clicks outside the map to return it to its original size
        document.addEventListener('click', function(event) {
            const mapContainer = document.getElementById('map');
            const backdrop = document.querySelector('.backdrop');

            if (mapContainer.classList.contains('expanded') && !mapContainer.contains(event.target)) {
                mapContainer.classList.remove('expanded');
                backdrop.style.display = 'none';
                adjustMapHeight();
                map.resize();
            }
        });
    </script>
@endsection
