@extends('layouts.app')
@section('title','Pending Loan'.' - '.$loan->installation->customerVehicle->user->first_name.' '.$loan->installation->customerVehicle->user->last_name)

<meta name="csrf-token" content="{{csrf_token()}}">

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <h2 class=""><span class="font-weight-bold">{{$loan->installation->customerVehicle->user->first_name}} {{$loan->installation->customerVehicle->user->last_name}}</span> - Loan Application</h2>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-10">
                <div class="ibox-title">
                    <h3>Section 1</h3>
                </div>

                <div class="ibox-content">
                    <div class="row form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control text-capitalize" name="first_name" id="first_name" value="{{$loan->installation->customerVehicle->user->first_name}}" disabled>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control text-capitalize" name="last_name" id="last_name" value="{{$loan->installation->customerVehicle->user->last_name}}" disabled>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control text-capitalize" name="phone_number" id="phone_number" value="{{$loan->installation->customerVehicle->user->phone_number}}" disabled>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="text" class="form-control text-capitalize" name="dob" id="dob" value="{{$loan->installation->customerVehicle->user->dob}}" disabled>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender</label>
                                <input type="text" class="form-control text-capitalize" name="gender" id="gender" value="{{$loan->installation->customerVehicle->user->gender}}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>National ID Number (NIDA) </label>
                            <span class="text-danger">*</span>
                            <input type="text" class="form-control" name="nida_no" id="nida_no" data-mask="99999999-99999-99999-99" value="{{$loan->installation->customerVehicle->user->nida_number}}" disabled/>
                          </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                              <label>Address </label>
                              <span class="text-danger">*</span>
                              <input type="text" class="form-control" name="address" id="address" value="{{$loan->installation->customerVehicle->user->address}}" disabled/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section" id="section-2">
                    <div class="ibox-title">
                        <h3>Section 2</h3>
                    </div>

                    <div class="ibox-content">
                        <div class="row form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehicle Name <span class="text-muted">(Model)</span></label>
                                    <input type="text" class="form-control text-capitalize" name="vehicle_name" id="vehicle_name" value="{{$loan->installation->customerVehicle->model}}" disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Vehicle Type</label>
                                    <select class="form-control select2" id="vehicle_type" name="vehicle_type" style="height: 2rem !important; width: 100%" disabled>
                                        <option value=""></option>
                                        <option value="car" {{$loan->installation->customerVehicle->vehicle_type == 'car' ? 'selected' : '' }}>Car</option>
                                        <option value="bajaj" {{$loan->installation->customerVehicle->vehicle_type == 'bajaj' ? 'selected' : '' }}>Bajaj</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                  <label>Plate Number</label>
                                  <input type="text" class="form-control text-capitalize" name="plate_number" id="plate_number" value="{{$loan->installation->customerVehicle->plate_number}}" disabled/>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                  <label>Fuel Type</label>
                                  <select class="form-control select2" id="fuel_type" name="fuel_type" style="height: 2rem !important; width: 100%" disabled>
                                    <option value=""></option>
                                    <option value="petrol" {{$loan->installation->customerVehicle->fuel_type == 'petrol' ? 'selected' : '' }}>Petrol</option>
                                    <option value="diesel" {{$loan->installation->customerVehicle->fuel_type == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                  </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                  <label>Cylinder Type</label>
                                  <select class="form-control select2" id="cylinder_type" name="cylinder_type" style="height: 2rem !important; width: 100%">
                                        <option value=""></option>
                                        @foreach ($cylinders as $cylinder)
                                        <option value="{{$cylinder->id}}">{{$cylinder->name}}</option>
                                        @endforeach
                                  </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                  <label>Required Amount</label>
                                  <input type="text" class="form-control amount" name="loan_required_amount" id="loan_required_amount">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                  <label>Payment Plan</label>
                                  <select class="form-control select2" id="loan_payment_plan" name="loan_payment_plan" style="height: 2rem !important; width: 100%">
                                    <option value=""></option>
                                    <option value="weekly">Weekly</option>
                                    <option value="bi-weekly">Bi Weekly</option>
                                    <option value="monthly">Monthly</option>
                                  </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section" id="section-3">
                    <div class="ibox-title">
                        <h3>Section 3</h3>
                    </div>

                    <div class="ibox-content">
                        <h3 class="text-info">Guarantors and Support Documents.</h3>
                        <br><br>

                        <div class="row">
                            @foreach ($loan->documents as $document)
                            <a class="col-md-4 mb-4" href='{{asset("/storage/{$document->document_path}")}}'>
                                <div class="form-group">
                                    @if (Str::endsWith($document->document_path, '.pdf'))
                                        <iframe src="{{ asset("/storage/{$document->document_path}") }}" width="100%" height="200" frameborder="0"></iframe>
                                    @else
                                        <img src="{{ asset("/storage/{$document->document_path}") }}" alt="Document Thumbnail" width="100%" height="200">
                                    @endif

                                    <h4>{!! $document->document_type !!}</h4>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="ibox-content">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <button type="button" class="btn btn-outline-danger btn-sm float-left prev-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    Reject Loan
                                </button>

                                <button type="button" class="btn btn-outline-info btn-sm float-right" id="accept-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                    </svg>
                                    Accept Loan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        let loanId={!! json_encode($loan->id) !!}

        $("#accept-btn").click(function () {
            let requiredFields = ["cylinder_type","loan_required_amount","loan_payment_plan"];

            $(".form-control").removeClass("border-danger");

            for (const field of requiredFields) {
                const $field = $(`#${field}`);

                if (!$field.val()) {
                    $field.addClass("border-danger");
                    return;
                } else {
                    $field.removeClass("border-danger");
                }
            }

            Swal.fire({
                title: null,
                html: "Loading....",
                didOpen: () => {
                  Swal.showLoading();
                }
            });

            $.ajax({
                type: 'POST',
                url: `/approve-loan/${loanId}`,
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    'cylinder_type':$('#cylinder_type').val(),
                    'loan_required_amount':$('#loan_required_amount').val(),
                    'loan_payment_plan':$('#loan_payment_plan').val()
                },
                success: function (response) {
                    Swal.close();

                    Swal.fire({
                        title: "",
                        icon: "success",
                        showConfirmButton: true,
                        allowEscapeKey: true,
                        text: response.message,
                    }).then((value) => {
                        window.location.href='/';
                    })
                },
                error: function (xhr) {
                    Swal.close();

                    Swal.fire({
                        title: "",
                        icon: "error",
                        showConfirmButton: true,
                        allowEscapeKey: true,
                        text: xhr.responseJSON.message,
                    });
                },
            });
        });

    </script>
@endsection
