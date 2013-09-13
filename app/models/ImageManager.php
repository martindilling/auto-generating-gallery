<?php

use Patchwork\Utf8 as UTF8;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\MessageBag;

class FileNotFoundException extends \Exception {}
class FolderNotFoundException extends \Exception {}

class ImageManager {

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $extensions = array('jpg','jpeg','png','gif');

	/**
	 * The uploads folder to find images.
	 *
	 * @var string
	 */
	protected $uploadsfolder;

	/**
	 * The images folder to find images.
	 *
	 * @var string
	 */
	protected $imagesfolder;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $files;

	/**
	 * The message bag instance.
	 *
	 * @var \Illuminate\Support\MessageBag
	 */
	protected $messages;

	/**
	 * Initialize the AlertMessageBag class.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @param  \Illuminate\Config\Repository $config
	 * @param  array  $messages
	 * @return \Martindilling\Messages\Messages
	 */
	public function __construct($uploadsfolder = 'uploads', $imagesfolder = 'images')
	{
		$this->uploadsfolder = $uploadsfolder;
		$this->imagesfolder  = $imagesfolder;
		$this->messages      = new MessageBag;

		if ( ! File::isDirectory($this->uploadsfolder) ) {
			throw new FolderNotFoundException("Folder '{$this->uploadsfolder}' does not exist");
		}
		if ( ! File::isDirectory($this->imagesfolder) ) {
			File::makeDirectory($this->imagesfolder, 0777, true);
		}

		$this->readFiles($this->uploadsfolder);
	}

	/**
	 * Convert a string to a URL safe string
	 *
	 * @param  string $str
	 * @param  array  $replace
	 * @param  string $separator
	 * @return string
	 */
	protected function urlSafe($str, $replace=array(), $separator = '-')
	{
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		// Transliterate non-ASCII characters
		// $str = UTF8::transliterate_to_ascii($str);

		// Remove all characters that are not the separator, a-z, 0-9, or whitespace
		$str = preg_replace('![^'.preg_quote($separator).'a-z0-9\s]+!', '', UTF8::strtolower($str));

		// Replace all separator characters and whitespace by a single separator
		$str = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $str);

		// Trim separators from the beginning and end
		return trim($str, $separator);
	}


	/**
	 * Add a message to the key in the MessageBag
	 *
	 * @param string $key
	 * @param string $message
	 */
	protected function addMessage($key, $message)
	{
		$this->messages->add($key, $message);
	}

	/**
	 * Add a error to the MessageBag
	 *
	 * @param string $errormessage
	 */
	protected function addError($errormessage)
	{
		$this->addMessage('error', $errormessage);
	}

	/**
	 * Strips out a dir from a path at the given position
	 *
	 * @param  string $path
	 * @param  int    $pos
	 * @return string/false
	 */
	protected function stripDirFromPath($path, $pos)
	{
		$patharray = explode(DIRECTORY_SEPARATOR, $path);

		if (count($patharray) > $pos)
		{
			unset($patharray[$pos]);
			return implode(DIRECTORY_SEPARATOR, $patharray);
		}

		return false;
	}

	/**
	 * Creates a pathstring from an array of segments
	 *
	 * @param  array  $segments
	 * @param  string $seperator
	 * @return string/false
	 */
	protected function createPath($segments, $seperator = DIRECTORY_SEPARATOR)
	{
		if ( ! is_array($segments) ) return false;

		return implode($seperator, $segments);
	}

	/**
	 * Read files and add them to $this->files
	 *
	 * @param  string $directory
	 * @return boolean
	 */
	protected function readFiles($directory)
	{
		if ( ! File::isDirectory($directory)) return false;

		$sort = function (\SplFileInfo $a, \SplFileInfo $b)
		{
			setlocale(LC_COLLATE, "dk_DK");
			return strcmp($a->getFilename(), $b->getFilename());
		};

		$finder = new Finder();
		$items = $finder
			->files()
			->sort($sort)
			->in($directory);

		foreach ($items as $item)
		{
			if ($item->isDir())
			{
				$path = $item->getPathname();

				$this->readFiles($path, $options);
			}
			else
			{
				if ( in_array(File::extension($item->getPathname()), $this->extensions) )
				{
					$this->addToArray($item->getPathname());
				}
			}
		}
		// die(var_dump($this->files));
		return true;
	}

	/**
	 * Add file details to $this->files
	 *
	 * @param string $filepath
	 */
	protected function addToArray($filepath)
	{
		$info = pathinfo($filepath);

		$albumName = $this->stripDirFromPath($info['dirname'], 0);

		$desciptionFilepath = $this->createPath(array(
			$info['dirname'],
			$info['filename'] . '.txt'
		));

		$albumCoverFilepath = $this->createPath(array(
			$info['dirname'],
			$info['filename'] . '_cover.txt'
		));

		if ( File::exists($desciptionFilepath) )
		{
			$text = File::get($desciptionFilepath);
		}

		if ( File::exists($albumCoverFilepath) )
		{
			$cover = 1;
		}

		$this->files[$albumName][] = array(
			'filename'      => $info['filename'],
			'newfilename'   => $this->urlSafe($info['filename']),
			'ext'           => File::extension($filepath),
			'text'          => (isset($text)) ? $text : null,
			'cover'         => (isset($cover)) ? true : false,
		);
	}

	/**
	 * Add information from $this->files to the database
	 *
	 * @return boolean
	 */
	protected function toDB()
	{
		if ( ! $this->files ) {
			$this->addError('No files where found!');
			return false;
		}

		foreach ($this->files as $album => $images)
		{
			$album = ($album) ? $album : 'Undefined';

			$a = Album::where('title', $album)->first();
			$a = ($a) ? $a : new Album;

			$a->folder = $this->urlSafe($album);
			$a->title  = $album;
			$a->save();

			if ( ! $a->id ) {
				$this->addError('Error saving album to database!');
				return false;
			}

			foreach ($images as $image)
			{
				$i = Image::where('image', $image['newfilename'].'.'.$image['ext'])->first();
				$i = ($i) ? $i : new Image;

				$i->image = $image['newfilename'] . '.' . $image['ext'];
				$i->title = $image['filename'];
				$i->text  = $image['text'];
				$i->album()->associate($a);
				$i->save();

				if ( ! $i->id ) {
					$this->addError('Error saving image to database!');
					return false;
				}

				if ($image['cover']) {
					$a->image_id = $i->id;
					$a->save();
				}
			}
		}
		return true;
	}

	/**
	 * Create a thumbnail from a file
	 *
	 * @param  string $file
	 * @param  string $thumb
	 * @param  int    $size
	 */
	protected function createThumb($file, $thumb, $size)
	{
		// open file a image resource
		$img = ImageLib::make($file);

		// crop the best fitting 1:1 ratio and resize to the size given
		$img->grab($size);

		// save the same file as jpeg with default quality
		$img->save($thumb);
	}

	/**
	 * Copy files to albums folder from information in $this->filesize()
	 *
	 * @return boolean
	 */
	protected function copyFiles()
	{
		if ( ! $this->files ) {
			$this->addError('No files where found!');
			return false;
		}

		foreach ($this->files as $album => $images)
		{
			$album = $album;
			$targetalbum = ($album) ? $this->urlSafe($album) : 'undefined';

			$path   = $this->createPath(array(
				public_path(),
				$this->uploadsfolder,
				$album
			));

			$target = $this->createPath(array(
				public_path(),
				$this->imagesfolder,
				$targetalbum
			));

			$thumbs = $this->createPath(array(
				$target,
				'thumbs'
			));

			if ( ! File::isDirectory($thumbs) ) {
				if ( ! File::makeDirectory($thumbs, 0777, true) ) {
					$this->addError('Error creating folder!');
					return false;
				}
			}

			foreach ($images as $image)
			{
				$oldfile  = $this->createPath(array(
					$path,
					$image['filename'].'.'.$image['ext']
				));

				$newfile  = $this->createPath(array(
					$target,
					$image['newfilename'].'.'.$image['ext']
				));

				$newthumb = $this->createPath(array(
					$thumbs,
					$image['newfilename'].'.'.$image['ext']
				));


				if ( ! File::copy($oldfile, $newfile) ) {
					$this->addError('Error copying files!');
					return false;
				}

				$this->createThumb($newfile, $newthumb, Config::get('filegallery.thumbsize'));
			}
		}
		return true;
	}

	/**
	 * Clean the uploads folder
	 */
	protected function cleanFolder()
	{
		File::cleanDirectory($this->uploadsfolder);
	}

	/**
	 * Import the images from the uploads folder
	 *
	 * @param  boolean $clean
	 * @return boolean
	 */
	public function import($clean = true)
	{
		if ( ! $this->copyFiles() ) return false;
		if ( ! $this->toDB() ) return false;

		if ($clean)
		{
			$this->cleanFolder();
		}
		return true;
	}

	/**
	 * An alternative more semantic shortcut to the message container.
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages;
	}

}
