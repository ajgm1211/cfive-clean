<!DOCTYPE html>
<html>
	<head>
		<title>Error 500</title>
		<link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="{{asset('bootstrap-4.4.1/css/bootstrap.min.css')}} ">
		<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
		<style>
			html, body {
				height: 100%;
			}
			body {
				background-image: url("/errors/test1.jpg");
				background-repeat: no-repeat;
				background-size: 50% 100%;
				background-position: right;
				margin: 0;
				padding: 0;
				width: 100%;
				/*color: #B0BEC5;*/
				display: table;
				font-weight: 900;
				font-family: 'Lato', sans-serif;
			}
			.container {
				text-align: center;
				display: table-cell;
				/*				vertical-align: top;*/
			}
			.title {
				font-size: 22px;
				width:50%;
				font-weight: bold !important;
				margin-bottom: 40px;
			}
			.img{
				margin-left:20px;
			}
			.size-60{
				font-size: 60px;
				font-weight: bold !important;
			}
			.ichead{
				font-size: 30px;
			}
			.btn-primary2:hover .ichead {
				color: #fff !important;
			}
			.btn-circle.btn-xl {
				width: 60px;
				height: 60px;
				padding: 13px 12px;
				border-radius: 35px;
				font-size: 30px;
				line-height: 1.33;

			}
			.btn-primary2 {
				color: #fff;
				background-color: #031B4E;
				border-color: #031B4E;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row align-items-center">
				<div class="col-6">
					<br><br><br><br><br>
					<img class="img" src="{{asset('images/logo-icon.png')}}"/>
				</div>
				<div class="col-md-12 ">
					<div class="title" style="">
						<h1 class="size-60">Ops!!!</h1>
						<p>
							<b>
								An error has occurred while we were processing your request, please try again.
							</b>
						</p>
						<p>
							<b>
								If it persists, contact the site administrator.
							</b>
						</p>
						<a  href="{{ url('/tickets') }}" class="btn btn-primary2 btn-circle btn-xl " title="Support">
							<span class="m-nav__link-icon">
								<i class="la la-headset ichead" ></i>
							</span>
						</a>
					</div>
				</div>
			</div>
		</div>
		<script src="{{asset('jquery-3.4.1.min.js')}}"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="{{asset('/bootstrap-4.4.1/js/bootstrap.min.js')}}" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	</body>
</html>