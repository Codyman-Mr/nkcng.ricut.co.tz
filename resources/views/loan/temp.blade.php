{{-- Wrapper --}}
        <div class="wrapper wrapper-content animated fadeInRight">

            {{-- Progress Bar --}}
            <div class="row mb-4">
                <div class="col-lg-10">
                    <div class="progress progress-bar-default">
                        <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="43" role="progressbar"
                            class="progress-bar">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="row">
                <form class="col-lg-10" id="loan-application-form">
                    {{-- Personal Details --}}
                    <div class="ibox">
                        <div class="form-section" id="section-1">
                            <div class="ibox-title">
                                <h3>Personal Details</h3>
                            </div>

                            <div class="ibox-content">
                                <div class="row form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            @if (auth()->user()->role === 'admin')
                                                <!-- Directly check the role -->
                                                <input type="text" class="form-control text-capitalize" name="first_name"
                                                    id="first_name" value="">
                                            @else
                                                <input type="text" class="form-control text-capitalize" name="first_name"
                                                    id="first_name" value="{{ $borrower->first_name }}" disabled>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            @if (auth()->user()->role === 'admin')
                                                <!-- Directly check the role -->
                                                <input type="text" class="form-control text-capitalize" name="last_name"
                                                    id="last_name" value="">
                                            @else
                                                <input type="text" class="form-control text-capitalize" name="last_name"
                                                    id="last_name" value="{{ $borrower->last_name }}" disabled>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            @if (auth()->user()->role === 'admin')
                                                <!-- Directly check the role -->
                                                <input type="text" class="form-control text-capitalize"
                                                    name="phone_number" id="phone_number" value="">
                                            @else
                                                <input type="text" class="form-control text-capitalize"
                                                    name="phone_number" id="phone_number"
                                                    value="{{ $borrower->phone_number }}" disabled>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input type="date" class="form-control" name="dob" id="dob"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Gender</label>
                                        <div class="form-control d-md-flex mb-4" id="gender-box">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" id="male"
                                                    value="male" checked>
                                                <label class="form-check-label" for="parent_login">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender"
                                                    id="female" value="female">
                                                <label class="form-check-label" for="staff_login">Female</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Driver's License or NIDA number</label>
                                            <span class="text-danger">*</span>
                                            <input type="text" class="form-control" name="nida_no" id="nida_no" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>ID Image - Front View</label>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="nida_front_view" id="nida_front_view"
                                                style="padding: 3px 12px" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" class="form-control text-capitalize" name="address"
                                                id="address">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Letter of Identification from Local Government</label>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="gvt_identification_letter"
                                                id="gvt_identification_letter" style="padding:3px 12px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-end mt-4">
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-info btn-sm float-right next-btn">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Details --}}
                    <div class="ibox">
                        <div class="form-section" id="section-2">
                            <div class="ibox-title">
                                <h3>Vehicle Details</h3>
                            </div>

                            <div class="ibox-content">
                                <div class="row form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vehicle Name <span class="text-muted">(Model)</span></label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="vehicle_name" id="vehicle_name" value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vehicle Type</label>
                                            <select class="form-control select2" id="vehicle_type" name="vehicle_type"
                                                style="height: 2rem !important; width: 100%">
                                                <option value=""></option>
                                                <option value="car">Car</option>
                                                <option value="bajaj">Bajaj</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Plate Number</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="plate_number" id="plate_number" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Vehicle Registration Card (Original Copy)</label>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="vehicle_registration_card"
                                                id="vehicle_registration_card" style="padding: 3px 12px" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fuel Type</label>
                                            <select class="form-control select2" id="fuel_type" name="fuel_type"
                                                style="height: 2rem !important; width: 100%">
                                                <option value=""></option>
                                                <option value="petrol">Petrol</option>
                                                <option value="diesel">Diesel</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-md-5">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm float-left prev-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                                            </svg>
                                            Previous
                                        </button>

                                        <button type="button" class="btn btn-outline-info btn-sm float-right next-btn">
                                            Next
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Guarantor Details --}}
                    <div class="ibox">
                        <div class="form-section" id="section-3">
                            <div class="ibox-title">
                                <h3>Guarantor Details</h3>
                            </div>

                            <div class="ibox-content">
                                <h4 class="text-info">Guarantor from Local Government</h4>
                                <br><br>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="gvt_guarantor_first_name" id="gvt_guarantor_first_name"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="gvt_guarantor_last_name" id="gvt_guarantor_last_name"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="gvt_guarantor_phone_number" id="gvt_guarantor_phone_number"
                                                value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Driver's License or NIDA number</label>
                                            <span class="text-danger">*</span>
                                            <input type="text" class="form-control" name="gvt_guarantor_nida_no"
                                                id="gvt_guarantor_nida_no" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>ID Image - Front View</label>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="gvt_guarantor_nida_front_view"
                                                id="gvt_guarantor_nida_front_view" style="padding: 3px 12px" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supporting Letter from Local Government Guarantor</label><span
                                                class="text-danger">*</span>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="gvt_guarantor_letter"
                                                id="gvt_guarantor_letter" style="padding:3px 12px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ibox-content">
                                <h4 class="text-info">Guarantor with Permanent Employment contract from Government or
                                    Private
                                    Sector</h4>
                                <br><br>

                                <div class="row form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="private_guarantor_first_name" id="private_guarantor_first_name"
                                                value="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="private_guarantor_last_name" id="private_guarantor_last_name"
                                                value="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" class="form-control text-capitalize"
                                                name="private_guarantor_phone_number" id="private_guarantor_phone_number"
                                                value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>National ID Number (NIDA) </label>
                                            <span class="text-danger">*</span>
                                            <input type="text" class="form-control" name="private_guarantor_nida_no"
                                                id="private_guarantor_nida_no" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>ID Image - Front View</label>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="private_guarantor_nida_front_view"
                                                id="private_guarantor_nida_front_view" style="padding: 3px 12px" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supporting Letter from Guarantor</label><span
                                                class="text-danger">*</span>
                                            <input type="file" accept="image/jpeg, image/png, image/jpg, .pdf"
                                                class="form-control" name="private_guarantor_letter"
                                                id="private_guarantor_letter" style="padding:3px 12px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-5">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm float-left prev-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-left mr-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                                            </svg>
                                            Previous
                                        </button>

                                        <button type="button" class="btn btn-primary btn-sm float-right"
                                            id="submit-btn">
                                            Submit
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-right ml-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>





<div x-data="termsModal()" x-init="checkAgreement()" class="relative">
        <!-- Modal -->
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
        </div>
    </div>
