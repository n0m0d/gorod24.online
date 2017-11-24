<?php

class Controller_ajax extends Controller
{
	
	function action_index($array = array())	{
		$action = $this->POST['ajax_action'];
		$action = (!empty($action))?$action:$_REQUEST['ajax_action'];
		if (method_exists($this, $action)){
			$this->$action();
			exit;
		}
		elseif(has_action('admin_ajax_'.$action)) {
			echo do_action('admin_ajax_'.$action, $this->POST);
			exit;
		}
		else {
			echo "ERROR $action";
			exit;
		}
	}
	
	function redirectToCityUrl(){
		$_SESSION['user_destination']['questuion'] = true;
	}
	
	function getCities(){
			$search = $this->POST['search'];
			$data = $this->model->get_cities($search);
			echo json_encode($data);
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
		 
		 
		// Open temp file
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		  // Read binary input stream and append it to temp file
		  $in = @fopen($_FILES['file']['tmp_name'], "rb");
		 
		  if ($in) {
			while ($buff = fread($in, 4096))
			  fwrite($out, $buff);
		  } else
			die('{"OK": 0, "info": "Failed to open input stream."}');
		 
		  @fclose($in);
		  @fclose($out);
		 
		  @unlink($_FILES['file']['tmp_name']);
		} else
		  die('{"OK": 0, "info": "Failed to open output stream."}');
		 
		 
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
		  // Strip the temp .part suffix off
		  rename("{$filePath}.part", $filePath);
		  
			$finfo = new finfo(FILEINFO_MIME);
			$type = mime_content_type($filePath);
			
			$d = explode('/', $type);
			$http_url = '/uploads/'.$d[0].'/'; $dir = APPDIR . $http_url; 
			if(!is_dir( $dir )){ mkdir($dir); chmod($dir, 0777);}
				$ext = getExtension5($orig_name);
				$fileName = uniqid($d[1].'_').'.'.$ext;
			rename($filePath, $dir.$fileName); $filePath = $dir.$fileName;
		
			$url = 'http://'.$_SERVER['HTTP_HOST'].$http_url.$fileName;
			
			$file_id = $this->model->addMediaFile($fileName, $orig_name, $filePath, $url, $type);
			if($d[0]=='image'){
				if(!is_dir($dir.'50/')){ mkdir($dir.'50/'); chmod($dir.'50/', 0777);}  		image_resize($dir.$fileName, $dir.'50/'.$file_id.'.'.$ext, 50, FALSE, 100);
				if(!is_dir($dir.'100/')){ mkdir($dir.'100/'); chmod($dir.'100/', 0777);}	image_resize($dir.$fileName, $dir.'100/'.$file_id.'.'.$ext, 100, FALSE, 100);
				if(!is_dir($dir.'150/')){ mkdir($dir.'150/'); chmod($dir.'150/', 0777);}	image_resize($dir.$fileName, $dir.'150/'.$file_id.'.'.$ext, 150, FALSE, 100);
				if(!is_dir($dir.'300/')){ mkdir($dir.'300/'); chmod($dir.'300/', 0777);}	image_resize($dir.$fileName, $dir.'300/'.$file_id.'.'.$ext, 300, FALSE, 100);
				if(!is_dir($dir.'600/')){ mkdir($dir.'600/'); chmod($dir.'600/', 0777);}	image_resize($dir.$fileName, $dir.'600/'.$file_id.'.'.$ext, 600, FALSE, 100);
				if(!is_dir($dir.'1000/')){ mkdir($dir.'1000/'); chmod($dir.'1000/', 0777);}	image_resize($dir.$fileName, $dir.'1000/'.$file_id.'.'.$ext, 1000, FALSE, 100);
			}
			$result = json_encode(array(
				"OK" => 1,
				"info" => "Upload successful.",
				"name" => $fileName,
				"url" => $url,
				"id" => $file_id,
				"image_50" => $dir.'50/'.$file_id.'.'.$ext,
				"image_100" => $dir.'100/'.$file_id.'.'.$ext,
				"image_150" => $dir.'150/'.$file_id.'.'.$ext,
				"image_300" => $dir.'300/'.$file_id.'.'.$ext,
				"image_600" => $dir.'600/'.$file_id.'.'.$ext,
				"image_1000" => $dir.'1000/'.$file_id.'.'.$ext,
			));
			die($result);
		}
		
		die('{"OK": 1, "info": "Upload successful."}');
	}
	
	function delete_attribute(){
		$attributes = $this->POST['attributes'];
		
		$this->model->deleteAttributes($attributes);
		$this->model->deleteOptions($attributes);
	}
	
	function delete_pages(){
		$pages = $this->POST['pages'];
		
		$this->model->deletePages($pages);
		$this->model->deletePagesAttributes($pages);
	}
	
	function delete_users(){
		$users = $this->POST['users'];
		do_action('delete-users', $users);
		$this->model->deleteUsers($users);
		$this->model->deleteUsersContacts($users);
		$this->model->deleteUsersPermissions($users);
	}
	
	function delete_products(){
		$items = $this->POST['items'];
		do_action('delete-products', $items);
		$this->model->deleteProducts($items);
	}
	
	function delete_groups(){
		$items = $this->POST['items'];
		do_action('delete-groups', $items);
		$this->model->deleteGroups($items);
	}
	
	function delete_invoices(){
		if( $this->getPermissions('invoices-delete') ){
			$items = $this->POST['items'];
			do_action('delete-invoices', $items);
			$this->model->deleteInvoices($items);
		}
	}
	
	function delete_files(){
		$files = $this->POST['pages'];
		
		for($i=0;$i<=count($files);$i++){
			$file = $this->model->get_file($files[$i]);
			$file['post_content'] = json_decode($file['post_content'], true);
			$dir=dirname($file['post_content']['destination']);
			$ext = getExtension5($file['post_name']);
			@unlink($dir.'/50/'.$file['post_id'].'.'.$ext);
			@unlink($dir.'/100/'.$file['post_id'].'.'.$ext);
			@unlink($dir.'/150/'.$file['post_id'].'.'.$ext);
			@unlink($dir.'/300/'.$file['post_id'].'.'.$ext);
			@unlink($dir.'/1000/'.$file['post_id'].'.'.$ext);
			@unlink($file['post_content']['destination']);
		}
		
		$this->model->deletePages($files);
		$this->model->deletePagesAttributes($files);
	}
	
	function update_page(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$post_id = $send['post_id'];
		$post_name = $send['post_name'];
		$post_content = $send['post_content'];
		$post_parent = $send['post_parent'];
		$post_is_main = $send['post_is_main'];
		$post_template = $send['post_template'];
		
		if($post_id =='new'){
			$post = $this->model->addPage($send);
			$post_id = $post['post_id'];
		} 
		else {
			$post = $this->model->updatePage($send);
		}
		
		if($post_is_main == "1"){
			$this->model->setMainPage($post_id);
		} else {
			$this->model->unsetMainPage($post_id);
		}
		
		$del = $send['del'];
		$deleted = $this->model->deletePageAttribute($del);
		
		$update = $send['update'];
		$updated = $this->model->updatePageAttribute($update);
		
		$add = $send['add'];
		$added = $this->model->addPageAttribute($add, $post_id);
		do_action('admin-after-update_page', $post_id, $send);
		$ret = array('post_id' => $post_id, 'post' => $post, 'deletedAttributes' => $deleted, 'updatedAttributes' => $updated, 'addedAttributes' => $added);
		
		echo json_encode($ret);
	}
	
	function update_attribute(){
		$send = $this->POST['send'];
		$at_id = $send['at_id'];
		$at_name = $send['post_name'];
		$at_type = $send['atribute_type'];
		$at_key = $send['at_key'];
		$at_comment = $send['at_comment'];
		$at_defval = $send['default_val'];
		
		if($at_id =='new'){
			$at_id = $this->model->addAttribute($at_name, $at_type, $at_key, $at_comment, $at_defval);
		} 
		else {
			$this->model->updateAttribute($at_id, $at_name, $at_type, $at_key, $at_comment, $at_defval);
		}
		
		$del = $send['del'];
		$this->model->deleteAttributeOption($del);
		
		$update = $send['update'];
		$this->model->updateAttributeOption($update);
		
		$add = $send['add'];
		$added = $this->model->addAttributeOption($add, $at_id);
		do_action('admin-after-update_attribute', $at_id, $send);
		$ret = array('at_id' =>$at_id, 'options' => $added);
		echo json_encode($ret);
		
		//echo $at_id;
	}
	
	function update_invoice(){
		$send = $_POST['send'];
		$inv_id = $send['inv_id'];
		$bas_id = $send['bas_id'];
		
		if($inv_id =='new'){
			$invoice = $this->model->createInvoice($send); $inv_id = $invoice['inv_id'];
			$bascket = $this->model->createBascket($inv_id, $send); $bas_id = $bascket['bas_id'];
			
		}
		else {
			$invoice = $this->model->updateInvoice($send);
			$bascket = $this->model->updateBascket($send);
			if($send['def_status']!="1")
			$this->model->clearBasket($bas_id, $inv_id);
		}
		if($send['def_status']!="1")
		$send['bas_items'] = $this->model->addToBasket($bas_id, $inv_id, $send['bas_items'], $send);
		do_action('admin-after-update_invoice', $inv_id, $send);
		$ret = array(	
						'inv_id' => $inv_id, 
						'bas_id' => $bas_id, 
						'invoice' => $invoice, 
						'bascket' => $bascket, 
						'bas_items' => $send['bas_items'], 
					);
		echo json_encode($ret);
	}
	
	function update_user(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$user_id = $send['user_id'];
		
		if($user_id =='new'){
			$user = $this->model->addUser($send);
			$user_id = $user['user_id'];
		} 
		else {
			$user = $this->model->updateUser($send);
		}
		do_action('admin-after-update_user', $user_id, $send);
		$ret = array('user_id' => $user_id, 'user' => $user);
		
		echo json_encode($ret);
	}
	
	function update_product(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$prod_id = $send['main_id'];
		
		if($prod_id =='new'){
			$product = $this->model->addProduct($send);
			$prod_id = $product['prod_id'];
		} 
		else {
			$product = $this->model->updateProduct($send);
		}
		do_action('admin-after-update_product', $prod_id, $send);
		$ret = array('prod_id' => $prod_id, 'product' => $product);
		echo json_encode($ret);
	}
	
	function update_productgroup(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$group_id = $send['main_id'];
		
		if($group_id =='new'){
			$group = $this->model->addGroup($send);
			$group_id = $group['group_id'];
		} 
		else {
			$group = $this->model->updateGroup($send);
		}
		do_action('admin-after-update_productgroup', $group_id, $send);
		
		$ret = array('group_id' => $group_id, 'group' => $group);
		echo json_encode($ret);
	}
	
	function update_mail_template(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$post_id = $send['post_id'];
		
		if($post_id =='new'){
			$post = $this->model->add_mail_template($send);
			$post_id = $post['post_id'];
		} 
		else {
			$post = $this->model->update_mail_template($send);
		}
		
		do_action('admin-after-update_mailtemplate', $post_id, $send);
		$ret = array('post_id' => $post_id, 'post' => $post);
		
		echo json_encode($ret);
	}
	
	function update_mediafile(){
		$send = $this->POST['send'];
		$send = $_POST['send'];
		$post_id = $send['post_id'];
		$post_name = $send['post_name'];
		$post_content = $send['post_content'];
		
		if($post_id =='new'){
			$post = $this->model->addPage($send);
			$post_id = $post['post_id'];
		} 
		else {
			$post = $this->model->updatePage($send);
		}
		do_action('admin-after-update_mediafile', $post_id, $send);
		$ret = array('post_id' => $post_id, 'post' => $post, 'deletedAttributes' => $deleted, 'updatedAttributes' => $updated, 'addedAttributes' => $added);
		
		echo json_encode($ret);
	}
	
	function get_autocomplete(){
		$table = (!empty($_REQUEST['table']))?$_REQUEST['table']:'`mvc_users_contacts`';
		$type = $_REQUEST['type'];
		$search = $_REQUEST['search'];
		
		switch($type){
			case "user_route" : 		{	$result = $this->model->get_uniq_contacts($table, 'user_route', $search);		break;}
			case "address_index" : 		{	$result = $this->model->get_uniq_contacts($table, 'address_index', $search);	break;}
			case "address_country" : 	{	$result = $this->model->get_uniq_contacts($table, 'address_country', $search);	break;}
			case "address_city" : 		{	$result = $this->model->get_uniq_contacts($table, 'address_city', $search);		break;}
			case "address_region" : 	{	$result = $this->model->get_uniq_contacts($table, 'address_region', $search);	break;}
			case "address_streat" : 	{	$result = $this->model->get_uniq_contacts($table, 'address_streat', $search);	break;}
		}
		echo json_encode($result);
	}
	
	function getFiles($data=array()){
		$contentType = $this->POST['contentType'];
		$files = $this->model->get_files(array( 'search' => $contentType ));
		foreach($files as $i => $file){
			$files[$i]['post_content'] = json_decode($file['post_content']);
		}
		echo json_encode($files);
	}
	
	function update_admin_page(){
		$data = $_POST;
		$table = $_POST['table'];
		$primary_key = $_POST['primary_key'];
		$primary_key_value = $_POST['primary_key_value'];
		$add_date = $_POST['add_date'];
		$update_date = $_POST['update_date'];
		unset($data['ajax_action']); unset($data['table']); unset($data['primary_key']); unset($data['primary_key_value']); unset($data['add_date']); unset($data['update_date']);
		
		if($primary_key_value!=''){
			if(!empty($update_date)) $data[$update_date]=date('Y-m-d H:i:s');
			$this->model->db->query("UPDATE {$table} SET ?u WHERE {$primary_key}='{$primary_key_value}'", $data);
		}
		else{
			if(!empty($add_date)) $data[$add_date]=date('Y-m-d H:i:s');
			if(!empty($update_date)) $data[$update_date]=date('Y-m-d H:i:s');
			$this->model->db->query("INSERT INTO {$table} SET ?u", $data);
			$primary_key_value=$this->model->db->insertId();
		}
		$row = $this->model->db->GetRow("SELECT * FROM {$table} WHERE {$primary_key}='{$primary_key_value}'");
		
		echo json_encode($row, JSON_UNESCAPED_UNICODE);
	}
	
	function delete_admin_page(){
		$data = $_POST;
		$table = $_POST['table'];
		$primary_key = $_POST['primary_key'];
		$primary_key_value = $_POST['primary_key_value'];
		$add_date = $_POST['add_date'];
		$update_date = $_POST['update_date'];
		unset($data['ajax_action']); unset($data['table']); unset($data['primary_key']); unset($data['primary_key_value']); unset($data['add_date']); unset($data['update_date']);
		$this->model->db->query("DELETE FROM {$table} WHERE {$primary_key} IN (?a)", json_decode($data['items'], true));
	}
	
	
}
