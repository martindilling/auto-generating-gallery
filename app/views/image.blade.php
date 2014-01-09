@extends('layouts.scaffold')

@section('meta')
	<meta property="og:type"            content="article" />
	<meta property="og:site_name"       content="{{ Config::get('filegallery.page.title') }}"/>
	<meta property="og:url"             content="{{ url($image->album->folder.'/'.$image->image) }}" />
	<meta property="og:title"           content="{{ $image->title }}" />
	<meta name="description"            content="{{ ($image->text) ? html_entity_decode(strip_tags($image->text),ENT_QUOTES,'UTF-8') : 'Image from the album '.$image->album->title }}">
	<meta property="og:description"     content="{{ ($image->text) ? html_entity_decode(strip_tags($image->text),ENT_QUOTES,'UTF-8') : 'Image from the album '.$image->album->title }}">
	<meta property="og:image"           content="{{ asset($image->imageurl) }}" />

	<meta name="twitter:card"           content="photo">
	<meta name="twitter:url"            content="{{ url($image->album->folder.'/'.$image->image) }}">
	<meta name="twitter:title"          content="{{ $image->title }}">
	<meta name="twitter:description"    content="{{ ($image->text) ? html_entity_decode(strip_tags($image->text),ENT_QUOTES,'UTF-8') : 'Image from the album '.$image->album->title }}">
	<meta name="twitter:image"          content="{{ asset($image->imageurl) }}">
@stop


@section('main')

		<ol class="breadcrumb">
			<li><a href="{{ url('/') }}">Albums</a></li>
			<li><a href="{{ url($image->album->folder) }}">{{ $image->album->title }}</a></li>
			<li class="active">{{ $image->title }}</li>
		</ol>

@if ($image)
	<div class="row">
		<div id="imagenav" class="navigation text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<a id="prev-img" href="{{ url($image->album->folder.'/'.$image->prev->image) }}" class="btn">
				<span class="glyphicon glyphicon-chevron-left"></span>
			</a>
			<a id="next-img" href="{{ url($image->album->folder.'/'.$image->next->image) }}" class="btn">
				<span class="glyphicon glyphicon-chevron-right"></span>
			</a>
		</div>
	</div>

	<div class="row">
		<div class="fullimagecontainer col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div id="loader" class="loading fullimage" rel="fitimage" imgfile="{{ asset($image->imageurl) }}">
				{{-- Here the image will be loaded --}}
			</div>
		</div>
	</div>

	@if ($image->text)
	<div class="row">
		<div class="text-center col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="img-description">{{ $image->text }}</div>
		</div>
	</div>
	@endif

	<div class="social">
		<?php
			$share = array(
				'url'   => url($image->album->folder.'/'.$image->image),
				'image' => asset($image->imageurl),
				'text'  => $image->text,
			);

			$facebook_like =
				'http://www.facebook.com/plugins/like.php' .
				'?href=' . $share['url'] .
				'&width=450&height=21&colorscheme=light&layout=button_count' .
				'&action=like&show_faces=false&send=false' .
				'&appId=' . Config::get('filegallery.fb_app_id');

			$pinterest =
				'http://pinterest.com/pin/create/button/' .
				'?url=' . $share['url'] .
				'&media=' . $share['image'];

			if ($share['text']) {
				$pinterest .= '&description=' . $share['text'];
			}

			$twitter = array(
				'url'     => url($image->album->folder.'/'.$image->image),
				'text'    => $image->text,
				'via'     => null,
				'hashtag' => null,
			);

		?>

		<div class="social_links fb_likes">
			<fb:like href="{{ $share['url'] }}" width="140" layout="button_count" show_faces="false" send="true"></fb:like>
		</div>
		<div class="social_links twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="{{ $twitter['url'] }}" {{ ($twitter['text']) ? 'data-text="'.$twitter['text'].'"' : '' }} {{ ($twitter['via']) ? 'data-via="'.$twitter['via'].'"' : '' }} {{ ($twitter['hashtag']) ? 'data-hashtags="'.$twitter['hashtag'].'"' : '' }}>Tweet</a>
			<script>
				!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
			</script>
		</div>
		<div class="social_links pinterest">
			<a href="{{ $pinterest }}" data-pin-do="buttonPin" data-pin-config="beside" target="_blank"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
		</div>
	</div>
	<div class="comments">
		<fb:comments href="{{ url($image->album->folder.'/'.$image->image) }}"></fb:comments>
	</div>
@else
	No image
@endif

@stop
