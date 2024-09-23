<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <link href={{asset('css/bootstrap.min.css')}} rel="stylesheet">
    <link href={{asset('font-awesome/css/font-awesome.css')}} rel="stylesheet">
    <link href={{asset('css/plugins/jasny/jasny-bootstrap.min.css')}} rel="stylesheet">

    <link href={{asset('css/style.css')}} rel="stylesheet">
    <link href={{asset('css/animate.css')}} rel="stylesheet">

    <link rel="stylesheet" href={{asset("css/plugins/sweetalert/sweetalert.css")}}>

    <!-- datatable -->
    <link href="{{asset('css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

    <!-- Select2 -->
    <link href="{{asset('css/plugins/select2/select2.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/plugins/select2/select2-bootstrap4.min.css')}}" rel="stylesheet" />

    {{-- Bootstrap Icons --}}
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />

    <script src={{asset('js/jquery-3.1.1.min.js')}}></script>
    <script src={{asset('js/popper.min.js')}}></script>
    <script src={{asset('js/bootstrap.js')}}></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('styles')

    <style>
        .text-sm{
            font-size: 0.875rem;
        }

        .iframe-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-top: 56.25%;
        }

        .iframe-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .swal2-confirm {
            border: none !important;
            box-shadow: none !important;
        }

        .select2-container {
            z-index: 10050 !important;
        }

        .select2-container--default
        .select2-selection--single {
            height: 36px;
        }

        .select2-container--default
        .select2-selection--multiple {
            height: auto !important;
            padding-bottom: 8.9px !important;
        }

        .select2-container--default
        .select2-selection--single
        .select2-selection__arrow
        b {
            border-color: #ddd transparent transparent;
            border-style: solid;
            border-width: 6px 6px 0;
            height: 0;
            left: 50%;
            margin-left: -10px;
            margin-top: 2px;
            position: absolute;
            top: 50%;
            width: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 34px;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #E5E6E7;
            border-radius: 0rem;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #E5E6E7;
            border-radius: 0rem;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(10, 280, 1, 0.5);
            z-index: 9999;
            display: none;
        }
    </style>
</head>

<body class="fixed-navigation">
    <div id="wrapper">
        @include('layouts.sidenav')

        <div id="page-wrapper" class="gray-bg">
            @include('layouts.headernav')
            <div class="wrapper wrapper-content">
                @yield('content')
            </div>
        </div>
    </div>

    <div id="overlay"></div>
</body>

<!-- Main Scripts -->
<script src={{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}></script>
<script src={{asset('js/plugins/slimscroll/jquery.slimscroll.min.js')}}></script>
<script src="{{asset('js/plugins/sweetalert/sweetalert.min.js')}}" defer></script>
<script src="{{asset('js/main.js')}}" defer type="module"></script>

<!-- datatable -->
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Custom and plugin javascript -->
<script src={{asset('js/inspinia.js')}}></script>
<script src={{asset('js/plugins/pace/pace.min.js')}}></script>

<!-- jQuery UI -->
<script src={{asset('js/plugins/jquery-ui/jquery-ui.min.js')}}></script>

<!-- Input Mask-->
<script src={{asset('js/plugins/jasny/jasny-bootstrap.min.js')}}></script>

<!-- Select2 -->
<script src="{{asset('js/plugins/select2/select2.full.min.js')}}"></script>

<footer class="footer" style="position: fixed !important;">
    <span>&copy; 2024 RICUT Co. All rights reserved.</span>
</footer>

@yield('scripts')
</body>
</html>

