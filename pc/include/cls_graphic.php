<?php

class Graphic {
	protected $img;
	protected $type;

	public $quality;

	function __construct($original, $quality = 70){
		$this->quality = $quality;

		preg_match('/(.[\w]+)$/is', $original, $match);
		switch(strtolower($match[1])){
			case '.jpg':
			case '.jpeg': $this->img = imagecreatefromjpeg($original); break;
			case '.png': $this->img = imagecreatefrompng($original); break;
			case '.gif': $this->img = imagecreatefromgif($original); break;
			default: exit;
		}

		$this->type = strtolower($match[1]);
	}

	function __destruct(){
		ImageDestroy($this->img);
	}

	// 设置 png gif 透明图像
	protected function transparentImage($create){
	    if ( ($this->type == ".gif") || ($this->type == ".png") || ($this->type == ".x-png")) {
	        $trnprt_indx = imagecolortransparent($this->img);
	        // If we have a specific transparent color
	        if ($trnprt_indx >= 0) {
	            // Get the original image's transparent color's RGB values
	            $trnprt_color = imagecolorsforindex($this->img, $trnprt_indx);
	            // Allocate the same color in the new image resource
	            $trnprt_indx = imagecolorallocate($create, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
	            // Completely fill the background of the new image with allocated color.
	            imagefill($create, 0, 0, $trnprt_indx);
	            // Set the background color for new image to transparent
	            imagecolortransparent($create, $trnprt_indx);
	        }
	        // Always make a transparent background color for PNGs that don't have one allocated already
	        elseif (($this->type == ".png") || ($this->type == ".x-png")) {
	            // Turn off transparency blending (temporarily)
	            imagealphablending($create, false);
	            // Create a new transparent color for image
	            $color = imagecolorallocatealpha($create, 0, 0, 0, 127);

	            // Completely fill the background of the new image with allocated color.
	            imagefill($create, 0, 0, $color);

	            // Restore transparency blending
	            imagesavealpha($create, true);
	        }
	    }
	}

	protected function export($path = null) {
		switch($this->type){
			case '.jpg':
			case '.jpeg': imagejpeg($this->img, $path, $this->quality); break;
			case '.png': imagepng($this->img, $path, intval($this->quality / 10)); break;
			case '.gif': imagegif($this->img, $path, $this->quality); break;
			default: exit;
		}
	}

	public function getWidth(){
		return imagesx($this->img);
	}

	public function getHeight(){
		return imagesy($this->img);
	}

	public function setImageSize($maxwidth, $maxheight, $strict = false) {
		$ratio = 1;
		$width = $this->getWidth();
		$height = $this->getHeight();

		if (!$strict) {
			$width_ratio = 1;
			$height_ratio = 1;
			if($maxwidth) $width_ratio = $maxwidth / $width;
			if($maxheight) $height_ratio = $maxheight / $height;

			if($width_ratio < $height_ratio) $ratio = $width_ratio;
			else $ratio = $height_ratio;

			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;
		} else {
			$newwidth = $maxwidth;
			$newheight = $maxheight;
		}

		if(function_exists('imagecopyresampled')){
			$tmppic = imagecreatetruecolor($newwidth, $newheight);
			$this->transparentImage($tmppic);
			imagecopyresampled($tmppic, $this->img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
		else {
			$tmppic = imagecreate($newwidth, $newheight);
			$this->transparentImage($tmppic);
			imagecopyresized($tmppic, $this->img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}

		$this->img = $tmppic;
	}

	public function waterMark($waterImage, $pct = 50) {	//水印应为透底的gif或png图片
		$auto_free = false;

		if(is_string($waterImage)){
			$waterImage = new Graphic($waterImage);
			$auto_free = true;
		}

		if($waterImage instanceof Graphic){
			$image_width = $this->getWidth();
			$image_height = $this->getHeight();
			$waterImage_width = $waterImage->getWidth();
			$waterImage_height = $waterImage->getHeight();

			$start_x = ($image_width - $waterImage_width) / 2;
			$start_y = ($image_height - $waterImage_height) / 2;

			imagecopymerge($this->img, $waterImage->img, $start_x, $start_y, 0, 0, $waterImage_width, $waterImage_height, $pct);

			if($auto_free){
				imagedestroy($waterImage->img);
				unset($waterImage);
			}
		}
	}

	public function mosaic($factor = 0.4) {
		$width = $this->getWidth();
		$height = $this->getHeight();
		$this->setImageSize($width * $factor, $height * $factor);

		$width = $this->getWidth();
		$height = $this->getHeight();
		$this->setImageSize($width / $factor, $height / $factor);
	}

	public function resizeImage($newpic, $maxwidth = 350, $maxheight = 350){
		$this->setImageSize($maxwidth, $maxheight);
		$this->export($newpic);
	}

	public function resizeImageStrict($newpic, $width, $height) {
		$this->setImageSize($width, $height, true);
		$this->export($newpic);
	}

	public function cutImageCenter($newpic, $new_width, $new_height){
		$width = $this->getWidth();
		$height = $this->getHeight();

		if ($width < $new_width && $height < $new_height) {
			$this->setImageSize($new_width, $new_height);
		}
		else if ($width > $new_width && $height > $new_height) {
			$ratio1 = $width / $new_width;
			$ratio2 = $height / $new_height;
			
			// 设小一些用于下面的重设大小
			if ($ratio1 < $ratio2) $width = 1;
			else $height = 1;
		}

		if ($width < $new_width) {
			// 设大一些以使宽度足够
			$this->setImageSize($new_width, $new_height * 1000);
		}
		else if ($height < $new_height) {
			// 设大一些以使高度足够
			$this->setImageSize($new_width * 1000, $new_height);
		}

		$old_width = $this->getWidth();
		$old_height = $this->getHeight();

		$oimg = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($oimg, $this->img, 0, 0, ($old_width - $new_width) / 2, ($old_height - $new_height) / 2, $new_width, $new_height, $new_width, $new_height);
		$this->img = $oimg;
		$this->export($newpic);
	}

	public function outPut(){
		ob_end_clean();
		header("Content-Type: image/" . $this->type);
		$this->export($newpic);
	}

	
}
?>