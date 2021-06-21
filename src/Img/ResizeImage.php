<?php
namespace Furo\Img;

use Exception;

/**
 * Resize image class will allow you to resize an image: jpg, png, gif, webp
 *
 * Can resize to exact size
 * Max width size while keep aspect ratio
 * Max height size while keep aspect ratio
 * Automatic while keep aspect ratio
 */
class ResizeImage
{
	private $ext;
	private $image;
	private $newImage;
	private $origWidth;
	private $origHeight;
	private $resizeWidth;
	private $resizeHeight;

	/**
	 * Class constructor requires to send through the image filename
	 *
	 * @param string $filename - Filename of the image you want to resize
	 */
	public function __construct( $filename )
	{
		if(file_exists($filename)) {
			$this->setImage( $filename );
		} else {
			throw new Exception('ERR_IMAGE_PATH', 400);
		}
	}

	/**
	 * Set the image variable by using image create
	 *
	 * @param string $filename - The image filename
	 */
	private function setImage( $filename )
	{
		$size = getimagesize($filename);
		$this->ext = $size['mime'];
		switch($this->ext) {
			case 'image/jpg':
			case 'image/jpeg':
				$this->image = imagecreatefromjpeg($filename);
				break;
			case 'image/gif':
				$this->image = @imagecreatefromgif($filename);
				break;
			case 'image/png':
				$this->image = @imagecreatefrompng($filename);
				break;
			case 'image/webp':
				$this->image = @imagecreatefromwebp($filename);
				break;
			default:
				throw new Exception("ERR_IMAGE_MIME", 400);
		}
		$this->origWidth = imagesx($this->image);
		$this->origHeight = imagesy($this->image);
	}
	/**
	 * Save the image as the image type the original image was
	 *
	 * @param  String[type] $savePath     - The path to store the new image
	 * @param  string $imageQuality 	  - The qulaity level of image to create
	 *
	 * @return Saves the image
	 */
	public function save($savePath, $imageQuality="100", $download = false)
	{
		switch($this->ext) {
			case 'image/jpg':
			case 'image/jpeg':
				if (imagetypes() & IMG_JPG) {
					imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->origWidth, $this->origHeight);
					imagejpeg($this->newImage, $savePath, $imageQuality);
				}
				break;
			case 'image/gif':
				if (imagetypes() & IMG_GIF) {
					imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->origWidth, $this->origHeight);
					imagegif($this->newImage, $savePath);
				}
				break;
			case 'image/png':
				$imageQuality = 9 - round(($imageQuality/100) * 9);
				if (imagetypes() & IMG_PNG) {
					imagealphablending($this->newImage, false);
					imagesavealpha($this->newImage, true);
					$transparent = imagecolorallocatealpha($this->newImage, 255, 255, 255, 127);
					imagefilledrectangle($this->newImage, 0, 0, $this->resizeWidth, $this->resizeHeight, $transparent);
					imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->origWidth, $this->origHeight);
					imagepng($this->newImage, $savePath, $imageQuality);
				}
				break;
			case 'image/webp':
				if (imagetypes() & IMG_WEBP) {
					imagealphablending($this->newImage, false);
					imagesavealpha($this->newImage, true);
					$transparent = imagecolorallocatealpha($this->newImage, 255, 255, 255, 127);
					imagefilledrectangle($this->newImage, 0, 0, $this->resizeWidth, $this->resizeHeight, $transparent);
					imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->origWidth, $this->origHeight);
					imagewebp($this->newImage, $savePath, $imageQuality);
				}
				break;
		}
		if($download) {
			header('Content-Description: File Transfer');
			header("Content-type: application/octet-stream");
			header("Content-disposition: attachment; filename= ".$savePath."");
			readfile($savePath);
		}
		imagedestroy($this->newImage);
	}
	/**
	 * Resize the image to these set dimensions
	 *
	 * @param  int $width        	- Max width of the image
	 * @param  int $height       	- Max height of the image
	 * @param  string $resizeOption - Scale option for the image
	 *
	 * @return Save new image
	 */
	public function resizeTo( $width, $height, $resizeOption = 'default' )
	{
		switch(strtolower($resizeOption)) {
			case 'exact':
				$this->resizeWidth = $width;
				$this->resizeHeight = $height;
			break;
			case 'maxwidth':
				$this->resizeWidth  = $width;
				$this->resizeHeight = $this->resizeHeightByWidth($width);
			break;
			case 'maxheight':
				$this->resizeWidth  = $this->resizeWidthByHeight($height);
				$this->resizeHeight = $height;
			break;
			default:
				if($this->origWidth > $width || $this->origHeight > $height)
				{
					if ( $this->origWidth > $this->origHeight ) {
						$this->resizeHeight = $this->resizeHeightByWidth($width);
			  			$this->resizeWidth  = $width;
					} else if( $this->origWidth < $this->origHeight ) {
						$this->resizeWidth  = $this->resizeWidthByHeight($height);
						$this->resizeHeight = $height;
					}
				} else {
					$this->resizeWidth = $width;
					$this->resizeHeight = $height;
				}
			break;
		}
		$this->newImage = imagecreatetruecolor($this->resizeWidth, $this->resizeHeight);
	}
	/**
	 * Get the resized height from the width keeping the aspect ratio
	 *
	 * @param  int $width - Max image width
	 *
	 * @return Height keeping aspect ratio
	 */
	private function resizeHeightByWidth($width)
	{
		return floor(($this->origHeight/$this->origWidth)*$width);
	}
	/**
	 * Get the resized width from the height keeping the aspect ratio
	 *
	 * @param  int $height - Max image height
	 *
	 * @return Width keeping aspect ratio
	 */
	private function resizeWidthByHeight($height)
	{
		return floor(($this->origWidth/$this->origHeight)*$height);
	}

	/**
	 * Get upload unique file path and create dir structure
	 *
	 * @param  string $path - Path to image or file with extensions
	 *
	 * @return string Path to new file
	 */
	function uploadPath($path, $mkdir = true, $dir = 'media')
	{
		if(empty($dir)) {
			throw new Exception("ERR_UPLOAD_DIR", 400);
		}
		// Extension
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$dir = rtrim($dir, '/');
		$dir = ltrim($dir, '/');
		// Unique id
		$uid = bin2hex(random_bytes(20));
		$path = rtrim($path, '/');
		$h = md5($uid . $path);
		// Dir structure
		$d1 = substr($h,0,2);
		$d2 = substr($h,2,2);
		$d3 = substr($h,4,2);
		// New path
		$p = $_SERVER['DOCUMENT_ROOT'].'/'.$dir.'/'.$d1.'/'.$d2.'/'.$d3;
		if($mkdir == true) {
			mkdir($p, 0777, true);
		}
		return $p.'/'.$h.'.'.$ext;
	}
}
/*
use Furo\Img\ResizeImage;

$resize = new ResizeImage('images/Be-Original.jpg');
// resize
$resize->resizeTo(100, 100, 'maxWidth');
$resize->resizeTo(100, 100, 'maxHeight');
$resize->resizeTo(100, 100, 'exact');
$resize->resizeTo(100, 100);
// save
$resize->save('images/be-original-maxWidth.jpg');
// download
$resize->save('images/be-original-exact.jpg', "100", true);
*/
?>