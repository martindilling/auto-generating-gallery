@extends('layouts.scaffold')

@section('meta')
	<meta property="og:type"            content="article" />
	<meta property="og:site_name"       content="{{ Config::get('filegallery.page.title') }}"/>
	<meta property="og:url"             content="{{ url($album->folder) }}" />
	<meta property="og:title"           content="{{ $album->title }}" />
	<meta name="description"            content="An album from the site: {{ Config::get('filegallery.page.title') }}">
	<meta property="og:description"     content="An album from the site: {{ Config::get('filegallery.page.title') }}">

	<meta name="twitter:card"           content="gallery">
	<meta name="twitter:url"            content="{{ url($album->folder) }}">
	<meta name="twitter:title"          content="{{ $album->title }}">
	<meta name="twitter:description"    content="An album from the site: {{ Config::get('filegallery.pagetitle') }}">
@if ($album->images->count() > 4)
@for ($i=0; $i < 4; $i++)
	<meta name="twitter:image{{ $i }}"         content="{{ url($album->folder.'/'.$album->images[$i]->image) }}">
@endfor
@endif

@stop


@section('main')

<ol class="breadcrumb">
	<li><a href="{{ url('/') }}">Albums</a></li>
	<li class="active">{{ $album->title }}</li>
</ol>

@if ($album->images->count())
	<div class="page-header">
		<h1 class="text-center">{{ $album->title }}</h1>
	</div>
	<div class="row">
	<?php $counter = 1 ?>
	@foreach ($album->images as $image)
		<div class="grid image col-xs-4 col-sm-3 col-md-2 col-lg-2">
			<a href="{{ url($image->album->folder.'/'.$image->image) }}" caption=" {{ $image->text }}" imgpage="{{ url($image->album->folder.'/'.$image->image) }}">
				<span class="dim"></span>
				<img src="{{ asset($image->thumburl) }}" class="img-responsive" alt="{{ $image->title }}">
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
	There are no images in this album
@endif

@stop