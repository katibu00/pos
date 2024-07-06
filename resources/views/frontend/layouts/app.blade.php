<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>Hendrio - Plumbing Service HTML5 Template</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Favicon -->
	<link rel="icon" type="image/png" sizes="56x56"href="/ecommerce/assets/images/fav-icon/icon.png">
	<!-- bootstrap CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/bootstrap.min.css" type="text/css" media="all">
	<!-- carousel CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/owl.carousel.min.css" type="text/css" media="all">
	<!-- animate CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/animate.css" type="text/css" media="all">
	<!-- animated-text CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/animated-text.css" type="text/css" media="all">
	<!-- font-awesome CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/all.min.css" type="text/css" media="all">
	<!-- font-flaticon CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/flaticon.css" type="text/css" media="all">
	<!-- theme-default CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/theme-default.css" type="text/css" media="all">
	<!-- meanmenu CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/meanmenu.min.css" type="text/css" media="all">
	<!-- transitions CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/owl.transitions.css" type="text/css" media="all">
	<!-- venobox CSS -->
	<link rel="stylesheet" href="venobox/venobox.css" type="text/css" media="all">
	<!-- bootstrap icons -->
	<link rel="stylesheet"href="/ecommerce/assets/css/bootstrap-icons.css" type="text/css" media="all">
	<!-- Main Style CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/style.css" type="text/css" media="all">  
	<!-- responsive CSS -->
	<link rel="stylesheet"href="/ecommerce/assets/css/responsive.css" type="text/css" media="all">
	<!-- modernizr js -->
	<script src="/ecommerce/assets/js/vendor/modernizr-3.5.0.min.js"></script>
</head>

<body>



	<!-- loder -->
	<div class="loader-wrapper">
		<div class="loader"></div>
		<div class="loder-section left-section"></div>
		<div class="loder-section right-section"></div>
	</div>



    @include('frontend.layouts.header')


	@yield('content')



    @include('frontend.layouts.footer')


	<!--==================================================-->
	<!-- Start Search Popup Section -->
	<!--==================================================-->
	<div class="search-popup">
		<button class="close-search style-two"><span class="flaticon-multiply"><i class="far fa-times-circle"></i></span></button>
		<button class="close-search"><i class="bi bi-arrow-up"></i></button>
		<form method="post" action="#">
			<div class="form-group">
				<input type="search" name="search-field" value="" placeholder="Search Here" required="">
				<button type="submit"><i class="fa fa-search"></i></button>
			</div>
		</form>
	</div>
	<!--==================================================-->
	<!-- Start Search Popup Section -->
	<!--==================================================-->




	<!--==================================================-->
	<!-- Start scrollup section Section -->
	<!--==================================================-->
	<!-- scrollup section -->
	<div class="scroll-area">
		<div class="top-wrap">
			<div class="go-top-btn-wraper">
				<div class="go-top go-top-button">
					<i class="bi bi-chevron-double-up"></i>
					<i class="bi bi-chevron-double-up"></i>
				</div>
			</div>
		</div>
	</div>
	<!--==================================================-->
	<!-- Start scrollup section Section -->
	<!--==================================================-->





	<script src="/ecommerce/assets/js/vendor/jquery-3.6.2.min.js"></script>

	<script src="/ecommerce/assets/js/popper.min.js"></script>

	<script src="/ecommerce/assets/js/bootstrap.min.js"></script>

	<script src="/ecommerce/assets/js/owl.carousel.min.js"></script>

	<script src="/ecommerce/assets/js/jquery.counterup.min.js"></script>

	<script src="/ecommerce/assets/js/waypoints.min.js"></script>

	<script src="/ecommerce/assets/js/wow.js"></script>

	<script src="/ecommerce/assets/js/imagesloaded.pkgd.min.js"></script>

	<script src="/ecommerce/venobox/venobox.js"></script>

	<script src="/ecommerce/assets/js/animated-text.js"></script>

	<script src="/ecommerce/venobox/venobox.min.js"></script>

	<script src="/ecommerce/assets/js/isotope.pkgd.min.js"></script>

	<script src="/ecommerce/assets/js/jquery.meanmenu.js"></script>

	<script src="/ecommerce/assets/js/jquery.scrollUp.js"></script>

	<script src="/ecommerce/assets/js/jquery.barfiller.js"></script>

	<script src="/ecommerce/assets/js/theme.js"></script>

</body>

</html>