@extends('layouts.app')
@section('title','Users')

<meta name="csrf-token" content="{{csrf_token()}}">

@section('content')
<div class="pagetitle d-flex justify-content-end w-100">
    <button type="button" class="btn btn-primary" onclick='$("#createModal").modal("show")'>Add User</button>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h3>Users</h3>
                </div>

                <div class="ibox-content table-responsive">
                    @unless(count($users)==0)
                    <table class="table text-sm" id="users-table" style="width: 100%;">
                        <thead class="text-xs bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3" style="width: 15rem;">Name</th>
                            <th scope="col" class="px-6 py-3" style="width: 12rem;">Phone Number</th>
                            <th scope="col" class="px-6 py-3" style="width: 1rem;"></th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $index => $user)
                            <tr class="bg-white border-b cursor-pointer" style="cursor: pointer;">
                                <td class="px-4 py-4" onclick="updateUser({{$user}})">{{Str::title($user->first_name)}} {{Str::title($user->last_name)}}</td>
                                <td class="px-4 py-4" onclick="updateUser({{$user}})">{{$user->phone_number}}</td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-4" onclick="window.location.href='loan-application/{{$user->id}}'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil mb-1" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                        </svg>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser({{$user}})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3 mb-1" viewBox="0 0 16 16">
                                            <path
                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
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
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>First Name</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control text-capitalize" name="first_name" id="first_name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control text-capitalize" name="last_name" id="last_name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone Number</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control" name="phone_number" id="phone_number">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm float-right" id="store-btn">Add User</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="updateModal" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="userUpdateForm" class="row form-row">
                    <div class="col-md-4" >
                        <div class="form-group">
                            <label>First Name</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control text-capitalize" name="patch_first_name" id="patch_first_name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control text-capitalize" name="patch_last_name" id="patch_last_name">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone Number</label> <span class="text-danger">*</span>
                            <input type="text" class="form-control" name="patch_phone_number" id="patch_phone_number">
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm float-right" id="update-btn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src={{asset('/js/users.js')}} defer type="module"></script>

    <script>
        function updateUser(user){
            user_id=user.id;

            $("#patch_first_name").val(user.first_name);
            $("#patch_last_name").val(user.last_name);
            $("#patch_phone_number").val(user.phone_number);

            $("#updateModal").modal("show");
        }

        function deleteUser(user){
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
                        url: `/delete-user/${user.id}`,
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
    </script>
@endsection

