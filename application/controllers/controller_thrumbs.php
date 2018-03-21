<?php
class controller_thrumbs extends Controller
{
	function __construct(){

	}
	
	public function action_index($array = array()){
		
		
	}
	
	public function action_news($array = array()){
		$img = $array[0];
		$name = explode('.', $img)[0];
		$chunks = explode('_', $name);
		$new_id = (int)$chunks[1];
		
		$thrumbs_directory = APPDIR . "/uploads/image/news_thrumbs/{$new_id}/";
		if(!is_dir($thrumbs_directory)) { mkdir($thrumbs_directory);  chmod($thrumbs_directory, 0777); }
		
		if(!file_exists("{$thrumbs_directory}{$img}")){
			$model_gorod_news = new model_gorod_news();
			$type = $chunks[0];
			ini_set("gd.jpeg_ignore_warning", 1); // иначе на некотоых jpeg-файлах не работает
			switch($type){
				case 'new':
					$w = (int)$chunks[2];
					$h = (int)$chunks[3];
					$photo = $model_gorod_news->model_gorod_photos()->getItemWhere("`new_id`='{$new_id}'", "*", "`pos` ASC");
					
					$this->echo_photo($photo, $img, $new_id, $w, $h);
					break;
				case 'photo':
					$photo_id = (int)$chunks[2];
					$w = (int)$chunks[3];
					$h = (int)$chunks[4];
					$photo = $model_gorod_news->model_gorod_photos()->getItemWhere("`new_id`='{$new_id}' AND `id`='{$photo_id}'", "*", "`pos` ASC");
					
					$this->echo_photo($photo, $img, $new_id, $w, $h);
					break;
				case 'bigphoto':
					$photo_id = (int)$chunks[2];
					$w = (int)$chunks[3];
					$h = (int)$chunks[4];
					$photo = $model_gorod_news->model_gorod_photos()->getItemWhere("`new_id`='{$new_id}' AND `id`='{$photo_id}'", "*", "`pos` ASC");
					
					$this->echo_photo($photo, $img, $new_id, $w, $h, true);
					break;
			}
		}
		else {
			header( 'Content-Type: image/jpeg' );
			session_cache_limiter('none');
			header('Cache-control: max-age='.(60*60*24*365));
			header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
			header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime("{$thrumbs_directory}{$img}")));
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				header('HTTP/1.1 304 Not Modified');
				die();
			}
			$content = file_get_contents("{$thrumbs_directory}{$img}");
			echo $content;
			
		}
		
	}
	
	public function echo_photo($photo, $img, $new_id, $w, $h, $big=false){
		$model_gorod_news = new model_gorod_news();
		$new = $model_gorod_news->getItem($new_id);
		$thrumbs_directory = APPDIR . "/uploads/image/news_thrumbs/{$new_id}/";
		if(!is_dir($thrumbs_directory)) { mkdir($thrumbs_directory);  chmod($thrumbs_directory, 0777); }
		if($photo){
			/*
			header('Pragma: public');
			header('Cache-Control: max-age=86400');
			header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
			*/
			header( 'Content-Type: image/jpeg' );
			resize_crop_image($w, $h, APPDIR . $photo['img'],  "{$thrumbs_directory}{$img}");
			if($big){
				$watermark = $new['watermark_big'];
			}
			else {
				$watermark = $new['watermark'];
			}
			
			if($watermark){
				$pic = ImageCreateFromjpeg("{$thrumbs_directory}{$img}");
				$im = imagecreatefrompng(APPDIR . "/uploads/image/watermarks/{$watermark}");
				$color=ImageColorAllocate($pic, 250, 250, 250); //получаем идентификатор цвета
				$grey = imagecolorallocate($im, 128, 128, 128);
				
				list($oldwidth, $oldheight, $type) = getimagesize("{$thrumbs_directory}{$img}");
				if (!$h) { $h = round($w * $oldheight/$oldwidth); }
				elseif (!$w) { $w = round($h * $oldwidth/$oldheight); }
				list($water_width, $water_height, $water_type) = getimagesize(APPDIR . "/uploads/image/watermarks/{$watermark}");
				
				$pos_x = $w - $water_width - 5;
				$pos_y = $h - $water_height - 5;
				imagecopy($pic, $im, $pos_x, $pos_y, 0, 0, imagesx($im), imagesy($im));
				unlink("{$thrumbs_directory}{$img}");
				Imagejpeg($pic, "{$thrumbs_directory}{$img}", 100); //сохраняем рисунок в формате JPEG
				ImageDestroy($pic); //освобождаем память и закрываем изображение
			
			}
			$content = file_get_contents("{$thrumbs_directory}{$img}");
			echo $content;
		}
		else {
			$img_pref = ($new["our"]?"onf":"knf");
			$content = file_get_contents("http://feo.ua/upload/news_fotos_thumb/{$img_pref}_{$new['news_id']}_{$w}_{$h}.jpg");
			if($content){
				file_put_contents("{$thrumbs_directory}{$img}", $content);
				$watermark = $new['watermark'];
				if($watermark){
					$pic = ImageCreateFromjpeg("{$thrumbs_directory}{$img}");
					$im = imagecreatefrompng(APPDIR . "/uploads/image/watermarks/{$watermark}");
					$color=ImageColorAllocate($pic, 250, 250, 250); //получаем идентификатор цвета
					$grey = imagecolorallocate($im, 128, 128, 128);
				
					list($oldwidth, $oldheight, $type) = getimagesize("{$thrumbs_directory}{$img}");
					if (!$h) { $h = round($w * $oldheight/$oldwidth); }
					elseif (!$w) { $w = round($h * $oldwidth/$oldheight); }
					list($water_width, $water_height, $water_type) = getimagesize(APPDIR . "/uploads/image/watermarks/{$watermark}");
					
					$pos_x = $w - $water_width - 5;
					$pos_y = $h - $water_height - 5;
					imagecopy($pic, $im, $pos_x, $pos_y, 0, 0, imagesx($im), imagesy($im));
					unlink("{$thrumbs_directory}{$img}");
					Imagejpeg($pic, "{$thrumbs_directory}{$img}", 100); //сохраняем рисунок в формате JPEG
					ImageDestroy($pic); //освобождаем память и закрываем изображение
				}
				$content = file_get_contents("{$thrumbs_directory}{$img}");
				/*
				header('Pragma: public');
				header('Cache-Control: max-age=86400');
				header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
				*/
				header( 'Content-Type: image/jpeg' );
				echo $content;
			}
			else {
				header("Pragma-directive: no-cache");
				header("Cache-directive: no-cache");
				header("Cache-control: no-cache");
				header("Pragma: no-cache");
				header("Expires: 0");
				header( 'Content-Type: image/png' );
			//image_resize(APPDIR . "/admin/application/views/img/no-photo.png", null, $w, $h, 100);
			resize_crop_image($w, $h, APPDIR . "/admin/application/views/img/no-photo.png", null);
			}
		}
					
	}
	
}