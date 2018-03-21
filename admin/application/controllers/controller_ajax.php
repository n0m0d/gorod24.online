<?php
class controller_ajax extends Controller
{
	function __construct(){

	}

	function action_index($array = array())	{

	}

	function action_uploads_folders($array = array()){
		$model = new model_uploads();
		$accept = $this->varChek($_POST['accept']);
		switch($accept){
			case 'image': $t=" AND `type`='image'";break;
			case 'audio':  $t=" AND `type`='audio'"; break;
			default: $t = ''; break;
		}
		$folders = $model->db()->getCol("select `type` from `{$model->getdatabasename()}`.`{$model->gettablename()}` WHERE 1 {$t} group by `type`");
		echo json_encode($folders, JSON_UNESCAPED_UNICODE);
	}

	function action_uploads_files($array = array()){
		$model = new model_uploads();
		$folder = $this->varChek($_POST['folder']);
		$accept = $this->varChek($_POST['accept']);
		$start = (int)$this->varChek($_POST['start']);
		switch($accept){
			case 'image': $t=" AND `type`='image'";break;
			case 'audio':  $t=" AND `type`='audio'"; break;
			default: $t = ''; break;
		}
		$folders = $model->db()->getCol("select `destination` from `{$model->getdatabasename()}`.`{$model->gettablename()}` WHERE `destination` LIKE '/uploads/{$folder}/%' and `destination`!='/uploads/{$folder}/' group by `destination`");
		$files = $model->getItemsWhere("`destination`='/uploads/{$folder}/' {$t}", "id desc", $start, 20);
		$result = [];
		foreach($folders as $i=>$folder){
			$result[]=[
				"type" => 'folder',
				"name" => substr($folder, 9),
			];
		}
		foreach($files as $i=>$file){
			$r = [
				"type" => $file['type'],
				"id" => $file['id'],
				"name" => $file['name'],
				"original_name" => $file['original_name'],
				"ext" => $file['ext'],
				"size" => $file['size'],
				"destination" => $file['destination'],
				"url" => $file['destination'].$file['name'],
			];
			
			if($file['type'] == 'image'){
				list($width, $height, $type) = getimagesize(APPDIR . $file['destination'].$file['name']);
				$r['image'] = [
					'width' => $width,
					'height' => $height,
				];
			}
			
			$result[]=$r;
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}

	function action_upload(){
		if (empty($_FILES) || $_FILES['file']['error']) {
		  die('{"OK": 0, "info": "Failed to move uploaded file."}');
		}
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
		$orig_name = $_REQUEST["name"];

		if(!is_dir('uploads')) mkdir('uploads');
		$filePath = APPDIR . "/uploads/$fileName";

		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
			$in = @fopen($_FILES['file']['tmp_name'], "rb");
			if ($in) {
				while ($buff = fread($in, 4096)) fwrite($out, $buff);
			} else die('{"OK": 0, "info": "Failed to open input stream."}');
		@fclose($in);
		@fclose($out);
		@unlink($_FILES['file']['tmp_name']);
		} else die('{"OK": 0, "info": "Failed to open output stream."}');

		if (!$chunks || $chunk == $chunks - 1) {
			rename("{$filePath}.part", $filePath);
			$type = mime_content_type($filePath);
			$d = explode('/', $type);
			$http_url = '/uploads/'.$d[0].'/';
			$dir = APPDIR . $http_url;
			if(!is_dir( $dir )){ mkdir($dir); chmod($dir, 0777);}
			$ext = getExtension5($orig_name);
			$fileName = uniqid($d[1].'_').'.'.$ext;
			$data = [
				"name" => $fileName,
				"original_name" => $orig_name,
				"ext" => $ext,
				"type" => $d[0],
				"size" => filesize($filePath),
				"destination" => $http_url,
				"author" => $_SESSION['user_id'],
				"date" => date('Y-m-d H:i:s'),
				"modified" => date('Y-m-d H:i:s'),
				"status" => 1,
				"other" => '',
			];
			$model_uploads = new model_uploads();
			$id = $model_uploads->InsertUpdate($data);
			$url = 'http://'.$_SERVER['HTTP_HOST'].$http_url.$fileName;

			rename($filePath, $dir.$fileName); $filePath = $dir.$fileName;
	
			$r = [
				"OK" => 1,
				"info" => "Upload successful.",
				"type" => $data['type'],
				"id" => $id,
				"name" =>$data['name'],
				"ext" => $data['ext'],
				"ext" => $data['size'],
				"ext" => $data['destination'],
				"url" => $file['destination'].$file['name'],
			];
			
			if($data['type'] == 'image'){
				list($width, $height, $type) = getimagesize(APPDIR . $file['destination'].$file['name']);
				$r['image'] = [
					'width' => $width,
					'height' => $height,
				];
			}
			
			$result = json_encode($r);
			die($result);

		}
		die('{"OK": 1, "info": "Upload successful."}');
	}
	
	function action_cropImage(){
		$data = $_POST['image'];
		$model_uploads = new model_uploads();
		$image = $model_uploads->getItem($data['id']);
		if(!empty($image)){
			if(in_array($image['ext'], ['jpg','jpeg','gif','png'])){
				$source = APPDIR . $image['destination'].$image['name'];
				$type = mime_content_type($source);
				$d = explode('/', $type);
				$http_url = '/uploads/'.$d[0].'/';
				$ext = getExtension5($image['name']);
				$fileName = uniqid('crop_'.$d[1].'_').'.'.$ext;
				$destination = APPDIR . $http_url.$fileName;
				image_crop($source, $destination, $data['x1'], $data['y1'], $data['w'], $data['h'], $data['percent']);
			
				$u_data = [
					"name" => $fileName,
					"original_name" => $image['original_name'],
					"ext" => $ext,
					"type" => $d[0],
					"size" => filesize($destination),
					"destination" => $http_url,
					"author" => $_SESSION['user_id'],
					"date" => date('Y-m-d H:i:s'),
					"modified" => date('Y-m-d H:i:s'),
					"status" => 1,
					"other" => '',
				];
				$id = $model_uploads->InsertUpdate($u_data);
			
				$r = [
					"OK" => 1,
					"info" => "Crop successful.",
					"type" => $u_data['type'],
					"id" => $id,
					"name" =>$u_data['name'],
					"original_name" =>$u_data['original_name'],
					"ext" => $u_data['ext'],
					"ext" => $u_data['size'],
					"ext" => $u_data['destination'],
					"url" => $u_data['destination'].$u_data['name'],
				];
				
				if($data['type'] == 'image'){
					list($width, $height, $type) = getimagesize(APPDIR . $u_data['destination'].$u_data['name']);
					$r['image'] = [
						'width' => $width,
						'height' => $height,
					];
				}
				echo json_encode($r);
			}
			else { die('{"error":1, "Incorect file type!"}'); }
		}
		else { die('{"error":1, "File not found!"}'); }
	}
	
	function action_cropImageSrc(){
		$data = $_POST['image'];
		$model_uploads = new model_uploads();
		//$image = $model_uploads->getItem($data['id']);
		if(file_exists(APPDIR . $data['src'])){
			$source = APPDIR . $data['src'];
			$type = mime_content_type($source);
			$d = explode('/', $type);
			$http_url = '/uploads/'.$d[0].'/';
			$ext = getExtension5($data['src']);
			$fileName = uniqid('crop_'.$d[1].'_').'.'.$ext;
			$destination = APPDIR . $http_url.$fileName;
			image_crop($source, $destination, $data['x1'], $data['y1'], $data['w'], $data['h'], 100);
		
			$u_data = [
				"name" => $fileName,
				"original_name" => $fileName,
				"ext" => $ext,
				"type" => $d[0],
				"size" => filesize($destination),
				"destination" => $http_url,
				"author" => $_SESSION['user_id'],
				"date" => date('Y-m-d H:i:s'),
				"modified" => date('Y-m-d H:i:s'),
				"status" => 1,
				"other" => '',
			];
			$id = $model_uploads->InsertUpdate($u_data);
		
			$r = [
				"OK" => 1,
				"info" => "Crop successful.",
				"type" => $u_data['type'],
				"id" => $id,
				"name" =>$u_data['name'],
				"original_name" =>$u_data['original_name'],
				"ext" => $u_data['ext'],
				"ext" => $u_data['size'],
				"ext" => $u_data['destination'],
				"url" => $u_data['destination'].$u_data['name'],
			];
			
			if($data['type'] == 'image'){
				list($width, $height, $type) = getimagesize(APPDIR . $u_data['destination'].$u_data['name']);
				$r['image'] = [
					'width' => $width,
					'height' => $height,
				];
			}
			echo json_encode($r);
		}
		else { die('{"error":1, "File not found!"}'); }
	}

}
