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
                    $countedInstallations = [];

                    foreach ($loans as $loan) {
                        $totalAmount = $loan->loan_required_amount;
                        $paidAmount = $loan->payments->sum('paid_amount');

                        if ($totalAmount > $paidAmount) {
                            if (!in_array($loan->installation_id, $countedInstallations)) {
                                $count++;
                                $countedInstallations[] = $loan->installation_id;
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




                        {{-- working map container --}}
                        <!-- Card 7: Responsive Map -->
                        <div
                            class="sm:col-span-1 md:col-span-3  lg:col-span-2 lg:col-start-4 lg:row-span-2 lg:row-start-1 xl:col-span-5 relative border border-gray-400 rounded-2xl flex flex-col shadow-sm min-h-[300px]">
                            <div id="map" class="map-container cursor-pointer rounded-2xl w-full h-full"></div>
                            <button id="map-close"
                                class="hidden absolute top-2 right-2 bg-white text-gray-700 rounded-full p-2 shadow z-50"
                                onclick="toggleMapSize(event)">
                                ✕
                            </button>
                            <button id="expand-map-btn"
                                class="absolute top-2 right-2 z-10 bg-white p-2 rounded-full shadow hover:bg-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4h6M4 4v6M20 20h-6m6 0v-6M4 20v-6m0 6h6M20 4v6m0-6h-6" />
                                </svg>
                            </button>
                        </div>
                        <div id="backdrop" class="backdrop" onclick="toggleMapSize(event)"></div>



                        {{-- end of working container --}}

                        {{-- testing map container --}}


                    </div>

                    <!-- tables -->
                    <div class="flex flex-col md:flex-row gap-4  p-1 overflow-x-auto">
                        <!-- Sidebar: Cell 1 & 4 stacked vertically -->
                        <div class="flex flex-col gap-2 w-full md:w-[300px]">
                            <!-- Payments This Week -->
                            <section class="flex-1 py-3 sm:py-5">
                                <div
                                    class="border border-gray-300 rounded-md bg-white shadow-sm dark:bg-gray-800 h-full flex flex-col">
                                    <div class="px-4 py-3 border-b">
                                        <h5 class="font-bold text-black dark:text-white">Payments This Week</h5>

                                    </div>
                                    <div class="overflow-x-auto flex-1">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th class="px-4 py-3">Name</th>
                                                    <th class="px-4 py-3">Time to Next Payment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($paymentsThisWeek as $payment)
                                                    @php
                                                        $days = round($payment->time_to_next_payment);
                                                    @endphp

                                                    <tr
                                                        class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <td class="px-4 py-2">
                                                            {{ $payment->applicant_name }}
                                                            
                                                        </td>
                                                        <td class="px-4 py-2">
                                                            {{ $days }} {{ Str::plural('day', $days) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2"
                                                            class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                                            No payments this week.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>

                            <!-- Missed Payments This Week -->
                            <section class="flex-1 py-3 sm:py-5">
                                <div
                                    class="border border-gray-300 rounded-md bg-white shadow-sm dark:bg-gray-800 h-full flex flex-col">
                                    <div class="px-4 py-3 border-b">
                                        <h5 class="font-bold text-black dark:text-black">Missed Payments </h5>

                                    </div>
                                    <div class="overflow-x-auto flex-1">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th class="px-4 py-3">Name</th>
                                                    <th class="px-4 py-3">Days Past Due</th>
                                                    <th class="px-4 py-3">Phone Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($missedPayments->isEmpty())
                                                    <tr>
                                                        <td colspan="3"
                                                            class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                                            No missed payments this week.
                                                        </td>
                                                    </tr>
                                                @else
                                                    @foreach ($missedPayments as $missed)
                                                        <tr
                                                            class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <td class="px-4 py-2">
                                                                {{ $missed->applicant_name }}
                                                                
                                                            </td>
                                                            <td class="px-4 py-2">
                                                                {{ abs(round($missed->days_past_due )) }} days
                                                            </td>
                                                            <td class="px-4 py-2">
                                                                {{ $missed->applicant_phone_number }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Main content: Cell 2,3,5,6 -->
                        <div class="flex-1">
                            @if ($nearEndLoans->isEmpty())
                                <div
                                    class="p-4 bg-white dark:bg-gray-800 rounded-md shadow-sm text-gray-500 dark:text-gray-300">
                                    No loans nearing end date.
                                </div>
                            @else
                                <section class="py-3 sm:py-5 h-full flex flex-col">
                                    <div
                                        class="border border-gray-300 rounded-md bg-white shadow-sm dark:bg-gray-800 flex-1 flex flex-col">
                                        <div class="px-4 py-3 border-b">
                                            <h5 class="font-bold text-black dark:text-white">Loan Nearing End Date</h5>

                                        </div>
                                        <div class="overflow-x-auto flex-1">
                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead
                                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th class="px-4 py-3">Name</th>
                                                        <th class="px-4 py-3">Required</th>
                                                        <th class="px-4 py-3">Pending Amount</th>
                                                        <th class="px-4 py-3">Days Remaining</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nearEndLoans as $loan)
                                                        <tr
                                                            class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <td class="px-2 py-3">
                                                                <span
                                                                    class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                                                    {{ $loan->applicant_name }}
                                                                    
                                                                </span>
                                                            </td>
                                                            <td class="px-2 py-3">
                                                                {{ number_format($loan->loan_required_amount) }} Tsh
                                                            </td>
                                                            <td class="px-2 py-3">
                                                                {{ number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount')) }}
                                                                Tsh
                                                            </td>
                                                            {{-- <td class="px-6 py-3">
                                                                @php
                                                                    $now = Carbon::now();
                                                                    $daysRemaining = $now->floatDiffInDays(
                                                                        $loan->loan_end_date,
                                                                        true,
                                                                    );
                                                                    echo $daysRemaining < 1
                                                                        ? round($daysRemaining * 24) . ' hours'
                                                                        : round($daysRemaining) .
                                                                            ' day' .
                                                                            (round($daysRemaining) !== 1 ? 's' : '');
                                                                @endphp
                                                            </td> --}}

                                                            <td class="px-6 py-3">
                                                                @php
    $now = Carbon::now();
    $end = Carbon::parse($loan->loan_end_date);

    $diffDays = $now->diffInDays($end, false);

    if ($diffDays < 0) {
        echo 'Expired';
    } elseif ($diffDays <= 14) {
        echo intval(round($diffDays)) . ' day' . (intval(round($diffDays)) !== 1 ? 's' : '');
    } else {
        // Usionyeshe kitu chochote (blank) kama zaidi ya 14 days
        echo '';
    }
@endphp

                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="my-4 px-4">
                                            {{ $nearEndLoans->onEachSide(1)->links() }}
                                        </div>
                                    </div>
                                </section>
                            @endif
                        </div>
                    </div>





                </div>
            </div>

        </main>

    </div>

    {{-- working styls --}}
    <style>
        .map-container {
            width: 100%;
            height: 100%;
            min-height: 300px;
            transition: all 0.3s ease-in-out;
        }

        .map-container.expanded {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 75vw !important;
            height: 75vh !important;
            z-index: 1000;
            background-color: white;
            border-radius: 1rem;
        }

        @media (min-width: 768px) {
            .map-container.expanded {
                width: 80vw !important;
                height: 80vh !important;
            }
        }

        .backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .map-container.expanded+.backdrop {
            display: block;
        }

        #map {
            width: 100%;
            height: 100%;
        }
    </style>
    {{-- end of working styles --}}



    {{-- working scripts --}}
    {{-- <script>
        mapboxgl.accessToken =
            'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg'; // your token

        const position = [39.241196125639995, -6.774418233335669];
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: position,
            zoom: 15
        });

        new mapboxgl.Marker().setLngLat(position).addTo(map);
        map.addControl(new mapboxgl.NavigationControl());
        map.on('style.load', () => map.setFog({}));

        const mapContainer = document.getElementById('map');
        const backdrop = document.getElementById('backdrop');
        const closeButton = document.getElementById('map-close');

        const markers = [];

        // Dynamic locations from backend
        const locations = @json($locations);

        function addMarkers(data) {
            markers.forEach(marker => marker.remove()); // clear existing markers
            markers.length = 0; // reset array

            data.forEach(loc => {
                const popup = new mapboxgl.Popup({
                    closeButton: false,
                    closeOnClick: false
                }).setText(`Device: ${loc.label}`);

                const markerElement = document.createElement('div');
                markerElement.className = 'custom-marker w-4 h-4 bg-nkgreen rounded-full shadow-md';
                markerElement.style.cursor = 'pointer';

                const marker = new mapboxgl.Marker(markerElement)
                    .setLngLat([loc.longitude, loc.latitude])
                    .addTo(map);

                // Hover events
                markerElement.addEventListener('mouseenter', () => {
                    popup.addTo(map);
                    popup.setLngLat([loc.longitude, loc.latitude]);
                });

                markerElement.addEventListener('mouseleave', () => {
                    popup.remove();
                });

                markers.push(marker);
            });
        }


        addMarkers(locations);

        const expandMapBtn = document.getElementById('expand-map-btn');

        expandMapBtn.addEventListener('click', toggleMapSize);


        function toggleMapSize(event) {
            const isExpanded = mapContainer.classList.toggle('expanded');
            backdrop.style.display = isExpanded ? 'block' : 'none';
            closeButton.classList.toggle('hidden', !isExpanded);
            if (event) event.stopPropagation();
            setTimeout(() => map.resize(), 350);
        }

        window.addEventListener('keydown', e => {
            if (e.key === 'Escape' && mapContainer.classList.contains('expanded')) {
                toggleMapSize();
            }
        });

        window.addEventListener('resize', () => {
            if (!mapContainer.classList.contains('expanded')) {
                map.resize();
            }
        });
    </script> --}}
    {{-- end of working scripts --}}
    <script>
        mapboxgl.accessToken =
            'pk.eyJ1IjoibWljaGFlbG1nb25kYXNyIiwiYSI6ImNtNXIwZHV0dDA1aDgyanIxaDd4OGQ2cWsifQ.wmLJmRnEG8S46PXSGajvSg';

        const locations = @json($locations);

        // Use the first location as the default center if available
        const defaultPosition = locations.length > 0 ? [locations[0].longitude, locations[0].latitude] : [
            39.241196125639995, -6.774418233335669
        ];

        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: defaultPosition,
            zoom: 40
        });

        map.addControl(new mapboxgl.NavigationControl());
        map.on('style.load', () => map.setFog({}));

        const mapContainer = document.getElementById('map');
        const backdrop = document.getElementById('backdrop');
        const closeButton = document.getElementById('map-close');
        const markers = [];


        function addMarkers(data) {
            markers.forEach(marker => marker.remove());
            markers.length = 0;

            data.forEach(loc => {
                const popup = new mapboxgl.Popup({
                    closeButton: false,
                    closeOnClick: false
                }).setHTML(`
            <div class="text-sm font-medium">
                <div><strong>${loc.user_name}</strong></div>
                <div>Loaned: ${loc.amount_loaned}</div>
                <div>Plate: ${loc.vehicle_plate}</div>
                <div>Status: ${loc.assignment_status}</div>
            </div>
        `);

                const markerElement = document.createElement('div');
                markerElement.className = 'custom-marker w-4 h-4 bg-nkgreen rounded-full shadow-md';
                markerElement.style.cursor = 'pointer';

                const marker = new mapboxgl.Marker(markerElement)
                    .setLngLat([loc.longitude, loc.latitude])
                    .addTo(map);

                // Hover popup
                markerElement.addEventListener('mouseenter', () => {
                    popup.setLngLat([loc.longitude, loc.latitude]).addTo(map);
                });
                markerElement.addEventListener('mouseleave', () => {
                    popup.remove();
                });

                // Click zoom and center
                markerElement.addEventListener('click', () => {
                    map.flyTo({
                        center: [loc.longitude, loc.latitude],
                        zoom: 17, // Adjust zoom level as needed
                        speed: 1.2,
                        curve: 1.5,
                        easing: t => t,
                    });

                    popup.setLngLat([loc.longitude, loc.latitude]).addTo(
                        map); // optional: open on click too
                });

                markers.push(marker);
            });
        }



        addMarkers(locations);

        const expandMapBtn = document.getElementById('expand-map-btn');

        expandMapBtn.addEventListener('click', toggleMapSize);


        function toggleMapSize(event) {
            const isExpanded = mapContainer.classList.toggle('expanded');
            backdrop.style.display = isExpanded ? 'block' : 'none';
            closeButton.classList.toggle('hidden', !isExpanded);
            if (event) event.stopPropagation();
            setTimeout(() => map.resize(), 350);
        }

        // Escape key collapses map
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape' && mapContainer.classList.contains('expanded')) {
                toggleMapSize();
            }
        });

        // Resize only on window resize
        window.addEventListener('resize', () => {
            if (!mapContainer.classList.contains('expanded')) {
                map.resize();
            }
        });
    </script>




@endsection
