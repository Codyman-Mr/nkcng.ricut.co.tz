@extends('layouts.app')
@section('title','Ongoing Loans')

<meta name="csrf-token" content="{{csrf_token()}}">

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-7">
        <h2 class="font-weight-bold">Ongoing Loans</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content table-responsive border-0">
                    @unless(count($loans)==0)
                    <table class="table text-sm" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Name</th>
                            <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Phone Number</th>
                            <th scope="col" class="px-6 py-3 text-left" style="width: 10rem; font-weight:600;">Date of Submission</th>
                            <th style="width: 1rem;"></th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach ($loans as $loan)
                                <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                    <td class="py-4">{{Str::title($loan->installation->customerVehicle->user->first_name)}} {{Str::title($loan->installation->customerVehicle->user->last_name)}}</td>
                                    <td class="py-4">{{Str::title($loan->installation->customerVehicle->user->phone_number)}} </td>
                                    <td class="py-4">{{\Carbon\Carbon::parse($loan->created_at)->format('d F Y')}} </td>
                                    <td class="px-4 py-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.href='/loan-payments/{{$loan->id}}'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor" class="bi bi-folder2-open" viewBox="0 0 16 16" style="margin-bottom:0.2rem !important;">
                                                <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                  @else
                        <p>No Record Found</p>
                  @endunless
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
