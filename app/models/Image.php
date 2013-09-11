<?php

class Image extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'images';

	protected $guarded = array();

	public static $rules = array(
		// 'name'          => 'required',
		// 'logo_url'      => 'required',
		// 'description'   => 'required',
		// 'game_start'    => 'required|date',
		// 'contact_email' => 'email',
	);

	public function album()
	{
		return $this->belongsTo('Album');
	}

	public function getImageurlAttribute($value)
	{
		$album = Album::find($this->album_id);
		return Config::get('filegallery.imagesfolder').'/'.$album->folder.'/'.$this->image;
	}

	public function getThumburlAttribute()
	{
		$album = Album::find($this->album_id);
		return Config::get('filegallery.imagesfolder').'/'.$album->folder.'/thumbs/'.$this->image;
	}

}
