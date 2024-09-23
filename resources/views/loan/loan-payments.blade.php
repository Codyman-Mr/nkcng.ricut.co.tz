@php
    $paidAmount=number_format($loan->payments()->sum('paid_amount'));
@endphp

@extends('layouts.app')
@section('title','Loan Payments'.' - '.$loan->installation->customerVehicle->user->first_name.' '.$loan->installation->customerVehicle->user->last_name)

<meta name="csrf-token" content="{{csrf_token()}}">

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-5">
        <h2 class=""><span class="font-weight-bold">{{$loan->installation->customerVehicle->user->first_name}} {{$loan->installation->customerVehicle->user->last_name}}</span></h2>
    </div>

    <div class="col-sm-7">
        <div class="float-md-right tooltip-demo" style="margin-top: 20px;">
            <button onclick='sendReminder({{$loan}})' type="button" class="btn btn-sm btn-outline-secondary mr-2">
                <span class="text-sm ml-1" style="font-size:0.7rem;"><i class="bi bi-bell-fill mr-1"></i> Send Reminder</span>
            </button>

            <button onclick='$("#createModal").modal("show")' type="button" class="btn btn-sm btn-outline-success mr-2">
                <span class="text-sm ml-1" style="font-size:0.7rem;"> <i class="bi bi-cash mr-2"></i>Add Payment</span>
            </button>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row m-b-lg">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="widget style1" style="background-color: white;color:#2E4057 !important;">
                    <h4 class="text-info">Required Amount</h4>
                    <br>
                    <p style="font-size: 1.5rem;">{{number_format($loan->loan_required_amount)}} Tshs</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="widget style1" style="background-color: white;color:#2E4057 !important;">
                    <h4 class="text-info">Amount Paid</h4>
                    <br>
                    <p style="font-size: 2rem;">{{$paidAmount}} Tshs</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="widget style1" style="background-color: white;color:#2E4057 !important;">
                    <h4 class="text-info">Remaining Amount</h4>
                    <br>
                    <p style="font-size: 1.5rem;">{{number_format($loan->loan_required_amount -$loan->payments()->sum('paid_amount')) }} Tshs</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="widget style1" style="background-color: white;color:#2E4057 !important;">
                <div class="card-body py-4">
                    <h5 class="mb-2 fw-bold text-info">Payment Progress</h5>
                    @php
                        $totalAmount = $loan->loan_required_amount;
                        $paidAmount = $loan->payments->sum('paid_amount');
                        $progressPercentage = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;
                    @endphp
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{$progressPercentage}}%;" aria-valuenow="{{$progressPercentage}}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($progressPercentage) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="ibox">
                <div class="ibox-content table-responsive">
                    @unless(count($loan->payments)==0)
                    <table class="table text-sm" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3" style="width: 17rem; font-weight:600;">Date of Payment</th>
                            <th scope="col" class="px-6 py-3" style="width: 15rem; font-weight:600;">Amount Paid</th>
                            <th scope="col" class="px-6 py-3 text-left" style="width: 10rem; font-weight:600;">Payment Method</th>
                            <th style="width: 1rem;"></th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach ($loan->payments as $payment)
                                <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                    <td class="py-4">{{\Carbon\Carbon::parse($payment->payment_date)->format('d F Y')}}</td>
                                    <td class="py-4">{{number_format($payment->paid_amount)}} Tsh</td>
                                    <td class="py-4">{{Str::title($payment->payment_method)}}</td>
                                    <td class="px-4 py-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePayment({{$payment}})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16" style="margin-bottom:0.2rem !important;">
                                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
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

<div class="modal inmodal fade" id="createModal" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date of Payment</label> <span class="text-danger">*</span>
                            <input type="date" class="form-control" name="payment_date" id="payment_date">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                          <label>Amount Paid</label> <span class="text-danger">*</span>
                          <input type="text" class="form-control amount" name="paid_amount" id="paid_amount">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                          <label>Payment Method</label> <span class="text-danger">*</span>

                          <select class="form-control select2" name="payment_method" id="payment_method" style="height: 2rem !important; width: 100%">
                            <option value="cash">CRDB</option>
                            <option value="NMB">NMB</option>
                            <option value="TIGO PESA">TIGO PESA</option>
                            <option value="MPESA">M PESA</option>
                            <option value="AIRTEL MONEY">AIRTEL MONEY</option>
                            <option value="AZAM PESA">AZAM PESA</option>
                          </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                <div class="form-group">
                        <label for="exampleFormControlTextarea1">Payment Descriptions</label>
                        <textarea class="form-control" id="payment_description" rows="3" name="payment_description"></textarea>
                </div>
                      
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm float-right" id="store-btn">Add Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
    <script>
        function number_format(number) {
            return new Intl.NumberFormat('en-US').format(number);
        }

        function sendReminder(loanDetails){
            const payment_plan=loanDetails.loan_payment_plan;
            const phone_number=loanDetails.user.phone_number;
            const customer=`${loanDetails.user.first_name} ${loanDetails.user.last_name}`;
            const requiredAmount=loanDetails.loan_required_amount;
            const paidAmount={!! $paidAmount !!};

            let message=
            `Habari ${customer}, Tunakuandikia kukukumbusha kwamba bado unadaiwa kiasi cha ${number_format(requiredAmount - paidAmount)}. Tafadhali endelea kulipia deni lako ili kuepuka usumbufu wowote.
Asante - NK CNG.`;

            Swal.fire({
              title:"Are you sure ?",
              text:`Please confirm if you want to proceed with this action.`,
              icon: "question",
              showCancelButton: true,
              cancelButtonText:"No",
              confirmButtonText: "Yes",
              confirmButtonColor: "#dc3545",
              reverseButtons: false,
              preConfirm: function (choice) {
              if (choice) {
                Swal.fire({
                    title: null,
                    html: "Loading....",
                    didOpen: () => {
                      Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/send-reminder`,
                    method: 'POST',
                    data:{
                        draw: Math.floor(Math.random() * 1000),
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        message,
                        phone_number:phone_number.replace('0', '+255')
                    },
                    success: function(response) {
                        Swal.close();

                        Swal.fire({
                            title: "",
                            icon: "success",
                            showConfirmButton: true,
                            allowEscapeKey: true,
                            text: 'Reminder sent successfully',
                        });
                    },
                    error: function(error) {
                        Swal.close();

                        Swal.fire({
                            title: "",
                            icon: "error",
                            showConfirmButton: true,
                            allowEscapeKey: true,
                            text: 'Something went wrong',
                        });
                    }
                });
              }
            }
            });
        }

        function deletePayment(payment){
          Swal.fire({
              title:"Are you sure ?",
              text:`Please confirm if you want to proceed with this action.`,
              icon: "question",
              showCancelButton: true,
              cancelButtonText:"No",
              confirmButtonText: "Yes",
              confirmButtonColor: "#dc3545",
              reverseButtons: false,
              preConfirm: function (choice) {
              if (choice) {
                  $.ajax({
                      type: 'DELETE',
                      url: `/delete-payment/${payment.id}`,
                      data: {
                          _token: $('meta[name="csrf-token"]').attr("content"),
                      },
                      success: function (response) {
                        window.location.reload();
                      },
                      error: function (xhr) {
                          Swal.fire({
                              title: "",
                              icon: "error",
                              showConfirmButton: true,
                              allowEscapeKey: true,
                              text: xhr.responseJSON.message,
                          });
                      },
                  });
              }
            }
          });
        }

        $("#createModal").on("hidden.bs.modal", function () {
            $("#payment_date").val(null);
            $("#paid_amount").val(null);
            $("#payment_method").val(null).trigger("change");
        });

        $("#store-btn").click(function () {
            const loanId= {!! $loan->id !!}
            let requiredFields = ["payment_date","paid_amount","payment_method"];

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

            $.ajax({
                type: 'POST',
                url: `/store-payment/${loanId}`,
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    'payment_date':$('#payment_date').val(),
                    'paid_amount':$('#paid_amount').val(),
                    'payment_method':$('#payment_method').val()
                },
                success: function (response) {
                    $("#createModal").modal("hide");

                    Swal.fire({
                        title: "",
                        icon: "success",
                        showConfirmButton: true,
                        allowEscapeKey: true,
                        text: response.message,
                    }).then((value) => {
                        window.location.reload();
                    })
                },
                error: function (xhr) {
                    $("#createModal").modal("hide");

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
