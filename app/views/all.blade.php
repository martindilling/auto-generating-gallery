@extends('layouts.scaffold')

@section('main')

@if ($albums->count())
	@foreach ($albums as $album)
		<div class="row">

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
		</div>
	@endforeach
@else
	There are no albums
@endif

@stop