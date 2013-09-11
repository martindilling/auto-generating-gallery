<!DOCTYPE html>
<html lang="en" xmlns:fb="http://ogp.me/ns/fb#">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{{ Config::get('filegallery.page.title') }}</title>
	<meta name="application-name" content="{{ Config::get('filegallery.page.title') }}" />
	<meta name="author" content="{{ Config::get('filegallery.page.author') }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta property="fb:app_id"          content="{{ Config::get('filegallery.fb_app_id') }}" />
@yield('meta')

	<!-- Styles -->
	<link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ url('assets/css/style.css') }}" rel="stylesheet">
@yield('styles')
</head>
<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId={{ Config::get('filegallery.fb_app_id') }}";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<!--[if lt IE 9]>
		<div class="updateclass"></div>
		<div class="updatemessage">
			Please update your browser! We would recommend installing either <a href="http://google.com/chrome/" target="_blank">Chrome</a> or <a href="http://firefox.com" target="_blank">Firefox</a>.<br />
			If it absolutely have to be Internet Explorer, then update it to the latest version <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" target="_blank">here</a>.<br /><br />
			We can't live in the past forever ;)
		</div>
	<![endif]-->

	<div class="container">
	@if (Session::has('message'))
		<div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Heads up!</strong> {{ Session::get('message') }}
		</div>
	@endif
	@if (Session::has('error'))
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Error!</strong> {{ Session::get('error') }}
		</div>
	@endif
	@if (Session::has('success'))
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>Success!</strong> {{ Session::get('success') }}
		</div>
	@endif
@yield('main')
		<div class="page-footer text-center">
			<small class="text-muted"><a href="https://github.com/martindilling/auto-generating-gallery" target="_blank">Auto Generating Gallery</a> by Martin Dilling-Hansen</small>
		</div>
	</div>


	<!-- Styles -->
	<script>var fb_app_id = '{{ Config::get('filegallery.fb_app_id') }}';</script>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="{{ url('assets/js/jquery-1.10.2.min.js') }}"><\/script>')</script>

	<script src="{{ url('assets/js/main.js') }}"></script>
@yield('scripts')

	<!-- Google Analytics -->
	<script>
		(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
		function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
		e=o.createElement(i);r=o.getElementsByTagName(i)[0];
		e.src='//www.google-analytics.com/analytics.js';
		r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
		ga('create','{{ Config::get('filegallery.google_analytics') }}');ga('send','pageview');
	</script>
</body>
</html>