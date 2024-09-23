
@php
 function convertPhoneNumberToInternationalFormat(String $phoneNumber){
    if (preg_match('/^06\d{8}|07\d{8}$/', $phoneNumber)) {
        return $phoneNumber = '+255' . substr($phoneNumber, 1);
    }else{
        return $phoneNumber;
    }
 }

//  convertPhoneNumberToInternationalFormat('0768591818');
@endphp

@extends('layouts.app')
@section('title','Repayment Alerts')

<meta name="csrf-token" content="{{csrf_token()}}">

@section('content')
<div class="pagetitle d-flex justify-content-end p-0">
    <button type="button" class="btn btn-sm btn-outline-info p-2" onclick='sendPaymentReminders();'>
        <span class="text-sm" style="font-size:0.7rem;">Send Payment Reminder(s) <i class="bi bi-bell-fill ml-2"></i></span>
    </button>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12 p-0">
            <div class="ibox">
                <div class="ibox-title">
                    <h3>Repayment Alerts</h3>
                </div>

                <div class="ibox-content table-responsive">
                    @unless(count($unpaid_loans)==0)
                    <table id="repayments-table" class="table text-sm" id="users-table" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3" style="width: 10rem;">Name</th>
                            <th scope="col" class="px-6 py-3" style="width: 2rem;">Required Amount</th>
                            <th scope="col" class="px-6 py-3" style="width: 2rem;">Paid Amount</th>
                            <th scope="col" class="px-6 py-3" style="width: 7rem;">Reminder</th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach ($unpaid_loans as $index => $loan)
                            <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                <td class="py-4">{{Str::title($loan->user->first_name)}} {{Str::title($loan->user->last_name)}} &nbsp; ({{convertPhoneNumberToInternationalFormat($loan->user->phone_number)}})</td>
                                <td class="py-4">Tsh. {{number_format($loan->loan_required_amount)}}</td>
                                <td class="py-4">Tsh. {{number_format($loan->payments->sum('paid_amount'))}}</td>
                                <td class="py-4">Habari {{Str::title($loan->user->first_name)}} {{Str::title($loan->user->last_name)}}, Tunakuandikia kukukumbusha kwamba bado unadaiwa kiasi cha  {{number_format($loan->loan_required_amount - $loan->payments->sum('paid_amount'))}}. Tafadhali endelea kulipia deni lako ili kuepuka usumbufu wowote.</td>
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

@section('scripts')
    <script src={{asset('/js/repayment-alerts.js')}} defer type="module"></script>

    <script>
        function sendPaymentReminders(){
            const recipients={!! $unpaid_loans !!}.map((recipient) => {
                return recipient.user.phone_number;
            });

            Swal.fire({
              title:"Are you sure ?",
              text:`You are about to send SMS notification(s) to ${recipients.length} recipient(s). Please confirm if you want to proceed with this action.`,
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
                    url: `/send-repayment-reminders`,
                    method: 'POST',
                    data:{
                        draw: Math.floor(Math.random() * 1000),
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        recipients,
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
                    error: function(xhr) {
                        Swal.close();

                        Swal.fire({
                            title: "",
                            icon: "error",
                            showConfirmButton: true,
                            allowEscapeKey: true,
                            text: xhr.responseJSON.message,
                        });
                    }
                });
              }
            }
            });
        }
    </script>
@endsection

