<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">

        <ul class="list-unstyled topnav-menu float-end mb-0">

            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ms-1">
                    {{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i> 
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                        <i class="ri-logout-box-line"></i>
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>

            
        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="#" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="{{ asset('assets/images/logo-sm.png')}}" alt="" height="24">
                </span>
                <span class="logo-lg fs-3 text-white">
                打ち出の小槌
            </span></a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect waves-light">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <li>
                <!-- Mobile menu toggle (Horizontal Layout)-->
                <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </li>   

        </ul>
        <div class="clearfix"></div>
    </div>
</div>
<!-- end Topbar -->