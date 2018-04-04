@extends('layouts.app')
@section('title', 'Bienvenido')
@section('content')
	<div class="m-content">
		<div class="col-xl-12">
			<!--begin:: Widgets/Quick Stats-->
			<div class="row m-row--full-height">
				<div class="col-sm-12 col-md-12 col-lg-6">
					<div class="m-portlet m-portlet--half-height m-portlet--border-bottom-brand ">
						<div class="m-portlet__body">
							<div class="m-widget26">
								<div class="m-widget26__number">
									600
									<small>
										Cotizaciones
									</small>
								</div>
							</div>
						</div>
					</div>
					<div class="m--space-30"></div>
					<div class="m-portlet m-portlet--half-height m-portlet--border-bottom-danger ">
						<div class="m-portlet__body">
							<div class="m-widget26">
								<div class="m-widget26__number">
									60
									<small>
										Ventas
									</small>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-6">
					<div class="m-portlet m-portlet--half-height m-portlet--border-bottom-success ">
						<div class="m-portlet__body">
							<div class="m-widget26">
								<div class="m-widget26__number">
									230
									<small>
										All Transactions
									</small>
								</div>
								<div class="m-widget26__chart" style="height:90px; width: 220px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
									<canvas id="m_chart_quick_stats_3" style="display: block; height: 110px; width: 220px;" width="440" height="220" class="chartjs-render-monitor"></canvas>
								</div>
							</div>
						</div>
					</div>
					<div class="m--space-30"></div>
					<div class="m-portlet m-portlet--half-height m-portlet--border-bottom-accent ">
						<div class="m-portlet__body">
							<div class="m-widget26">
								<div class="m-widget26__number">
									470
									<small>
										All Comissions
									</small>
								</div>
								<div class="m-widget26__chart" style="height:90px; width: 220px;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
									<canvas id="m_chart_quick_stats_4" style="display: block; height: 110px; width: 220px;" width="440" height="220" class="chartjs-render-monitor"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--end:: Widgets/Quick Stats-->
		</div>
	</div>
@endsection

