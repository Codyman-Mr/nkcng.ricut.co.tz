<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold text-black">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item" href="#">Profile</a>

                            <form action="{{route('user-logout')}}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                    </ul>
                </div>

                <div class="logo-element ">
                    <img src="{{asset('/img/logo.png')}}" width="50px" height="50px" alt="">
                </div>
            </li>

            <li>
                <a href="/"><i class="fa fa-th-large"></i><span class="nav-label">Home</span></a>
            </li>

            <li>
                <a href="/loan-application"><i class="fa fa-pencil" aria-hidden="true" style="font-size: 1rem;"></i><span class="nav-label">Apply Loan</span></a>
            </li>

            @can('isAdmin')
                <li>
                    <a href="/loans-pending"><i class="fa fa-spinner" aria-hidden="true" style="font-size: 1rem;"></i><span class="nav-label">Pending Loans</span></a>
                </li>

                <li>
                    <a href="/loans-ongoing"><i class="fa fa-file-text" aria-hidden="true"></i><span class="nav-label">Ongoing Loans</span></a>
                </li>

                <li>
                    <a href="/users"><i class="fa fa-users" aria-hidden="true"></i><span class="nav-label">Users</span></a>
                </li>

                <li>
                    <a href="/repayment-alerts"><i class="fa fa-commenting" aria-hidden="true"></i><span class="nav-label">Loan Repayment Alerts</span></a>
                </li>
                <li  class="dropdown">
                    <a   href="/loans-ongoing"><i class="fa fa-file-text" aria-hidden="true"></i><span class="nav-label">Reports</span></a>
                    <ul class="dropdown-menu" style="margin-left:215px; margin-top:-50px;">
                        <li><a   href="/payment-report"><span  class="nav-label">Payments</span></a></li>
                        
                    </ul>
                </li>

            @endcan
        </ul>
    </div>
</nav>

