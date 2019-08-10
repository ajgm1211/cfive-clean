<div class="m-stack m-stack--ver m-stack--desktop">
    <!-- BEGIN: Brand -->
    <div class="m-stack__item m-brand  m-brand--skin-dark ">
        <div class="m-stack m-stack--ver m-stack--general">
            <div class="m-stack__item m-stack__item--middle m-brand__logo">
                @if(empty(\Auth::user()->company_user_id) != true)
                <a href="/" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png"/>
                </a>
                @else
                <a href="/settings" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png"/>
                </a>

                @endif
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

                @if(empty(\Auth::user()->company_user_id) != true)
                <!--<li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
<a  href="{{route('quotes.index')}}" class="m-menu__link ">
<span class="m-menu__link-text">
<b>Quotes</b>
</span>
</a>
</li>
<li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
<a  href="{{route('quotes-v2.index')}}" class="m-menu__link ">
<span class="m-menu__link-text">
<b>Quotes</b>
</span>
</a>
</li>-->
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Quotes</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>


                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                           <!--
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('quotes.index')}}" class="m-menu__link ">
                                    <span class="m-menu__link-text">
                                        <b>Quotes</b>
                                    </span>
                                </a>
                            </li>-->
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('quotes-v2.index')}}" class="m-menu__link ">
                                    <span class="m-menu__link-text">
                                        <b>Quotes V2 (Beta)</b>
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
                                <a  href="{{route('companies.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-business"></i>
                                    <span class="m-menu__link-text">
                                        Companies
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('contacts.index')}}" class="m-menu__link ">
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
                                <a  href="{{route('contracts.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-file"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('contractslcl.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-file"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight LCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('surcharges.index')}}" class="m-menu__link ">
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
                                <a  href="{{route('globalcharges.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-globe"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('globalchargeslcl.index')}}" class="m-menu__link ">
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
                                <a  href="{{route('inlands.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            @hasrole('administrator')

                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('inlandL.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Locations
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('inlandD.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Distance
                                    </span>
                                </a>
                            </li>
                            @endhasrole
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
                            @if(\Auth::user()->type=='admin' || \Auth::user()->type=='company' || \Auth::user()->type=='subuser')
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('prices.index') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon la la-sellsy"></i>
                                        <span class="m-menu__link-text">
                                            Price levels
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{route('UserConfiguration.index')}}" class="m-menu__link ">
                                        <i class="m-menu__link-icon la la-envelope"></i>
                                        <span class="m-menu__link-text">
                                            Email Notifications
                                        </span>
                                    </a>
                                </li>
                            @endif
                            @if(\Auth::user()->type=='admin' || \Auth::user()->type=='company')

                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('settings.index') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-profile"></i>
                                        <span class="m-menu__link-text">
                                            Company Profile
                                        </span>
                                    </a>
                                </li>
                                <!--
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('terms.list') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-warning-sign"></i>
                                        <span class="m-menu__link-text">
                                            Terms & Conditions
                                        </span>
                                    </a>
                                </li>-->
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('termsv2.list') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-warning-sign"></i>
                                        <span class="m-menu__link-text">
                                            Terms & Conditions 
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('remarks.list') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-book"></i>
                                        <span class="m-menu__link-text">
                                            Remarks
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                    <a  href="#" class="m-menu__link m-menu__toggle">
                                        <i class="m-menu__link-icon flaticon-share"></i>
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="m-menu__link-text">
                                            API Settings
                                        </span>                                    
                                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">

                                        <ul class="m-menu__subnav">
                                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                                <a  href="{{ route('oauth.tokens') }}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon flaticon-refresh"></i>
                                                    <span class="m-menu__link-text">
                                                        API tokens
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                                <a  href="{{route('api.settings')}}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon flaticon-network"></i>
                                                    <span class="m-menu__link-text">
                                                        API Integrations
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @if(\Auth::user()->type=='admin' || \Auth::user()->type=='company' || \Auth::user()->type=='subuser')
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('templates.index') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon fa fa-envelope-square"></i>
                                        <span class="m-menu__link-text">
                                            Email templates
                                        </span>
                                    </a>
                                </li>
                                <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                    <a  href="{{ route('dashboard.index') }}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-graph"></i>
                                        <span class="m-menu__link-text">
                                            Dashboard
                                        </span>
                                    </a>
                                </li>
                            @endif
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{ route('users.home') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-users"></i>
                                    <span class="m-menu__link-text">
                                        Users
                                    </span>
                                </a>
                            </li>
                            @if(\Auth::user()->type=='admin' || \Auth::user()->type=='data_entry')

                            <!-- Sub- Menus --------------------------------------------------------------- -->

                            <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                <a  href="#" class="m-menu__link m-menu__toggle">
                                    <i class="m-menu__link-icon la la-arrow-circle-o-up"></i>
                                    &nbsp;&nbsp;&nbsp;
                                    <span class="m-menu__link-text">
                                        Manage Requests
                                    </span>                                    
                                    <i class="m-menu__hor-arrow la la-angle-down"></i>
                                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">

                                    <ul class="m-menu__subnav">
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{ route('RequestImportation.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-folder-3"></i>
                                                <span class="m-menu__link-text">
                                                    Request Importation FCL
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{ route('RequestImportationLcl.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-folder-3"></i>
                                                <span class="m-menu__link-text">
                                                    Request Importation LCL
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{ route('RequestsGlobalchargersFcl.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-folder-3"></i>
                                                <span class="m-menu__link-text">
                                                    Request Importation G.C-FCL
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                <a  href="#" class="m-menu__link m-menu__toggle">
                                    <i class="m-menu__link-icon la la-ship"></i>
                                    &nbsp;&nbsp;&nbsp;
                                    <span class="m-menu__link-text">
                                        Manage G.C.
                                    </span>                                    
                                    <i class="m-menu__hor-arrow la la-angle-down"></i>
                                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                                    <ul class="m-menu__subnav">

                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{ route('gcadm.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-globe"></i>
                                                <span class="m-menu__link-text">
                                                    Administrator FCL
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{ route('gclcladm.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-globe"></i>
                                                <span class="m-menu__link-text">
                                                    Administrator LCL
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                <a  href="#" class="m-menu__link m-menu__toggle">
                                    <i class="m-menu__link-icon la la-map-marker"></i>
                                    &nbsp;&nbsp;&nbsp;
                                    <span class="m-menu__link-text">
                                        Places And Carriers
                                    </span>                                    
                                    <i class="m-menu__hor-arrow la la-angle-down"></i>
                                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">

                                    <ul class="m-menu__subnav">
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('UploadFile.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-business"></i>
                                                <span class="m-menu__link-text">
                                                    Harbors
                                                </span>
                                            </a>
                                        </li>

                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('Countries.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-globe"></i>
                                                <span class="m-menu__link-text">
                                                    Countries
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('Region.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-globe"></i>
                                                <span class="m-menu__link-text">
                                                    Regions
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('managercarriers.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-ship"></i>
                                                <span class="m-menu__link-text">
                                                    Manage Carriers
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('CarrierImportation.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-dropbox"></i>
                                    <span class="m-menu__link-text">
                                        Carrier Auto Importation
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('settings.companies')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-user-settings"></i>
                                    <span class="m-menu__link-text">
                                        User companies
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('search.list')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-search"></i>
                                    <span class="m-menu__link-text">
                                        Search History
                                    </span>
                                </a>
                            </li>
                            @endif
                            @if(Session::has('impersonate'))
                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                <a  href="{{route('impersonate.revert')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-logout"></i>
                                    <span class="m-menu__link-text">
                                        Exit impersonate mode
                                    </span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"  data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                    <a  href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>{{\Auth::user()->name}} {{\Auth::user()->lastname}}</b>
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
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="m-dropdown__header-subtitle" style="font-size: 15px;">
                                            <strong>Notifications</strong>
                                        </span>
                                    </div>
                                    <style>
                                        .setting:hover {
                                            background-color: gray;
                                        }
                                    </style>
                                    <div class="col-md-6" title="Notification settings">
                                        <a href="{{route('UserConfiguration.index')}}" >
                                            <i  class="la la-cog pull-right setting" style="font-weight: bold; color:white; font-size: 23px;"></i>
                                        </a>
                                    </div>
                                </div>
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
