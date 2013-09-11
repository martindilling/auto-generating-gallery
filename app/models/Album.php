<?php

class Album extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'albums';

	protected $guarded = array();

	public static $rules = array(
		// 'name'          => 'required',
		// 'logo_url'      => 'required',
		// 'description'   => 'required',
		// 'game_start'    => 'required|date',
		// 'contact_email' => 'email',
	);

	public function images()
	{
		return $this->hasMany('Image');
	}

	public function getImageIdAttribute($value)
	{
		if ($value)
		{
			$image = Image::find($value);
		}
		else
		{
			$image = Image::where('album_id', $this->id)->first();
		}

		return ($image) ? $image->thumburl : '';
	}

}
