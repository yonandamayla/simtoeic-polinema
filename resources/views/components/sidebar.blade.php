<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img src="{{ asset('img/simtoeic0.png') }}" alt="logo" style="height: 40px; margin: bottom 3px;">
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <img src="{{ asset('img/simtoeic-1.png') }}" alt="logo" style="height: 50px; vertical-align:bottom;">
        </div>
        <ul class="sidebar-menu">
            @if(Auth::check() && Auth::user()->isAdmin())
                <!-- ADMIN SIDEBAR MENU -->
                <!-- Dashboard -->
                <li class="{{ Request::is('dashboard-admin') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-fire"></i><span>Dashboard</span>
                    </a>
                </li>


                <!-- Management Dropdown -->
                <li
                    class="nav-item dropdown {{ in_array(Request::segment(1), ['exam-results', 'users']) || Request::is('admin/verification-requests*') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-cogs"></i> <span>Management</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('exam-results*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('exam-results') }}">Exam Results</a>
                        </li>
                        <li class="{{ Request::is('users*') || Request::is('users*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('users') }}">Users</a>
                        </li>
                        <li class="{{ Request::is('admin/verification-requests*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.verification.requests.index') }}">Verification
                                Requests</a>
                        </li>
                    </ul>
                </li>

                <!-- Notices Dropdown -->
                <li
                    class="nav-item dropdown {{ in_array(Request::segment(1), ['announcements', 'faqs', 'telegram']) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-bullhorn"></i> <span>Notices</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ Request::is('announcements*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('announcements') }}">Announcements</a>
                        </li>
                        <li class="{{ Request::is('faqs*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('faqs') }}">FAQ</a>
                        </li>
                        <li class="{{ Request::is('telegram*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('telegram.index') }}">Telegram Settings</a>
                        </li>
                    </ul>
                </li>

                <!-- Profile -->
                <li class="{{ Request::is('admin/profile') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('admin/profile') }}">
                        <i class="fas fa-user"></i> <span>Profile</span>
                    </a>
                </li>
            @else
                <!-- OTHER USERS SIDEBAR MENU (Staff, Alumni, Student, Lecturer) -->
                <!-- Dashboard -->
                <li class="{{ Request::is('*/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url(Auth::user()?->role . '/dashboard') }}">
                        <i class="fas fa-fire"></i><span>Dashboard</span>
                    </a>
                </li>

                <!-- Registration -->
                <li class="{{ Request::is('*/registration*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url(Auth::user()?->role . '/registration') }}">
                        <i class="fas fa-clipboard-list"></i> <span>Registration</span>
                    </a>
                </li>

                <!-- Request (for students only) -->
                @if(Auth::user()?->role === 'student')
                    <li class="{{ Request::is('*/verification-request*') || Request::is('*/request*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('student.request.index') }}">
                            <i class="fas fa-certificate"></i> <span>Request</span>
                        </a>
                    </li>
                @endif

                <!-- FAQs -->
                <li class="{{ Request::is('faq*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('faq') }}">
                        <i class="fas fa-question-circle"></i><span>FAQs</span>
                    </a>
                </li>

                <!-- Profile -->
                <li class="{{ Request::is('*/profile') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url(Auth::user()?->role . '/profile') }}">
                        <i class="fas fa-user"></i> <span>Profile</span>
                    </a>
                </li>
            @endif

            <!-- Logout (Common for all users) -->
            <li>
                <a class="nav-link text-danger" href="{{ route('auth.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </aside>
</div>