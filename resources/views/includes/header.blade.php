<div class="logo-container">
    {{-- <a href="../" class="logo">
        <img src="{{ asset('img/logo.png') }}" height="35" alt="ICFMS Logo" />
    </a> --}}
    <h2 style="float: left;font-family:'Noto Sans','Comic Sans MS';color: #0088CC;margin-top:10px;margin-left:25px;font-weight:bold;">
        <span class="alternative-fonts">IcAUMS 2023</span>
    </h2>
    <div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
        <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
    </div>
</div>

<!-- start: search & user box -->
<div class="header-right">
    <span class="separator"></span>

    <div id="userbox" class="userbox">
        <a href="#" data-toggle="dropdown">
            {{-- <figure class="profile-picture">
                <img src="asset/images/!logged-user.jpg" alt="Joseph Doe" class="img-circle" data-lock-picture="asset/images/!logged-user.jpg" />
            </figure> --}}
            <div class="profile-info">
                <span class="name">{{ Auth::user()->name }}</span>
                <span class="role">{{ (session('icfms_tipe_login') == 1 ? 'Administrator' : 'Member') }}</span>
            </div>

            <i class="fa custom-caret"></i>
        </a>

        <div class="dropdown-menu">
            <ul class="list-unstyled">
                <li class="divider"></li>
                {{-- <li>
                    <a role="menuitem" tabindex="-1" href="pages-user-profile.html"><i class="fa fa-user"></i> My Profile</a>
                </li> --}}
                <li>
                    <a role="menuitem" tabindex="-1" href="#" id="mn-changePass"><i class="fa fa-lock"></i> Change Password</a>
                </li>
                <li>
                    <a role="menuitem" tabindex="-1" href="{{ route('logout') }}"><i class="fa fa-power-off"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</div>
