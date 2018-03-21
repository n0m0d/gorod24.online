<?php
class controller_appdownload extends Controller
{
	function __construct(){
		//$this->view = new View('index.tpl');
		//$this->view->headers['title'] = 'Приложение | Город 24 онлайн';
	}
	
	public function action_index($array = array()){
		$Browser = new Browser();
		$platform = $Browser->getPlatform();
		$ip = getIp();
		$gb = new IPGeoBase();
		$IPGeo = $gb->getRecord($ip);
		switch($platform){
			case 'Android':
				if($IPGeo['region']=='Крым'){
					$this->view = new View('index.tpl');
					$this->view->headers['title'] =  'Город24: Всегда там где ты!';
					$this->view->data['content'] =  '<script>$(function(){
						window.location.href="https://gorod24.online/city24.apk";						
					})</script>';
					//header("Location: https://gorod24.online/city24.apk"); 
				}
				else {
					header("Location: https://play.google.com/store/apps/details?id=ru.mobmed.city24"); 
				}
				break;
			case 'iPhone': 
			case 'iPad': 
			case 'iPod': 
			case 'Apple': 
				//$this->view = new View('page.tpl');
				//$this->view->setHeaderView('header-page.tpl');
				//$this->view->setFooterView('footer-page.tpl');
				
				header("Location: https://itunes.apple.com/ru/app/%D0%B3%D0%BE%D1%80%D0%BE%D0%B424-online/id1324112026?l=en&mt=8"); 
				/*
				$this->view->headers['title'] = $meta['title'];
				$this->view->headers['description'] = $meta['description'];
				$this->view->headers['keywords'] = $meta['keywords'];
				$this->view->headers['image'] = $meta['image'];
				$this->view->data['content'] = do_shortcode(apply_filters('the_content',"<h1>Приложение \"Город 24\" выйдет на устройства с IOS операционной системой в ближайшее врямя.</h1>"));
				*/
				break;
			case 'Windows': 
				$this->view = new View('page.tpl');
				//$this->view->setHeaderView('header-page.tpl');
				//$this->view->setFooterView('footer-page.tpl');
				
				$this->view->headers['title'] = $meta['title'];
				$this->view->headers['description'] = $meta['description'];
				$this->view->headers['keywords'] = $meta['keywords'];
				$this->view->headers['image'] = $meta['image'];
				$this->view->data['content'] = do_shortcode(apply_filters('the_content',"<h1>Чтобы скачать приложение \"Город 24\" зайдите на эту страницу с телефона или планшета на базе операционной системы Android или IOS.</h1>"));
				break;
			default : header("Location: https://gorod24.online");  break;
		}
	}
	
	
	public function action_invite($array = array()){
		if($array[0] and !isBot()){
			$invite_code = $array[0];
			
			$model_feo_accounts = new model_feo_accounts();
			$inviter = $model_feo_accounts->getItemWhere("`invite_code` = '{$invite_code}'");

			if($inviter and !$_COOKIE['_i'.$inviter['id']]){
				if($_COOKIE['_i'.$inviter['id']]!='ok'){
				$sum = 10;
				$payment = new model_payment();
				$invoice = $payment->registerInvoice($inviter['id'], "Пополнение счета", $sum, 0, 0, 0, "Пополнение счета (Скачивание по приглашению #{$inviter['id']})");
				$transaction = $payment->transaction_begin($inviter['id'],$invoice['id'], $invoice['price'], $invoice['descr'], $exdata, 'Payment_Render_Test', 'https://xn--e1asq.xn--p1ai/myroot/', 'https://xn--e1asq.xn--p1ai/myroot/');
				$payment->process_transaction($transaction['id']); 
				}
				/*
				var_dump($inviter);
				var_dump($_COOKIE['_i'.$inviter['id']]);
				exit;
				*/
				
				setcookie('_i'.$inviter['id'], 'ok', time()+(3600*24*30*12*100), '/');
			}
		}
		$Browser = new Browser();
		$platform = $Browser->getPlatform();
		$ip = getIp();
		$gb = new IPGeoBase();
		$IPGeo = $gb->getRecord($ip);
		switch($platform){
			case 'Android': 
				if($IPGeo['region']=='Крым'){
					header("Location: https://gorod24.online/city24.apk"); 
				}
				else {
					header("Location: https://play.google.com/store/apps/details?id=ru.mobmed.city24"); 
				}
				break;
			case 'iPhone': 
			case 'iPad': 
			case 'iPod': 
			case 'Apple': 
				//$this->view = new View('page.tpl');
				//$this->view->setHeaderView('header-page.tpl');
				//$this->view->setFooterView('footer-page.tpl');
				
				header("Location: https://itunes.apple.com/ru/app/%D0%B3%D0%BE%D1%80%D0%BE%D0%B424-online/id1324112026?l=en&mt=8"); 
				/*
				$this->view->headers['title'] = $meta['title'];
				$this->view->headers['description'] = $meta['description'];
				$this->view->headers['keywords'] = $meta['keywords'];
				$this->view->headers['image'] = $meta['image'];
				$this->view->data['content'] = do_shortcode(apply_filters('the_content',"<h1>Приложение \"Город 24\" выйдет на устройства с IOS операционной системой в ближайшее врямя.</h1>"));
				*/
				break;
			case 'Windows': 
				$this->view = new View('page.tpl');
				//$this->view->setHeaderView('header-page.tpl');
				//$this->view->setFooterView('footer-page.tpl');
				$this->view->headers['title'] = "Приложение Город24: Всегда там где ты!";
				$this->view->headers['description'] = 'Друзья, рекомендую установить приложение нашего города. В нем очень много полезной информации для Вас. Укажите этот промокод '.$invite_code.' при регистрации, Вы мне очень поможете.';
				$this->view->headers['title'] = $this->view->headers['description'];
				$this->view->headers['keywords'] = $meta['keywords'];
				$this->view->headers['image'] = $meta['image'];
				$this->view->data['content'] = do_shortcode(apply_filters('the_content',"<h1>Чтобы скачать приложение \"Город 24\" зайдите на эту страницу с телефона или планшета на базе операционной системы Android или IOS.</h1>"));
				break;
			default : header("Location: https://gorod24.online");  break;
		}
	}
	
	
	
	public function action_send($array = array()){
		$phone = $_POST['phone'];
		$phone = preg_replace('~[^0-9]+~','',$phone); 
		if(empty($phone)) die('0');
		if(strlen($phone)!=11) die('3');
		$model_sms = new model_sms();
		$result = $model_sms->sendOnceDay('gorod24', $phone, 'Приложение Город 24 https://gorod24.online/appdownload');
		if($result){
			echo '1';
		}
		else {
			echo '2';
		}
	}
	
	public function action_geo($array = array()){
		$ip = getIp();
		$gb = new IPGeoBase();
		$IPGeo = $gb->getRecord($ip);
		
		var_dump($IPGeo);

	}
	
	
}