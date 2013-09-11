<?php

use Patchwork\Utf8 as UTF8;
// use FilesystemIterator;
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
	 * Convert a string to a URL-safe string.
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
	 * Add a message to the collection of messages.
	 */
	protected function addMessage($attribute, $message)
	{
		$this->messages->add($attribute, $message);
	}

	/**
	 * Add a message to the collection of messages.
	 */
	protected function addError($errormessage)
	{
		$this->addMessage('error', $errormessage);
	}

	protected function addToArray($filepath)
	{
		$info = pathinfo($filepath);

		$folderarray = explode(DIRECTORY_SEPARATOR, $info['dirname']);
		unset($folderarray[0]);
		$folder = implode(DIRECTORY_SEPARATOR, $folderarray);

		if ( File::exists($info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.txt') )
		{
			$text = File::get($info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.txt');
		}

		$this->files[$folder][] = array(
			'filename'      => $info['filename'],
			'newfilename'   => $this->urlSafe($info['filename']),
			'ext'           => File::extension($filepath),
			'text'          => (isset($text)) ? $text : null,
		);
	}

	protected function readFiles($directory, $options = null)
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

	public function toDB()
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
				// $i->thumb = $image['newfilename'] . '.' . $image['ext'];
				$i->title = $image['filename'];
				$i->text  = $image['text'];
				$i->album()->associate($a);
				$i->save();

				if ( ! $i->id ) {
					$this->addError('Error saving image to database!');
					return false;
				}
			}
		}
		return true;
	}

	public function copyFiles()
	{
		if ( ! $this->files ) {
			$this->addError('No files where found!');
			return false;
		}

		foreach ($this->files as $album => $images)
		{
			// $album = $this->urlSafe($album);
			$album = $album;
			$targetalbum = ($album) ? $this->urlSafe($album) : 'undefined';

			$folder  = public_path() . DIRECTORY_SEPARATOR . $this->imagesfolder . DIRECTORY_SEPARATOR . $targetalbum;
			$path    = public_path() . DIRECTORY_SEPARATOR . $this->uploadsfolder . DIRECTORY_SEPARATOR . $album . DIRECTORY_SEPARATOR;
			$target  = public_path() . DIRECTORY_SEPARATOR . $this->imagesfolder . DIRECTORY_SEPARATOR . $targetalbum . DIRECTORY_SEPARATOR;
			$thumbs  = $target.'thumbs' . DIRECTORY_SEPARATOR;

			if ( ! File::isDirectory($thumbs) ) {
				if ( ! File::makeDirectory($thumbs, 0777, true) ) {
					$this->addError('Error creating folder!');
					return false;
				}
			}

			foreach ($images as $image)
			{
				$oldfile = $image['filename'].'.'.$image['ext'];
				$newfile = $image['newfilename'].'.'.$image['ext'];

				if ( ! File::copy($path.$oldfile, $target.$newfile) ) {
					$this->addError('Error copying files!');
					return false;
				}

				// open file a image resource
				$img = ImageLib::make($target.$newfile);

				// crop the best fitting 1:1 ratio (200x200) and resize to 200x200 pixel
				$img->grab(Config::get('filegallery.thumbsize'));

				// save the same file as jpeg with default quality
				$img->save($thumbs.$newfile);
			}
		}
		return true;
	}

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

	public function cleanFolder()
	{
		File::cleanDirectory($this->uploadsfolder);
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

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getUploadsFolder()
	{
		return $this->uploadsfolder;
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getImagesFolder()
	{
		return $this->imagesfolder;
	}

}
