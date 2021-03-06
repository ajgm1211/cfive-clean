<div class="m-stack m-stack--ver m-stack--desktop">
    <!-- BEGIN: Brand -->
    <div class="m-stack__item m-brand  m-brand--skin-dark ">
        <div class="m-stack m-stack--ver m-stack--general">
            <div class="m-stack__item m-stack__item--middle m-brand__logo">
                @if(empty(\Auth::user()->company_user_id) != true)
                <a href="{{url('/api/search')}}" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png" />
                </a>
                @else
                <a href="/settings" class="m-brand__logo-wrapper">
                    <img alt="" src="/logo.png" />
                </a>

                @endif
            </div>
            
        </div>
    </div>
    <!-- END: Brand -->
    <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
        <!-- BEGIN: Horizontal Menu -->
        <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark "
            id="m_aside_header_menu_mobile_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_header_menu"
            class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-dakr m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
            <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">

                @if(empty(\Auth::user()->company_user_id) != true)
                <!-- <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="{{route('quotes-v2.index')}}" class="m-menu__link ">
                        <span class="m-menu__link-text">
                            <b>Quotes</b>
                        </span>
                    </a> -->
                    
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('quotes-v2.index', 'quotes-v2.show', 'quotes-v2.search', 'quote.index') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{url('/api/search')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-graphic-1"></i>
                                    <span class="m-menu__link-text">
                                        Rate Finder
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{url('/api/quotes')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-coins"></i>
                                    <span class="m-menu__link-text">
                                        Quotes
                                    </span>
                                </a>
                            </li>
                     
                        </ul>
                    </div>
                </li> 
                @role('administrator|company|subuser')
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('companies.index', 'companies.show', 'contacts.index') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('companies.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-industry"></i>
                                    <span class="m-menu__link-text">
                                        Companies
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('contacts.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-users"></i>
                                    <span class="m-menu__link-text">
                                        Contacts
                                    </span>
                                </a>
                            </li>


                        </ul>
                    </div>
                </li>
                @endrole
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('new.contracts.index', 'contractslcl.index', 'surcharges.index', 'new.contracts.edit', 'Request.importaion.lcl', 'contractslcl.add') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('new.contracts.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-map-location"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                                <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                    <a href="{{route('new.contracts.lcl.index')}}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-route"></i>
                                        <span class="m-menu__link-text">
                                            Sea Freight LCL
                                        </span>
                                    </a>
                                </li> 
                            @role('administrator|company')
                                <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                    <a href="{{route('surcharges.index')}}" class="m-menu__link ">
                                        <i class="m-menu__link-icon flaticon-list-1"></i>
                                        <span class="m-menu__link-text">
                                            Surcharge List
                                        </span>
                                    </a>
                                </li>
                            @endrole
                        </ul>
                    </div>
                </li>   
                @role('administrator|company')

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('globalcharges.index', 'globalchargeslcl.index', 'globalchargesapi.index', 'RequestsGlobalchargersFcl.create', 'RequestsGlobalchargersLcl.create', 'globalchargesapi') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">
                                    <b>Local Charges</b>
                                </span>
                            </span>
                        </span>
                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('globalcharges.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-line-chart"></i>
                                    <span class="m-menu__link-text">
                                        Local Charges FCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('globalchargeslcl.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-bar-chart"></i>
                                    <span class="m-menu__link-text">
                                        Local Charges LCL
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('sale_term_v3.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-web"></i>
                                    <span class="m-menu__link-text">
                                        Sale Templates FCL
                                    </span>
                                </a>
                            </li>

                            @hasrole('administrator')
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('globalchargesapi.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-external-link-square"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight API
                                    </span>
                                </a>
                            </li>
                            @endrole
                        </ul>
                    </div>
                </li>

                

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('inlands.index', 'inland.edit', 'inlandD.find', 'UploadFile.index', 'provinces.index') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{url('api/inlands')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-truck"></i>
                                    <span class="m-menu__link-text">
                                        Sea Freight FCL
                                    </span>
                                </a>
                            </li>
                            
                            @hasrole('administrator')

                            <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('UploadFile.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-ship"></i>
                                                <span class="m-menu__link-text">
                                                    Harbors
                                                </span>
                                            </a>
                                        </li>

                                        <li class="m-menu__item "  data-redirect="true" aria-haspopup="true">
                                            <a  href="{{route('provinces.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon flaticon-placeholder"></i>
                                                <span class="m-menu__link-text">
                                                    Provinces
                                                </span>
                                            </a>
                                        </li>

                            <!-- <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('inlandL.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Locations
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('inlandD.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-truck"></i>
                                    <span class="m-menu__link-text">
                                        Distance
                                    </span>
                                </a>
                            </li>-->
                            @endhasrole
                        </ul>
                    </div>
                </li>
                @endrole
                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel {{ ! Route::is('settings.index', 'termsv2.list', 'remarks.list', 'oauth.tokens', 'api.settings', 'users.home', 'prices.index', 'prices.add', 'UserConfiguration.index', 'templates.index', 'dashboard.index', 'ContainerCalculation.index', 'transit_time.index', 'settings.companies',  'search.list', 'impersonate.revert') ?: 'active-link' }}" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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
                            @role('administrator|company')

                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('settings.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-profile"></i>
                                    <span class="m-menu__link-text">
                                        Company Profile
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('termsv2.list') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-sticky-note"></i>
                                    <span class="m-menu__link-text">
                                        Terms & Conditions
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{url('api/providers')}}" class="m-menu__link "> 
                                    <i class="m-menu__link-icon la la-cube"></i>
                                    <span class="m-menu__link-text">
                                        Providers
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('remarks.list') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-comment"></i>
                                    <span class="m-menu__link-text">
                                        Remarks
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('users.home') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-users"></i>
                                    <span class="m-menu__link-text">
                                        Users
                                    </span>
                                </a>
                            </li>
                            @endrole
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('user.info') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-user"></i>
                                    <span class="m-menu__link-text">
                                        My profile 
                                    </span>
                                </a>
                            </li>
                            @if(Session::has('impersonate'))
                                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"
                                    data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                    <a href="#" class="m-menu__link m-menu__toggle">
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
                                                <a href="{{ route('oauth.tokens') }}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon flaticon-refresh"></i>
                                                    <span class="m-menu__link-text">
                                                        API tokens
                                                    </span>
                                                </a>
                                            </li>
                                            <!--<li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                                <a href="{{route('api.settings')}}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon flaticon-network"></i>
                                                    <span class="m-menu__link-text">
                                                        External API Integrations
                                                    </span>
                                                </a>
                                            </li>-->
                                        </ul>
                                    </div>
                                </li>
                            @endif
                            @role('administrator|company|subuser')
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('pricelevels.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-sellsy"></i>
                                    <span class="m-menu__link-text">
                                        Price levels
                                    </span>
                                </a>
                            </li>
                            <!-- <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"
                                data-menu-submenu-toggle="click" data-redirect="true" aria-haspopup="true">
                                <a href="#" class="m-menu__link m-menu__toggle">
                                    <i class="m-menu__link-icon la la-envelope"></i>
                                    &nbsp;&nbsp;&nbsp;
                                    <span class="m-menu__link-text">
                                        Email Settings
                                    </span>
                                    <i class="m-menu__hor-arrow la la-angle-down"></i>
                                    <i class="m-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">

                                    <ul class="m-menu__subnav">
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="{{route('UserConfiguration.index')}}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-comments"></i>
                                                <span class="m-menu__link-text">
                                                    Notifications
                                                </span>
                                            </a>
                                        </li>
                                        <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                            <a href="{{ route('templates.index') }}" class="m-menu__link ">
                                                <i class="m-menu__link-icon la la-file"></i>
                                                <span class="m-menu__link-text">
                                                    Templates
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('dashboard.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-graph"></i>
                                    <span class="m-menu__link-text">
                                        Dashboard
                                    </span>
                                </a>
                            </li> -->
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('search.list')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-search"></i>
                                    <span class="m-menu__link-text">
                                        Search History
                                    </span>
                                </a>
                            </li>
                            @endrole

                            @role('administrator')

                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('ContainerCalculation.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon la la-clipboard"></i>
                                    <span class="m-menu__link-text">
                                        Containers Calculation T.
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('transit_time.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-calendar"></i>
                                    <span class="m-menu__link-text">
                                        Transit Times
                                    </span>
                                </a>
                            </li>
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{ route('apicredentials.index') }}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-lock"></i>
                                    <span class="m-menu__link-text">
                                        Carrier APIs Credentials
                                    </span>
                                </a>
                            </li>
                            @endrole


                            @role('administrator|data_entry')
                            <!--                        Fin Links Importacion-->
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('settings.companies')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-user-settings"></i>
                                    <span class="m-menu__link-text">
                                        User companies
                                    </span>
                                </a>
                            </li>
                
                            @endrole

                            @role('administrator|company')
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('segments_configuration.index')}}" class="m-menu__link ">
                                    <i class="m-menu__link-icon flaticon-user-settings"></i>
                                    <span class="m-menu__link-text">
                                        Segments Configuration
                                    </span>
                                </a>
                            </li>
                            @endrole
                            @if(Session::has('impersonate'))
                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="{{route('impersonate.revert')}}" class="m-menu__link ">
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

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel" data-menu-submenu-toggle="click"
                    data-redirect="true" aria-haspopup="true">
                    <a href="#" class="m-menu__link m-menu__toggle">
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

                            <li class="m-menu__item " data-redirect="true" aria-haspopup="true">
                                <a href="" class="m-menu__link "
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="m-menu__link-text">
                                        <span class="la la-sign-out" style="font-size:23px;"></span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<b>Logout </b>
                                    </span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>

                <div class="row">
                    <div class="col-md-6">
                        <li id="notifications"
                            class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center m-dropdown--mobile-full-width"
                            data-dropdown-toggle="click" data-dropdown-persistent="true" style="margin-top:20px;">
                            <a href="#" class="m-nav__link m-dropdown__toggle newNotification" hidden="true"
                                id="m_topbar_notification_icon">
                                <div class='row'>
                                    <div class="col-md-2">
                                        <span
                                            class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
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
                                    <div class="m-dropdown__header m--align-center"
                                        style="background: url(../../assets/app/media/img/misc/notification_bg.jpg); background-size: cover;">
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
                                                <a href="{{route('UserConfiguration.index')}}">
                                                    <i class="la la-cog pull-right setting"
                                                        style="font-weight: bold; color:white; font-size: 23px;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand"
                                                role="tablist">
                                                <li class="nav-item m-tabs__item">
                                                    <a class="nav-link m-tabs__link active" data-toggle="tab"
                                                        href="#topbar_notifications_notifications" role="tab">
                                                        New Notifications
                                                    </a>
                                                </li>
                                                <li class="nav-item m-tabs__item">
                                                    <a class="nav-link m-tabs__link " data-toggle="tab"
                                                        href="#topbar_notifications_notifications_old" role="tab">
                                                        Old Notifications
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="topbar_notifications_notifications"
                                                    role="tabpanel">
                                                    <div class="m-scrollable" data-scrollable="true"
                                                        data-max-height="250" data-mobile-max-height="200">
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
                                                <div class="tab-pane " id="topbar_notifications_notifications_old"
                                                    role="tabpanel">
                                                    <div class="m-scrollable" data-scrollable="true"
                                                        data-max-height="250" data-mobile-max-height="200">
                                                        <div class="m-list-timeline m-list-timeline--skin-light">
                                                            <div class="m-list-timeline__items notifications_old">
                                                                <div
                                                                    class="m-list-timeline__item notificationsMenu_old">
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
                    </div>
                </div>

                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel">
                    <div class="col-md-6">
                        <a target="_blank" href="https://support.cargofive.com/frequent-questions/"> 
                            <span class="m-menu__link-text">
                                <u><b>FAQ</b></u>
                            </span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>