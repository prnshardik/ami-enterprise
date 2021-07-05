<nav class="page-sidebar" id="sidebar">
    <div id="sidebar-collapse">
        <div class="admin-block d-flex">
            <div>
                <img src="{{ asset('assets/img/admin-avatar.png') }}" width="45px" />
            </div>
            <div class="admin-info">
                <div class="font-strong">{{ auth()->user()->name }}</div>
                <small>
                    @if(auth()->user()->is_admin == 'y')
                        Administrator 
                    @else
                        User
                    @endif
                </small>
            </div>
        </div>
        <ul class="side-menu metismenu">
            <li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
                <a class="{{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="sidebar-item-icon fa fa-th-large"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>
            <li class="{{ Request::is('users*') ? 'active' : '' }}">
                <a class="{{ Request::is('users*') ? 'active' : '' }}" href="{{ route('users') }}"><i class="sidebar-item-icon fa fa-users"></i>
                    <span class="nav-label">Users</span>
                </a>
            </li>
            <li class="{{ Request::is('products*') ? 'active' : '' }}">
                <a class="{{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products') }}"><i class="sidebar-item-icon fa fa-product-hunt"></i>
                    <span class="nav-label">Products</span>
                </a>
            </li>
            <li class="{{ Request::is('customers*') ? 'active' : '' }}">
                <a class="{{ Request::is('customers*') ? 'active' : '' }}" href="{{ route('customers') }}"><i class="sidebar-item-icon fa fa-users"></i>
                    <span class="nav-label">Customers</span>
                </a>
            </li>
            <li class="{{ Request::is('orders*') ? 'active' : '' }}">
                <a class="{{ Request::is('orders*') ? 'active' : '' }}" href="{{ route('orders') }}"><i class="sidebar-item-icon fa fa-shopping-basket"></i>
                    <span class="nav-label">Orders</span>
                </a>
            </li>
            <li class="{{ (Request::is('payment*') || Request::is('payments.reminders*')) ? 'active' : '' }}">
                <a href="javascript:;" aria-expanded="false">
                    <i class="sidebar-item-icon fa fa-money"></i>
                    <span class="nav-label">Payments</span>
                    <i class="fa fa-angle-left arrow"></i>
                </a>
                <ul class="nav-2-level collapse" aria-expanded="false">
                    <li class="{{ (Request::is('payment*') && !Request::is('payments-reminder*')) ? 'active' : '' }}">
                        <a class="{{ (Request::is('payment*') && !Request::is('payments-reminder*')) ? 'active' : '' }}" href="{{ route('payment') }}"><i class="sidebar-item-icon fa fa-money"></i>
                            <span class="nav-label">Payments</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('payments-reminder*') ? 'active' : '' }}">
                        <a class="{{ Request::is('payments-reminder*') ? 'active' : '' }}" href="{{ route('payments.reminders') }}"><i class="sidebar-item-icon fa fa-money"></i>
                            <span class="nav-label">Payments Reminders</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ (Request::is('tasks*') || Request::is('mytasks*')) ? 'active' : '' }}">
                <a href="javascript:;" aria-expanded="false">
                    <i class="sidebar-item-icon fa fa-tasks"></i>
                    <span class="nav-label">Tasks</span>
                    <i class="fa fa-angle-left arrow"></i>
                </a>
                <ul class="nav-2-level collapse" aria-expanded="false">
                    <li class="{{ Request::is('tasks*') ? 'active' : '' }}">
                        <a class="{{ Request::is('tasks*') ? 'active' : '' }}" href="{{ route('tasks') }}"><i class="sidebar-item-icon fa fa-tasks"></i>
                            <span class="nav-label">Tasks</span>
                        </a>
                    </li>
                    <li>
                        <a class="{{ Request::is('mytasks*') ? 'active' : '' }}" href="{{ route('mytasks') }}"><i class="sidebar-item-icon fa fa-tasks"></i>
                            <span class="nav-label">My Tasks</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ (Request::is('notices*') || Request::is('notice-board')) ? 'active' : '' }}">
                <a href="javascript:;" aria-expanded="false">
                    <i class="sidebar-item-icon fa fa-bullhorn"></i>
                    <span class="nav-label">Notices</span>
                    <i class="fa fa-angle-left arrow"></i>
                </a>
                <ul class="nav-2-level collapse" aria-expanded="false">
                    <li>
                        <a class="{{ Request::is('notice-board') ? 'active' : '' }}" href="{{ route('notice.board') }}"><i class="sidebar-item-icon fa fa-bullhorn"></i>
                            <span class="nav-label">Notices Board</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('notices*') ? 'active' : '' }}">
                        <a class="{{ Request::is('notices*') ? 'active' : '' }}" href="{{ route('notices') }}"><i class="sidebar-item-icon fa fa-bullhorn"></i>
                            <span class="nav-label">Notices</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>