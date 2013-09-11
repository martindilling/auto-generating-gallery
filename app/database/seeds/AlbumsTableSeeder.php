<?php

class AlbumsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('albums')->truncate();

		$albums = array(
			array(
				"id"          => 1,
				"image_id"    => 1,
				"folder"      => "first-album",
				"title"       => "First Album",
				"text"        => "Some text descriping what's in this album",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 2,
				"image_id"    => 5,
				"folder"      => "second-album",
				"title"       => "Second Album",
				"text"        => null,
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
			array(
				"id"          => 3,
				"image_id"    => null,
				"folder"      => "third-album",
				"title"       => "Third Album",
				"text"        => "This is a useless album",
				"created_at"  => new DateTime('now'),
				"updated_at"  => new DateTime('now')
			),
		);

		// Uncomment the below to run the seeder
		DB::table('albums')->insert($albums);

		$this->command->info('- 3 albums!');
	}

}
