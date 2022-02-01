<header class="m-grid__item m-header" data-minimize-offset="200" data-minimize-mobile-offset="200" >
	<div class="m-container m-container--fluid m-container--full-height">
		@if(Session::has('impersonate') || config('custom.app_env') == 'local' || config('custom.app_env') == 'prod' || config('custom.app_env') == 'dev')
			@include('includes.header_menu')
			@elseif(config('custom.app_view') == 'operaciones')
			@include('includes.header_menu_operaciones')
		@endif
		<!-- END: Horizontal Menu -->

	</div>

</header>