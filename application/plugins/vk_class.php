<?php
/*

Plugin Name: vk_class
Plugin URI: vk_class
Description: Плагин vk_class
Version: 1.0
Author: Заднепряный Андрей
Author URI: 

*/

if(!class_exists('vk', false)){
class vk {
	private $token;
	private $app_id;
	//ID группы или страницы пользователя
	private $group_id;
	//вероятность публикации поста на стену
	private $delta;
	
	public function __construct( $token, $delta, $app_id, $group_id ) {
		$this->token = $token;
		$this->delta = $delta;
		$this->app_id = $app_id;
		$this->group_id = $group_id;
	}
	
	//постинг на стену
	public function post( $desc, $photo, $link ) {
		
			$data = json_decode(
						$this->execute(
							'wall.post',
							array(
								'owner_id' => $this->group_id,
								'from_group' => 1,
								'message' => $desc,
								'v' => '5.73',
								'attachments' => 'photo' . $this->group_id . '_' . $photo . ',' . $link
								
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response->post_id;
		}
		
	#Постинг на стену универсальный
	public function post_wall( $owner_id, $from_group, $message, $attachments ) {
		
			$data = json_decode(
						$this->execute(
							'wall.post',
							array(
								'owner_id' => $owner_id,
								'from_group' => $from_group,
								'message' => $message,
								'v' => '5.73',
								'attachments' => $attachments
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response->post_id;
		
	}
	
	#Репост со стены
	public function wall_repost( $object, $message, $group_id, $attachments ) {
		
			$data = json_decode(
						$this->execute(
							'wall.repost',
							array(
								'object' => $object,
								'message' => $message,
								'v' => '5.73',
								'group_id' => $group_id
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response->post_id;
		
	}
	
	#Получение юзера в сообществе
	public function groups_isMember($group_id, $user_id, $extended) {
		
			$data = json_decode(
						$this->execute(
							'groups.isMember',
							array(
								'group_id' => $group_id,
								'user_id' => $user_id,
								'extended' => $extended,
								'v' => '5.73',
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response;
		
	}
	
	#Являються пользователи друзьями
	public function friends_areFriends($user_ids, $need_sign) {
		
			$data = json_decode(
						$this->execute(
							'friends.areFriends',
							array(
								'user_ids' => $user_ids,
								'need_sign' => $need_sign,
								'v' => '5.73',
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response;
		
	}
	
	#Приглашение в сообщество
	public function groups_invite($group_id, $user_id) {
		
			$data = json_decode(
						$this->execute(
							'groups.invite',
							array(
								'group_id' => $group_id,
								'user_id' => $user_id
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}else{
				return $data->response;
			}
	}
		
	//отправка сообщения
	public function mess_send( $user_id, $message ) {
		
			$data = json_decode(
						$this->execute(
							'messages.send',
							array(
								'user_id' => $user_id,
								'message' => $message
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data->response;
		}
		
	//список друзей
	public function get_user( $user_id, $count, $offset, $fields) {
		
			$data = json_decode(
						$this->execute_no_token(
							'friends.get',
							array(
								'user_id' => $user_id,
								
								'fields' => $fields,
								'offset' =>  $offset,
								'count' => $count,
								
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data;
		}
		
	//получить информацию о пользователе
	public function users_get($user_id,$fields) {
		
			$data = json_decode(
						$this->execute(
							'users.get',
							array(
								'user_ids' => $user_id,
								'fields' => $fields
								
								
								
								
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data;
		}
	
	//пригласить друга
	public function friends_add($user_id, $text) {
		
			$data = json_decode(
						$this->execute(
							'friends.add',
							array(
								'user_id' => $user_id,
								'text'=>$text
								
								
								
								
							)
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
			return $data;
		}
	
	//создание альбома
	public function create_album( $name, $desc ) {
		$data = json_decode(
					$this->execute(
						'photos.createAlbum',
						array(
							'title' => $name,
							
							'description' => $desc,
							'comment_privacy' => 1,
							'privacy' => 1
						)
					)
				);
		if( isset( $data->error ) ) {
			return $this->error( $data );
		}
		return $data->response;
	}
	
	//получение кол-ва фотографий в альбоме
	public function get_album_size( $id ) {
		$data = json_decode(
					$this->execute(
						'photos.getAlbums',
						array(
							'oid' => -$this->group_id,
							'aids' => $id
						)
					)
				);
		if( isset( $data->error ) ) {
			return $data->error->error_code ;
		}
		return $data->response['0']->size;
	}
	
	//получить id записи состены последнюю
	public function wall_get_last_id( $owner_id ) {
		$data = json_decode(
					$this->execute(
						'wall.get',
						array(
							'owner_id' => $owner_id,
							'count' => 3,
							'offset' => 1
						)
					)
				);
		if( isset( $data->error ) ) {
			return $data->error->error_code ;
		}
		return $data->response['1']->id;
	}
	
	//получить репосты записи...
	public function wall_getrepost( $owner_id, $post_id, $count, $offset ) {
		$data = json_decode(
					$this->execute(
						'wall.getReposts',
						array(
							'owner_id' => $owner_id,
							'post_id' => $post_id,
							'count' => $count
						)
					)
				);
		if( isset( $data->error ) ) {
			return $data->error->error_code ;
		}
		return $data->response->profiles;
	}
	
	//загрузка фотографии
	public function upload_photo( $file, $album_id, $desc, $group_id ) {
		$params = array(
							'album_id' => $album_id,
							'v' => '5.73',
							'save_big' => 1
						);
		if($group_id) { $params['group_id'] = $group_id; }
		$data = json_decode(
					$this->execute(
						'photos.getUploadServer',
						$params
					)
				);
		if( isset( $data->error ) ) {
			return $this->error( $data );
		}
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $data->response->upload_url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, array( 'file1' => new CURLFile($file) ) );
		$result = json_decode(curl_exec($ch), true);
		
		if( isset( $result['error'] ) ) {
			return $result['error'];
		}
		if(!empty($result['photos_list'])){
			$params = array(
								'album_id' => $album_id,
								'server' => $result['server'],
								'photos_list' => $result['photos_list'],
								'hash' => $result['hash'],
								'v' => '5.73',
								'caption' => $desc
							);
			if($group_id) { $params['group_id'] = $group_id; }
			
			$data = json_decode(
						$this->execute(
							'photos.save',
							$params
						)
					);
			if( isset( $data->error ) ) {
				return $this->error( $data );
			}
		}
		return $data->response['0'];
	}
	
	private function execute( $method, $params ) {
		$ch = curl_init( 'https://api.vk.com/method/' . $method . '?access_token=' . $this->token );
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	private function execute_no_token( $method, $params ) {
		$ch = curl_init( 'https://api.vk.com/method/' . $method );
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	private function error( $data ) {
		//обработка ошибок
		return $data->error;
		return '['.$data->error->error_code.'] '.$data->error->error_msg.'<br/>';
	}
}
}