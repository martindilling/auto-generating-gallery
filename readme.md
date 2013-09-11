Auto Generating Gallery
=======================

[See the demo](http://gallerydemo.martindilling.com)

**Pretty, simple and easy gallery.**
1. Upload albumfolder via ftp
2. Visit http://domain.com/import
3. Done ;)

## Installation

Like with Laravel 4 you need to install all the nessesary packages with composer:

    composer install

Configure the database settings in *app/config/database.php*
And migrate the database with:

    artisan migrate

Change configuration of the gallery in *app/config/filegallery.php*

That should be it ;)

## Creating an album

To create an album you upload a folder (the album) with images to the uploads
folder configured in *app/config/filegallery.php* (default: 'uploads')

Names of the folders created in the 'public/uploads' folder will be the album names.
To create a description to an image create a *.txt file with the same name as the
imagefile, and write the description in that file.
To create the album, create a 'ready.txt' file in the 'uploads' folder, and
visit the page http://domain.com/import, and it will create the nessesary files
and create the albums in the database :)

