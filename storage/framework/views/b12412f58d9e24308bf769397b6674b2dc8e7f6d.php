<div class="m-stack m-stack--ver m-stack--desktop">
    <!-- BEGIN: Brand -->
    <div class="m-stack__item m-brand  m-brand--skin-dark ">
        <div class="m-stack m-stack--ver m-stack--general">
            <div class="m-stack__item m-stack__item--middle m-brand__logo">
                <?php if(empty(\Auth::user()->company_user_id) != true): ?>
                <a href="/" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png"/>
                </a>
                <?php else: ?>
                <a href="/settings" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png"/>
                </a>

                <?php endif; ?>
            </div>
            <div class="m-stack__item m-stack__item--middle m-brand__tools">
                <!-- BEGIN: Left Aside Minimize Toggle 1 -->
                <!--<a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block 
">
<span></span>
</a>-->
                <!-- END -->
                <!-- BEGIN: Responsive Aside Left Menu Toggler 2 -->
                <!--<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
<span></span>
</a>-->
                <!-- END -->
                <!-- BEGIN: Responsive Header Menu Toggler 3 -->
                <a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                    <span></span>
                </a>
                <!-- END -->
                <!-- BEGIN: Topbar Toggler 4 -->
                <!--<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
<i class="flaticon-more"></i>
</a>-->
                <!-- BEGIN: Topbar Toggler 5 -->
            </div>
        </div>
    </div>
    <!-- END: Brand -->
    <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
        <!-- BEGIN: Horizontal Menu -->
        <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark " id="m_aside_header_menu_mobile_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-dakr m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark "  >
            <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">

                <?php if(empty(\Auth::user()->company_user_id) != true): ?>
                <!--<li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="<?php echo e(route('quotes.index')); ?>" class="m-menu__link ">
                        <span class="m-menu__link-text">
                            <b>Quotes</b>
                        </span>
                    </a>
                </li>-->
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="<?php echo e(route('quotes-v2.index')); ?>" class="m-menu__link ">
                        <span class="m-menu__link-text">
                            <b>Quotes</b>
                        </span>
                    </a>
                </li>
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Clients</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>


                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('companies.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-business"></i>
                                    <span class="m-menu__link-text">
                                        Companies
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('contacts.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-users"></i>
                                    <span class="m-menu__link-text">
                                        Contacts
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Contracts</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('contracts.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-file"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('contractslcl.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-file"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight LCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('surcharges.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-list-1"></i>
                                    <span class="m-menu__link-text">
                                        Surcharge List
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Global Charges</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('globalcharges.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-globe"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('globalchargeslcl.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-globe"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight LCL
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Inland</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('inlands.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Settings</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('prices.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-sellsy"></i>
                                    <span class="m-menu__link-text">
                                        Price levels
                                    </span>
                                </a>
                            </li>
                            <?php if( Auth::user()->type == 'company' ||  Auth::user()->type == 'admin'): ?>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('settings.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-profile"></i>
                                    <span class="m-menu__link-text">
                                        Company's Profile
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('terms.list')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-warning-sign"></i>
                                    <span class="m-menu__link-text">
                                        Terms & Conditions
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('oauth.tokens')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-share"></i>
                                    <span class="m-menu__link-text">
                                        API tokens
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('templates.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon fa fa-envelope-square"></i>
                                    <span class="m-menu__link-text">
                                        Email templates
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('users.home')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-users"></i>
                                    <span class="m-menu__link-text">
                                        Users
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('dashboard.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-graph"></i>
                                    <span class="m-menu__link-text">
                                        Dashboard
                                    </span>
                                </a>
                            </li>
                            <?php if(auth()->check() && auth()->user()->hasRole('administrator')): ?>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('RequestImportation.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-folder-3"></i>
                                    <span class="m-menu__link-text">
                                        Request Importation FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('RequestImportationLcl.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-folder-3"></i>
                                    <span class="m-menu__link-text">
                                        Request Importation LCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('RequestsGlobalchargersFcl.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-folder-3"></i>
                                    <span class="m-menu__link-text">
                                        Request Importation G.C-FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('UploadFile.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-business"></i>
                                    <span class="m-menu__link-text">
                                        Manage Harbors
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('Countries.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-globe"></i>
                                    <span class="m-menu__link-text">
                                        Manage Countries
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('Region.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-globe"></i>
                                    <span class="m-menu__link-text">
                                        Manage Regions
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('settings.companies')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-user-settings"></i>
                                    <span class="m-menu__link-text">
                                        User companies
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('search.index')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-search"></i>
                                    <span class="m-menu__link-text">
                                        Search History
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(Session::has('impersonate')): ?>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="<?php echo e(route('impersonate.revert')); ?>" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-logout"></i>
                                    <span class="m-menu__link-text">
                                        Exit impersonate mode
                                    </span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b><?php echo e(\Auth::user()->name); ?> <?php echo e(\Auth::user()->lastname); ?></b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">

                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="" class="m-menu__link " onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="m-menu__link-text">
                                        <span class="la la-sign-out" style="font-size:23px;"></span> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;<b>Logout </b>
                                    </span>
                                </a>
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                    <?php echo e(csrf_field()); ?>

                                </form>
                            </li>
                        </ul>
                    </div>
                </li>



                <li id="notifications" class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center m-dropdown--mobile-full-width" data-dropdown-toggle="click" data-dropdown-persistent="true" style="margin-top:20px;">
                    <a href="#" class="m-nav__link m-dropdown__toggle newNotification" hidden="true" id="m_topbar_notification_icon">
                        <div class='row'>
                            <div class="col-md-2">
                                <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
                            </div>
                            <span class="m-nav__link-icon">
                                <i class="flaticon-music-2"></i>
                            </span>
                        </div>
                    </a>

                    <a href="#" class="m-nav__link m-dropdown__toggle noNotification">
                        <div class='row'>
                            <div class="col-md-2">
                                <div>

                                </div>
                            </div>
                            <span class="m-nav__link-icon">
                                <i class="flaticon-music-2"></i>
                            </span>
                        </div>
                    </a>
                    <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__header m--align-center" style="background: url(../../assets/app/media/img/misc/notification_bg.jpg); background-size: cover;">
                                <span class="m-dropdown__header-subtitle">
                                    Notifications
                                </span>
                            </div>
                            <div class="m-dropdown__body">
                                <div class="m-dropdown__content">
                                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#topbar_notifications_notifications" role="tab">
                                                New Notifications
                                            </a>
                                        </li>
                                        <li class="nav-item m-tabs__item">
                                            <a class="nav-link m-tabs__link " data-toggle="tab" href="#topbar_notifications_notifications_old" role="tab">
                                                Old Notifications
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="topbar_notifications_notifications" role="tabpanel">
                                            <div class="m-scrollable" data-scrollable="true" data-max-height="250" data-mobile-max-height="200">
                                                <div class="m-list-timeline m-list-timeline--skin-light">
                                                    <div class="m-list-timeline__items notifications">
                                                        <div class="m-list-timeline__item notificationsMenu">
                                                            <span class="m-list-timeline__badge"></span>
                                                            <span class="m-list-timeline__text">
                                                                No notifications
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane " id="topbar_notifications_notifications_old" role="tabpanel">
                                            <div class="m-scrollable" data-scrollable="true" data-max-height="250" data-mobile-max-height="200">
                                                <div class="m-list-timeline m-list-timeline--skin-light">
                                                    <div class="m-list-timeline__items notifications_old">
                                                        <div class="m-list-timeline__item notificationsMenu_old">
                                                            <span class="m-list-timeline__badge"></span>
                                                            <span class="m-list-timeline__text">
                                                                No notifications
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
