<?php

class GalleryController extends BaseController {

	public function import()
	{
		if ( File::exists(Config::get('filegallery.uploadsfolder').'/'.Config::get('filegallery.readyfile')) )
		{
			$files = new ImageManager(Config::get('filegallery.uploadsfolder'), Config::get('filegallery.imagesfolder'));

			if ( ! $files->import() ) {
				$errors = $files->messages()->get('error');
				return Redirect::to('/')->with('error', $errors[0]);
			}
			return Redirect::to('/')->with('success', 'Done importing images.');
		}
		return Redirect::to('/')->with('error', 'Not ready for import.');
	}

	public function showAlbums()
	{
		$albums = Album::with('images')->get();

		return View::make('albums', compact('albums'));
	}

	public function showAlbum($album_folder)
	{
		$album = Album::with('images')->where('folder', $album_folder)->first();

		if ( ! $album ) {
			App::abort(404, 'Album wasn\'t found.');
		}

		return View::make('album', compact('album'));
	}

	public function showImage($album_folder, $image_file)
	{
		$album = Album::with('images')->where('folder', $album_folder)->first();
		// $image = Image::with('album')->find($image_id);

		if ( ! $album ) {
			App::abort(404, 'Album wasn\'t found.');
		}

		$count = $album->images->count();

		$success = false;

		for ($i=0; $i < $count; $i++) {
			if ($album->images[$i]->image == $image_file) {
				$image = $album->images[$i];

				// Previous image
				if ($i == 0) {
					$image->prev = $album->images[$count-1];
				} else {
					$image->prev = $album->images[$i-1];
				}

				// Next image
				if ($i == $count-1) {
					$image->next = $album->images[0];
				} else {
					$image->next = $album->images[$i+1];
				}

				$success = true;
			}
		}

		if ( ! $success ) {
			App::abort(404, 'Image wasn\'t found.');
		}

		return View::make('image', compact('image'));
	}

	public function showAllImages()
	{
		$albums = Album::with('images')->get();

		return View::make('all', compact('albums'));
	}

}
