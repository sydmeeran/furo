<?php
namespace Furo\Img;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class Image
{
	protected $Path = '';
	protected $Extension = '';
	protected $RawImage = null;
	protected $Size = null;

	function load($path)
	{
		// Path
		$this->Path = $path;
		// File
		$this->Extension = $this->fileExtension($this->Path);
		// Image
		$image = new Imagine();
		$this->RawImage = $image->open($this->Path);
		// Size
		$this->Size = $this->RawImage->getSize();

		return $this;
	}

	function fileExists($path)
	{
		if(!file_exists($path))
		{
			throw new Exception("ERR_FILE_PATH", 1);
		}
	}

	function fileExtension($path)
	{
		$this->fileExists($path);

		return pathinfo($path, PATHINFO_EXTENSION);
	}

	function resizeImage(int $width, int $height = 0)
	{
		// Image width
		$ratio = $width / $this->Size->getWidth();

		// Auto resize
		if($height == 0)
		{
			$height = $ratio * $this->Size->getHeight();
		}

		$this->RawImage->resize(new Box($width, $height), ImageInterface::FILTER_LANCZOS);

		return $this;
	}

	function crop($width, $height, $start_width = 0, $start_height = 0)
	{
		$this->RawImage->crop(new Point($start_width, $start_height), new Box($width, $height));

		return $this;
	}

	function save($path, $flatten = true)
	{
		if($flatten) {
			$this->RawImage->save($path, array('flatten' => true));
		}else {
			$this->RawImage->save($path, array('flatten' => false));
		}

		return $this;
	}

	function show($type = 'jpg')
	{
		ob_end_clean();

		if($type == 'png' || $type == 'gif' || $type == 'webp')
		{
			$this->RawImage->show($type);
		}
		else
		{
			$this->RawImage->show('jpg');
		}
	}

	function saveQualityPng($path, $quality = 9)
	{
		if($quality < 0 || $quality > 9)
		{
			$quality = 9;
		}
		$this->RawImage->save($path, array('png_compression_level' => $quality, 'flatten' => false)); // from 0 to 9
	}

	function saveQualityJpg($path, $quality  = 100)
	{
		if($quality < 0 || $quality > 100)
		{
			$quality = 100;
		}
		$this->RawImage->save($path, array('jpeg_quality' => $quality, 'flatten' => false)); // from 0 to 100
	}
}

/*
use Furo\Img\Image;

$img = new Image();
// Resize and save
$img->load('media/img/img.png')->resizeImage(50,0)->save('/tmp/img-save.jpg');
// Resize and show image
$img->load('media/img/error.jpg')->resizeImage(400,0)->show('png');
// Crop
$img->load('media/img/error.jpg')->crop(50,50,0,0)->show('png');
*/