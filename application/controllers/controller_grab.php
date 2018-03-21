<?php
class controller_grab extends Controller
{
	function __construct(){

	}
	
	public function action_index($array = array()){
		//header("Content-type: text/plain; charset=UTF-8");
		$model_news_grab = new model_news_grab();
		$model_news_grab_photos = new model_news_grab_photos();
		$model_news_grab_settings = new model_news_grab_settings();
		
		$sites = $model_news_grab_settings->getItemsWhere("`id`=8");
		foreach($sites as $site) {
		echo "<h1>{$site['name']}:</h1>";
		$url = $site['domain'];
		$news_page = $site['news'];
		$source_name = $site['name'];
		$city_id = $site['city_id'];
		$protocol = explode(":", $url);
		$protocol = $protocol[0];
		
		$news = $site['container'];
		$news_head = $site['container_head'];
		$news_link = $site['container_link'];
		$head_source = $site['head'];
		$body_source = $site['body'];
		$images_source = $site['photos'];
		$headers = $site['headers'];
		
		$context = stream_context_create(array('https' => array('header' => ">User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0);\r\nCookie: beget=begetok; Cookie: path=/\r\n")));
		//$response = file_get_contents($url, false, $context);
		//echo $response;
		
		$response = $this->getContent($url.$news_page); # This returns HTML 
		$html = str_get_html($response);
		$items = $html->find($news);
		foreach($items as $element) {
			if($news_head){ $head = $element->find($news_head)[0]->plaintext; } else { $head = $element->plaintext; } 
			if($news_link){$source = $element->find($news_link)[0]->href; } else { $source = $element->href; }
			
			$ch = $model_news_grab->getCountWhere("`head`='{$head}'");
			if(!$ch){
				if(substr($source, 0, 2)=='//'){ $source = $protocol.':'.$source;}
				elseif(substr($source, 0, strlen($protocol))!=$protocol){ $source = $url.$source;}
				//$response_new = file_get_contents($source, false, $context);
				//var_dump($source); exit;
				if($source != $url){
				$response_new = $this->getContent($source);
				$html_new = str_get_html($response_new);
				
				if($html_new){
				$head2 = $html_new->find($head_source)[0]->plaintext;
				$body = $html_new->find($body_source)[0]->innertext;
				$images = $html_new->find($images_source);
				
				$body_new = str_get_html($body);
				foreach($body_new->find('img') as $node){
						$node->outertext = '';
				}
				foreach($body_new->find('script') as $node){
						$node->outertext = '';
				}
				foreach($body_new->find('style') as $node){
						$node->outertext = '';
				}
				$body_new->save();
				$body = $body_new->plaintext;
				
				$new = [
					'head' => ($head2?$head2:$head),
					'body' => ($body?$body:''),
					'city_id' => $city_id,
					'status' => 1,
					'date' => date('Y-m-d H:i:s'),
					'domain' => $url,
					'source' => $source,
					'source_name' => $source_name,
					'confirm_id' => 0,
				];
				echo "<h3>{$source_name}: <a href=\"{$source}\" target=\"_blank\">{$head}</a></h3>";
				echo "<div>{$body_source}: {$body}</div>";
				echo "<div>{$images_source}: {$images}</div>";
				//echo "<div>{$html_new}</div>";
				//echo $images_source;
				//var_dump($new);
				//continue;
				//exit;
				$ch = $model_news_grab->getCountWhere("`head`='{$new['head']}'");
				if(!$ch){
				$id = $model_news_grab->Insert($new);
				
				if(!empty($images)){
					foreach($images as $i=>$image){
						$image_src = $image->src;
				
						if(substr($image_src, 0, 2)=='//'){ $image_src = $protocol.':'.$image_src;}
						elseif(substr($image_src, 0, strlen($protocol))!=$protocol){ $image_src = $url.$image_src;}
						
						//echo $image_src;
						
						$ext = getExtension1($image_src);
						$name = "{$id}-{$i}.{$ext}";
						if(substr($image_src, 0, strlen($protocol))!=$protocol){ $image_src = $protocol.':'.$image_src;}
						$image_data = file_get_contents($image_src, false, $context);
						if($image_data){
							file_put_contents(APPDIR . '/uploads/image/news_grub/'.$name, $image_data);
							$model_news_grab_photos->Insert([
								'new_id' => $id,
								'photo' => '/uploads/image/news_grub/'.$name,
								'photo_id' => 0,
							]);
						}
					}
				}
				}
				}
				}
			}
		}
		
		}
		
	}
	
	public function action_test($array = array()){
		$url = $_GET['url'];
		$response = $this->getContent($url); # This returns HTML 
		echo $response;
	}
	
	public function getContent($url){
		$ch = curl_init(); 
		curl_setopt ($ch, CURLOPT_URL, $url); 
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
		curl_setopt ($ch, CURLOPT_COOKIE, "beget=begetok; path=/");​;
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$store = curl_exec ($ch); 
		$response = curl_exec ($ch); # This returns HTML 
		curl_close ($ch);  
		return $response;
	}
	
	
}