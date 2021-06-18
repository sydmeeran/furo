<?php
namespace Furo\Img;

use Exception;

class Download
{
	protected $File = '';
	protected $Speed = 0;
	protected $Mb = 1024 * 1024;
	protected $AllowedExtensions = array();

	function __construct($speed = 3){
		$this->maxSpeed($speed);
	}

	function downloadFile($path){
		// Add file
		$this->addFile($path);
		// Test extension
		$this->isValidExtension($path);
		// Clean
		@ob_end_clean();
		// Errors
		$this->displayErrors();
		// Compress
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		// Headers
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"". basename($this->File) ."\"");
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($this->File));
		// Download
		set_time_limit(0);
		$fh = fopen($this->File, 'rb');
		while (!feof($fh)) {
			echo fread($fh, $this->Speed);
			ob_flush();
			// Download speed
			sleep(1);
		}
		exit;
	}

	function isValidExtension($path){
		if(!empty($this->AllowedExtensions)){
			if(!in_array(pathinfo($path, PATHINFO_EXTENSION), $this->AllowedExtensions)){
				throw new Exception("Error: Incorrect file extension", 3);
			}
		}
	}

	function addExtension($ext){
		if(!empty($ext)){
			$this->AllowedExtensions[] = $ext;
		}else{
			throw new Exception("Error: Add not empty extension", 2);
		}
	}

	protected function maxSpeed($mb = 1){
		$this->Speed = (int) $mb * $this->Mb;
		if($this->Speed < 0){
			$this->Speed = $this->Mb;
		}
	}

	protected function addFile($path){
		if(file_exists($path)){
			$this->File = $path;
		}else{
			throw new Exception("Error: Set file path!", 1);
		}
	}

	protected function displayErrors() {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	}
}

/*
use Furo\Img\Download;

try{
	// Set download speed
	$d = new Download(1);

	// Allow only with extensions
	// $d->addExtension('jpg');
	// $d->addExtension('png');
	// $d->addExtension('route');

	// Download from browser
	$d->downloadFile("route/user-route.route");

}catch(Exception $e){
	echo $e->getMessage() .' '. $e->getCode();
	exit;
}
*/
?>
