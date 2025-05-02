{{-- <div>
    @if (!$showApplicationForm)
        <div class="flex flex-col justify-center items-center overflow-hidden mb-4">
            <div class="w-full sm:w-10/12 px-6 sm:px-8 mt-10">

                <!-- Title Section -->
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-extrabold text-gray-900">Explore Our Loan Packages</h1>
                    <p class="text-lg text-gray-600 mt-3 max-w-2xl mx-auto leading-relaxed">
                        Discover flexible and affordable loan options tailored for your financial needs. We offer
                        competitive rates and seamless application processes to help you achieve your goals.
                    </p>
                </div>

                <!-- Loan Packages Grid -->
                <div class="grid grid-cols-1 custom:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6  mb-10">

                    <!-- Card 1: Bajaji -->
                    <div
                        class="border border-gray-200 shadow-sm bg-white rounded-xl p-6 transition-transform hover:scale-105">
                        <div class="text-center">
                            <span class="text-gray-900 text-2xl font-semibold">Bajaji</span>
                            <p class="text-gray-600 text-sm mt-2">For Bajaji enthusiasts</p>
                        </div>
                        <div class="text-primary flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-sm">Tsh</span>
                            <p class="text-5xl font-bold text-gray-900">200,000</p>
                        </div>
                        <ul class="text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 1,600,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Down Payment: 200,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance: 1,400,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity: 7L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('bajaji')"
                            class="btn btn-primary btn-sm w-full mt-6  rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                    <!-- Card 2: Small Cars -->
                    <div
                        class="border border-primary shadow-sm bg-white rounded-xl p-6 transition-transform hover:scale-105 relative">
                        <span class="badge badge-soft badge-sm badge-primary absolute top-2 right-2">Popular</span>
                        <div class="text-center">
                            <span class="text-gray-900 text-2xl font-semibold">Small Cars</span>
                            <p class="text-gray-600 text-sm mt-2">For small to medium cars</p>
                        </div>
                        <div class="text-primary flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-sm">Tshs</span>
                            <p class="text-5xl font-bold text-gray-900">400,000</p>
                        </div>
                        <ul class="text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 1,900,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Down Payment: 400,000</span>
                            </li>

                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance 1,500,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity 11 L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('small_car')"
                            class="btn btn-primary btn-sm w-full mt-6  rounded-lg shadow-sm hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                    <!-- Card 3: Medium Car Package -->
                    <div
                        class="border border-gray-200 shadow-sm bg-white rounded-xl p-6 transition-transform hover:scale-105">
                        <div class="text-center">
                            <span class="text-gray-900 text-2xl font-semibold">Medium Car Package</span>
                            <p class="text-gray-600 text-sm mt-2">Solution for medium cars</p>
                        </div>
                        <div class="text-primary flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-sm">Tshs</span>
                            <p class="text-5xl font-bold text-gray-900">800,000</p>
                        </div>
                        <ul class="text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 2,200,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Down Payment 800,000</span>
                            </li>

                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance 1,400,000</span>
                            </li>

                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity 15 L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('medium_car')"
                            class="btn btn-primary btn-sm w-full mt-6  rounded-lg shadow-sm hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
    @if ($showApplicationForm)
        @livewire('loan-application-form', ['package' => $selectedLoanPackage])
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-4"> {{ session('error') }}</div>
    @endif
</div> --}}


<div>
    @if (!$showApplicationForm)
        <div class="flex flex-col justify-center items-center overflow-hidden mb-4">
            <div class="w-full sm:w-10/12 px-6 sm:px-8 mt-10">

                <!-- Title Section -->
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-extrabold text-gray-900">Explore Our Loan Packages</h1>
                    <p class="text-lg text-gray-600 mt-3 max-w-2xl mx-auto leading-relaxed">
                        Discover flexible and affordable loan options tailored for your financial needs. We offer
                        competitive rates and seamless application processes to help you achieve your goals.
                    </p>
                </div>

                <!-- Loan Packages Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-10">

                    <!-- Card 1: Bajaji -->
                    <div
                        class="border border-gray-200 shadow-sm bg-white rounded-xl p-4 sm:p-6 transition-transform hover:scale-105">
                        <div class="text-center">
                            <span class="text-gray-900 text-xl sm:text-2xl font-semibold">Bajaji</span>
                            <p class="text-gray-600 text-xs sm:text-sm mt-2">For Bajaji enthusiasts</p>
                        </div>
                        <div class="flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-xs sm:text-sm">Tsh</span>
                            <p class="text-3xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-gray-900">200,000</p>
                        </div>
                        <ul class="text-xs sm:text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 1,600,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Down Payment: 200,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance: 1,400,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity: 7L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('bajaji')"
                            class="btn btn-primary text-xs sm:text-sm w-full mt-6 rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                    <!-- Card 2: Small Cars -->
                    <div
                        class="border border-primary shadow-sm bg-white rounded-xl p-4 sm:p-6 transition-transform hover:scale-105 relative">
                        <span class="badge badge-soft badge-sm badge-primary absolute top-2 right-2">Popular</span>
                        <div class="text-center">
                            <span class="text-gray-900 text-xl sm:text-2xl font-semibold">Small Cars</span>
                            <p class="text-gray-600 text-xs sm:text-sm mt-2">For small to medium cars</p>
                        </div>
                        <div class="flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-xs sm:text-sm">Tshs</span>
                            <p class="text-3xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-gray-900">400,000</p>
                        </div>
                        <ul class="text-xs sm:text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 1,900,000</span>
                            </li>
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Down Payment: 400,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance: 1,500,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity: 11L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('small_car')"
                            class="btn btn-primary text-xs sm:text-sm w-full mt-6 rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                    <!-- Card 3: Medium Car Package -->
                    <div
                        class="border border-gray-200 shadow-sm bg-white rounded-xl p-4 sm:p-6 transition-transform hover:scale-105">
                        <div class="text-center">
                            <span class="text-gray-900 text-xl sm:text-2xl font-semibold">Medium Car Package</span>
                            <p class="text-gray-600 text-xs sm:text-sm mt-2">Solution for medium cars</p>
                        </div>
                        <div class="flex justify-center gap-x-2 items-center mt-4">
                            <span class="text-gray-500 text-xs sm:text-sm">Tshs</span>
                            <p class="text-3xl sm:text-2xl md:text-3xl lg:text-3xl font-bold text-gray-900">800,000</p>
                        </div>
                        <ul class="text-xs sm:text-sm space-y-3 mt-4">
                            <li class="flex items-center gap-x-3">
                                <span class="bg-primary/20 text-primary rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4"></span>
                                </span>
                                <span class="text-gray-700">Total Installation cost: 2,200,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Down Payment: 800,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Amount to Finance: 1,400,000</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span
                                    class="bg-primary/20 text-primary flex items-center justify-center rounded-full p-1">
                                    <span class="icon-[tabler--arrow-right] size-4 rtl:rotate-180"></span>
                                </span>
                                <span class="text-gray-700">Cylinder Capacity: 15L</span>
                            </li>
                        </ul>
                        <button type="button" wire:click="setPackage('medium_car')"
                            class="btn btn-primary text-xs sm:text-sm w-full mt-6 rounded-lg shadow-md hover:bg-primary-dark transition duration-300">
                            Apply
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
    @if ($showApplicationForm)
        @livewire('loan-application-form', ['package' => $selectedLoanPackage])
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mt-4"> {{ session('error') }}</div>
    @endif
</div>