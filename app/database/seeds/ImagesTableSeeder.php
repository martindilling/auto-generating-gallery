<?php

class ImagesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('images')->truncate();

		$images = array(
			array(
				"id"          => 1,
				"album_id"    => 1,
				"image"       => "imagefile1.jpg",
				"title"       => "First Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 2,
				"album_id"    => 1,
				"image"       => "imagefile2.jpg",
				"title"       => "Second Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 3,
				"album_id"    => 1,
				"image"       => "imagefile3.jpg",
				"title"       => "Third Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 4,
				"album_id"    => 1,
				"image"       => "imagefile4.jpg",
				"title"       => "Fourth Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),

			array(
				"id"          => 5,
				"album_id"    => 2,
				"image"       => "imagefile5.jpg",
				"title"       => "Fifth Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 6,
				"album_id"    => 2,
				"image"       => "imagefile6.jpg",
				"title"       => "Sixth Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 7,
				"album_id"    => 2,
				"image"       => "imagefile7.jpg",
				"title"       => "Seventh Image",
				"text"        => "Some text descriping this image",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
		);

		// Uncomment the below to run the seeder
		DB::table('images')->insert($images);

		$this->command->info('- 7 images!');
	}

}
