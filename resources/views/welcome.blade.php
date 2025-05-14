@extends('layouts.app')

@section('main-content')


        <div class="container mx-auto mt-12 flex flex-col items-center">
            <div class="bg-white shadow-lg rounded-lg p-8 h-full w-full max-w-lg text-center">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-700">
                        WELCOME <span class="text-green-700">{{ Auth::user()->first_name ?? 'Guest' }}</span>,
                    </h1>
                    <div class="border-b-2 border-green-500 w-16 mx-auto my-2"></div>
                </div>
                <p class="text-gray-600">Great choice! Please share more details to get your vehicle ready for installation
                    appointment.</p>
                <button
                    class="mt-6 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-300"
                    onclick="window.location.href='/'">
                    Get Started
                </button>
            </div>
        </div>


    </div>
@endsection


