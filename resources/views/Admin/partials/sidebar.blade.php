<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header bg-white-5">
        <!-- Logo -->
        <a class="font-w600 text-dual" href="/">
            <i class="fa fa-circle-notch text-primary"></i>
            <span class="smini-hide">
                <span class="font-w700 font-size-h5">Esharti</span>
            </span>
        </a>
        <!-- END Logo -->
    </div>
    <!-- END Side Header -->

    <!-- Side Navigation -->
    <div class="content-side content-side-full">
        <ul class="nav-main">
            <li class="nav-main-heading"><i class="nav-main-link-icon fa fa-tachometer-alt"></i> @lang('app.dashboard')</li>
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('/') || request()->is('/admin') ? ' active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="nav-main-link-icon fa fa-circle"></i>
                    <span class="nav-main-link-name">@lang('app.overview')</span>
                </a>
            </li>
            <li class="nav-main-heading"><i class="nav-main-link-icon fas fa-database"></i> @lang('app.mobile_app')</li>
            @if(Auth::user()->hasPermission('articles.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/articles') || request()->is('sysAdmin/articles/*') ? ' active' : '' }}" href="{{ route('articles.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.articles')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('courses.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/courses') || request()->is('sysAdmin/courses/*') ? ' active' : '' }}" href="{{ route('courses.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.courses')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('lessons.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/lessons') || request()->is('sysAdmin/lessons/*') ? ' active' : '' }}" href="{{ route('lessons.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.lessons')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('dictionaries.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/dictionaries') || request()->is('sysAdmin/dictionaries/*') ? ' active' : '' }}" href="{{ route('dictionaries.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.dictionaries')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('live-event.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/live-event') || request()->is('sysAdmin/live-event/*') ? ' active' : '' }}" href="{{ route('live-event.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.live-event')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('certificates.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/certificates') || request()->is('sysAdmin/certificates/*') ? ' active' : '' }}" href="{{ route('certificates.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.certificates')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('plans.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/plans') || request()->is('sysAdmin/plans/*') ? ' active' : '' }}" href="{{ route('plans.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.plans')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('live-support-request.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/plans') || request()->is('sysAdmin/live-support-request/*') ? ' active' : '' }}" href="{{ route('live-support-request.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.live-support-request')</span>
                    </a>
                </li>
            @endif


            <li class="nav-main-heading"><i class="nav-main-link-icon fas fa-dollar-sign"></i> @lang('app.finance')</li>
            @if(Auth::user()->hasPermission('transactions.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/transactions') || request()->is('sysAdmin/transactions/*') ? ' active' : '' }}" href="{{ route('transactions.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.transactions')</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/paytabstransactions') || request()->is('sysAdmin/paytabstransactions/*') ? ' active' : '' }}" href="{{ route('paytabstransactions.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.paytabs_transactions')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('users.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/users*') || request()->is('sysAdmin/users/create') ? ' active' : '' }}" href="{{ route('users.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.users')</span>
                    </a>
                </li>
            @endif

            <li class="nav-main-heading"><i class="nav-main-link-icon fas fa-comment"></i> @lang('app.messages')</li>
            @if(Auth::user()->hasPermission('contactus.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/contactus') || request()->is('sysAdmin/contactus/*') ? ' active' : '' }}" href="{{ route('contactus') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.contact_us')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('pushNotifications.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/pushNotifications') ? ' active' : '' }}" href="{{ route('pushNotifications.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.PushNotifications')</span>
                    </a>
                </li>
            @endif
            <li class="nav-main-heading"><i class="nav-main-link-icon si si-settings"></i> @lang('app.settings')</li>
            @if(Auth::user()->hasPermission('admins.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/admins*') || request()->is('sysAdmin/admins/create') ? ' active' : '' }}" href="{{ route('admins.list') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.admins')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('banner.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/banner*') || request()->is('sysAdmin/banner/create') ? ' active' : '' }}" href="{{ route('banner.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.banner')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('settings.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/settings*') || request()->is('sysAdmin/settings/create') ? ' active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.fixed_values')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('roles.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/role*') ? ' active' : '' }}" href="{{ route('role.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.roles')</span>
                    </a>
                </li>
            @endif
            @if(Auth::user()->hasPermission('permissions.manage'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->is('sysAdmin/permission*') ? ' active' : '' }}" href="{{ route('permission.index') }}">
                        <i class="nav-main-link-icon fa fa-circle"></i>
                        <span class="nav-main-link-name">@lang('app.permissions')</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
