<header class="m-grid__item m-header" data-minimize-offset="200" data-minimize-mobile-offset="200" >
	<div class="m-container m-container--fluid m-container--full-height">
		@if(Session::has('impersonate') || env('APP_VIEW') == 'local' || env('APP_VIEW') == 'prod' || env('APP_VIEW') == 'dev')
		@include('includes.header_menu')
		@elseif(env('APP_VIEW') == 'operaciones')
		@include('includes.header_menu_operaciones')
		@endif
		<!-- END: Horizontal Menu -->

	</div>

</header>