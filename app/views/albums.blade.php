@extends('layouts.scaffold')

@section('meta')
	<meta property="og:type"            content="article" />
	<meta property="og:site_name"       content="{{ Config::get('filegallery.page.title') }}"/>
	<meta property="og:url"             content="{{ url('/') }}" />
	<meta property="og:title"           content="{{ Config::get('filegallery.page.title') }}" />
	<meta name="description"            content="Welcome to the online gallery {{ Config::get('filegallery.pagetitle') }}">
	<meta property="og:description"     content="Welcome to the online gallery {{ Config::get('filegallery.pagetitle') }}">

	<meta name="twitter:card"           content="summary">
	<meta name="twitter:url"            content="{{ url('/') }}">
	<meta name="twitter:title"          content="{{ Config::get('filegallery.page.title') }}">
	<meta name="twitter:description"    content="Welcome to the online gallery {{ Config::get('filegallery.pagetitle') }}">
@stop


@section('main')

<ol class="breadcrumb">
	<li class="active">Albums</li>
</ol>

<div class="page-header">
	<h1 class="text-center">Albums</h1>
</div>
@if ($albums->count())
	<div class="row">
		<?php $counter = 1 ?>
		@foreach ($albums as $album)
			<div class="grid album col-xs-4 col-sm-3 col-md-2 col-lg-2">
				<a href="{{ url($album->folder) }}">
					<span class="dim"></span>
					<img src="{{ asset($album->image_id) }}" class="img-responsive" alt="{{ $album->title }}">
					<span class="title label label-default">{{ $album->title }}</span>
				</a>
			</div>
			<?php
				$clearfixes = '';
				if ($counter % 3 == 0) { $clearfixes .= ' visible-xs'; }
				if ($counter % 4 == 0) { $clearfixes .= ' visible-sm'; }
				if ($counter % 6 == 0) { $clearfixes .= ' visible-md visible-lg'; }

				$counter++;
			?>
			@if ( $clearfixes != '' )
				<div class="clearfix {{ $clearfixes }}"></div>
			@endif
		@endforeach
	</div>
@else
	There are no albums
@endif

@stop