<?php
class controller_comments extends Controller
{
	function __construct()
	{
		$this->model_comments = new model_gorod_comments();
		$this->model_comments_pages = $this->model_comments->model_comments_pages();
		$this->model_comments_likes = $this->model_comments->model_comments_likes();
		$this->model_news = new model_gorod_news();
	}

	//Добавление комментария
	public function action_index()
	{
		if ($_POST['comment'])
		{
			$comment = trim(strip_tags(stripcslashes(htmlspecialchars($_POST['comment']))));

			if ($_POST['type'] == 1)
			{
				$com_for_table = $_POST['com_for_table'];
				$com_for_column = $_POST['com_for_column'];
				$com_main_id = $_POST['com_main_id'];
				$com_parent_id = 0;
			}
			else
			{
				$com_for_table = $_POST['com_for_table'];
				$com_for_column = $_POST['com_for_column'];
				$com_main_id = $_POST['com_main_id'];
				$com_parent_id = $_POST['com_parent_id'];
			}

			if (!$comment)
			{
				$errors[] = "Введите текст комментария";
			}

			$valid = $this->model_comments->getCountWhere("com_avtor = ".$_SESSION['user']['id']." AND com_main_id = ".$com_main_id." AND com_for_table = '".$com_for_table."' AND com_text = '".$comment."'");

			if ($valid > 0)
			{
				$errors[] = "Вы уже отправляли этот комментарий";
			}

			if (!$errors && $_SESSION['user']['id'] && $valid == 0)
			{
				$data = [
					"com_text" => $comment,
					"com_avtor" => $_SESSION['user']['id'],
					"com_date" => date("Y-m-d H:i:s"),
					"com_for_table" => $com_for_table,
					"com_for_column" => $com_for_column,
					"com_main_id" => $com_main_id,
					"com_parent_id" => $com_parent_id,
					"com_ip" => getIp(),
					"com_agent" => $_SERVER['HTTP_USER_AGENT'],
				];
				$this->model_comments->InsertUpdate($data);

				if ($_POST['page'] && $_POST['page'] == 'opinions')
				{
					echo json_encode(array('result' => "true", 'html' => $this->comments_opinions(array("com_for_table" => $com_for_table, "com_for_column" => $com_for_column, "com_main_id" => $com_main_id))));
				}
				else
				{
					echo json_encode(array('result' => "true", 'html' => $this->comments_rend(array("com_for_table" => $com_for_table, "com_for_column" => $com_for_column, "com_main_id" => $com_main_id))));
				}
			}
			else
			{
				foreach($errors as $error)
				{
					echo json_encode(array('result' => "false", 'error' => $error));
				}
			}
		}
	}

	//Лайки комментариев
	public function action_like()
	{
		if ($_POST)
		{
			$com_for_table = $_POST['com_for_table'];
			$com_for_column = $_POST['com_for_column'];
			$com_main_id = $_POST['com_main_id'];
			$com_parent_id = $_POST['com_parent_id'];
			$page = $_POST['page'];

			$comment = $this->model_comments->getItemWhere("com_id = '".$com_parent_id."' AND com_for_table = '".$com_for_table."' AND com_for_column = '".$com_for_column."' AND com_main_id = ".$com_main_id." AND com_status = 2");
			$like = $this->model_comments_likes->getCountWhere("like_id = ".$com_parent_id." AND like_uid = ".$_SESSION['user']['id']);

			if ($like > 0)
			{
				echo json_encode(array('result' => "false"));
			}
			else
			{
				$data = [
					"like_id" => $comment['com_id'],
					"like_uid" => $_SESSION['user']['id'],
					"like_date" => date("Y-m-d H:i:s"),
					"like_for_table" => $comment['com_for_table'],
					"like_for_column" => $comment['com_for_column'],
					"like_status" => $_POST['type'] == 'like' ? "1" : "-1",
					"like_ip" => getIp(),
				];
				$this->model_comments_likes->Insert($data);

				if ($page == "opinions")
				{
					echo json_encode(array('result' => "true", 'html' => $this->comments_opinions(array("com_for_table" => $com_for_table, "com_for_column" => $com_for_column, "com_main_id" => $com_main_id))));
				}
				else
				{
					echo json_encode(array('result' => "true", 'html' => $this->comments_rend(array("com_for_table" => $com_for_table, "com_for_column" => $com_for_column, "com_main_id" => $com_main_id))));
				}
			}
		}
	}

	//Кнопка "показать ещё"
	public function action_view_more()
	{
		if ($_POST)
		{
			$html = $this->comments_rend(array("com_for_table" => $_POST['com_for_table'], "com_for_column" => $_POST['com_for_column'], "com_main_id" => $_POST['com_main_id'], "start" => $_POST['count_show'], "limit" => $_POST['count_add']));

			if (!$html)
			{
				echo json_encode(array('result' => "false"));
			}
			else
			{
				echo json_encode(array('result' => "true", 'html' => $html));
			}
		}
	}

	//Кнопка "показать ещё" мнения
	public function action_view_more_opinions()
	{
		if ($_POST)
		{
			$html = $this->comments_opinions(array("start" => $_POST['count_show'], "limit" => $_POST['count_add']));

			if (!$html)
			{
				echo json_encode(array('result' => "false"));
			}
			else
			{
				echo json_encode(array('result' => "true", 'html' => $html));
			}
		}
	}

	public function comments_sum($id)
	{
		return $this->model_comments->getCountWhere("com_main_id = ".$id);
	}

	public function comments_opinions($attr)
	{
		$totalItems = $this->model_comments->getCountWhere("com_status = 2 AND com_parent_id = 0");
		$urlPattern = $GLOBALS['CONFIG']['HTTP_HOST'].'/opinions/?page=(:num)';
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$pagitanor = new Paginator($totalItems, $limit, $page, $urlPattern);

		$comments = $this->model_comments->get("*,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = 1) as `likes`,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = -1) as `dislikes`")
		               ->where("com_status = 2 AND com_parent_id = 0")->order("com_date DESC")->offset($offset)->limit($limit)->commit();

		$comment_html = '';

		foreach ($comments AS $comment)
		{

			$user = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `name` as `user_name`, `i_name`, `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $comment['com_avtor']);

			if ($comment['com_for_table'] == "gorod_news")
			{
				$our = $this->model_news->getItemWhere("id =".$comment['com_main_id'], "news_head, url");
				$controller = "news";
				$head = $our['news_head'];
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/'.$our['url'];
			}
			elseif ($comment['com_for_table'] == "webcam.chanels")
			{
				$our = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `cpu` as `url`, `name` FROM `webcam`.`chanels` WHERE id = ?i", $comment['com_main_id']);
				$controller = "webcam";
				$head = "WEB - камера: ".$our['name'];
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/'.$our['url'];
			}
			else
			{
				$controller = "";
				$head = "Мнения";
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/opinions';
			}

			$comment_html .= '
					<div class="comment" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'">
						<div class="left-col">
							<div class="ava-wrap">
								<img src="'.($user['ava_file'] ? $user['ava_file'] : 'http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png').'" alt="'.$user['i_name'].'">
							</div>
						</div>
						<div class="right-col">
							<div class="user-comment">
								<div class="head">
									<div class="name">'.($user['i_name'] ? $user['i_name'] : $user['user_name']).'</div>
									<time class="time" datetime="'.$comment['com_date'].'" title="'.$comment['com_date'].'"></time>
								</div>
								<div class="out">
									<a href="'.$url.'">'.$head.'</a>
								</div>
								<div class="content">
									<p>'.$comment['com_text'].'</p>
								</div>
								<div class="foot">
									<div class="review">
										<a href="#" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'" data-page="opinions">Ответить</a>
									</div>
									<div class="likes">
										<a href="#"><span class="icon-like" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'" data-page="opinions"></span></a><span class="text-like">'.$comment['likes'].'</span><a href="#"><span class="icon-dislike" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'" data-page="opinions"></span></a><span class="text-like">'.$comment['dislikes'].'</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				';

			$comment_review = $this->model_comments->get("*,
			(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = 1) as `likes`,
			(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = -1) as `dislikes`")
			                                       ->where("com_parent_id = '".$comment['com_id']."' AND com_status = 2")->order("com_date DESC")->commit();

			if ($comment_review)
			{
				foreach ($comment_review AS $comment)
				{

					$user = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `name` as `user_name`, `i_name`, `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $comment['com_avtor']);

					$comment_html .= '
						<div class="comment review" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'">
							<div class="left-col">
								<div class="ava-wrap">
									<img src="'.($user['ava_file'] ? $user['ava_file'] : "http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png").'" alt="'.$user['i_name'].'">
								</div>
							</div>
							<div class="right-col">
								<div class="user-comment">
									<div class="head">
										<div class="name">'.$user['i_name'].'</div>
										<time class="time" datetime="'.$comment['com_date'].'" title="'.$comment['com_date'].'"></time>
									</div>
									<div class="content">
										<p>'.$comment['com_text'].'</p>
									</div>
									<div class="foot">
										<div class="likes">
											<a href="#"><span class="icon-like" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'" data-page="opinions"></span></a><span class="text-like">'.$comment['likes'].'</span><a href="#"><span class="icon-dislike" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'" data-page="opinions"></span></a><span class="text-like">'.$comment['dislikes'].'</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					';
				}
			}
		}

		$user_ava = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);

		$comments_html = '
				<div class="comments-wrap-opinions">
					<div class="add-comment">
						<div class="add-form">
							<div class="left-col">
								<div class="ava-wrap">
									<img src="'.($user_ava['ava_file'] ? $user_ava['ava_file'] : "http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png").'" alt="alt">
								</div>
							</div>
							<div class="right-col">
								<div class="comment-area">
									<form method="POST" id="form-comments">
										<textarea placeholder="Оставить комментарий" name="comment"></textarea>
										<input type="hidden" name="type" value="1">
										<input type="hidden" name="com_for_table" class="com_for_table" value="gorod_opinions">
										<input type="hidden" name="com_for_column" class="com_for_column" value="id">
										<input type="hidden" name="com_main_id" class="com_main_id" value="0">
										<input type="hidden" name="page" class="page" value="opinions">
										<input type="hidden" name="url" class="url" value="'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">
										<button name="action" type="submit">Отправить</button>
									</form>
								</div>
							</div>
						</div>
						<div class="comments">
							'.$comment_html.'
						</div>
					</div>
					<div class="pagination-wrap">
                    	'.$pagitanor.'
                    </div>
				</div>
			';

		return $comments_html;
	}

	//Рендер комментариев
	public function comments_rend($attr)
	{
		if (!$attr['start'] && !$attr['limit'])
		{
			$start = 0;
			$limit = 20;
		}
		else
		{
			$start = $attr['start'];
			$limit = $attr['limit'];
		}

		$comments = $this->model_comments->get("*,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = 1) as `likes`,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = -1) as `dislikes`")
		                                 ->where("com_for_table = '".$attr['com_for_table']."' AND com_for_column = '".$attr['com_for_column']."' AND com_main_id = '".$attr['com_main_id']."' AND com_parent_id = 0 AND com_status = 2")->order("com_date DESC")->offset($start)->limit($limit)->commit();

		$comment_html = '';

		foreach ($comments AS $comment)
		{

			$user = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `name` as `user_name`, `i_name`, `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $comment['com_avtor']);

			$comment_html .= '
					<div class="comment" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'">
						<div class="left-col">
							<div class="ava-wrap">
								<img src="'.($user['ava_file'] ? $user['ava_file'] : "http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png").'" alt="'.$user['i_name'].'">
							</div>
						</div>
						<div class="right-col">
							<div class="user-comment">
								<div class="head">
									<div class="name">'.$user['i_name'].'</div>
									<time class="time" datetime="'.$comment['com_date'].'" title="'.$comment['com_date'].'"></time>
								</div>
								<div class="content">
									<p>'.$comment['com_text'].'</p>
								</div>
								<div class="foot">
									<div class="review">
										<a href="#" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'">Ответить</a>
									</div>
									<div class="likes">
										<a href="#"><span class="icon-like" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'"></span></a><span class="text-like">'.$comment['likes'].'</span><a href="#"><span class="icon-dislike" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'"></span></a><span class="text-like">'.$comment['dislikes'].'</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				';

			$comment_review = $this->model_comments->get("*,
			(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = 1) as `likes`,
			(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = -1) as `dislikes`")
			                                       ->where("com_for_table = '".$attr['com_for_table']."' AND com_for_column = '".$attr['com_for_column']."' AND com_main_id = '".$attr['com_main_id']."' AND com_parent_id = '".$comment['com_id']."' AND com_status = 2")->order("com_date DESC")->commit();

			if ($comment_review)
			{
				foreach ($comment_review AS $comment)
				{

					$user = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `name` as `user_name`, `i_name`, `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $comment['com_avtor']);

					$comment_html .= '
						<div class="comment review" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'">
							<div class="left-col">
								<div class="ava-wrap">
									<img src="'.($user['ava_file'] ? $user['ava_file'] : "http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png").'" alt="'.$user['i_name'].'">
								</div>
							</div>
							<div class="right-col">
								<div class="user-comment">
									<div class="head">
										<div class="name">'.$user['i_name'].'</div>
										<time class="time" datetime="'.$comment['com_date'].'" title="'.$comment['com_date'].'"></time>
									</div>
									<div class="content">
										<p>'.$comment['com_text'].'</p>
									</div>
									<div class="foot">
										<div class="likes">
											<a href="#"><span class="icon-like" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'"></span></a><span class="text-like">'.$comment['likes'].'</span><a href="#"><span class="icon-dislike" data-table="'.$comment['com_for_table'].'" data-column="'.$comment['com_for_column'].'" data-main-id="'.$comment['com_main_id'].'" data-comment-id="'.$comment['com_id'].'"></span></a><span class="text-like">'.$comment['dislikes'].'</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					';
				}
			}
		}

		$user_ava = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `ava_file` FROM `new_feo_ua`.`accounts` WHERE id = ?i", $_SESSION['user']['id']);

		$comments_html = '
				<div class="comments-wrap">
					<h2>Комментарии</h2>
					<div class="add-comment">
						<div class="left-col">
							<div class="ava-wrap">
								<img src="'.($user_ava['ava_file'] ? $user_ava['ava_file'] : "http://xn--e1asq.xn--p1ai/skin/media/images/no-ava.png").'" alt="alt">
							</div>
						</div>
						<div class="right-col">
							<div class="comment-area">
								<form method="POST" id="form-comments">
									<textarea placeholder="Оставить комментарий" name="comment"></textarea>
									<input type="hidden" name="type" value="1">
									<input type="hidden" name="com_for_table" class="com_for_table" value="'.$attr['com_for_table'].'">
									<input type="hidden" name="com_for_column" class="com_for_column" value="'.$attr['com_for_column'].'">
									<input type="hidden" name="com_main_id" class="com_main_id" value="'.$attr['com_main_id'].'">
									<input type="hidden" name="page" class="page" value="all">
									<input type="hidden" name="url" class="url" value="'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">
									<button name="action" type="submit">Отправить</button>
								</form>
							</div>
						</div>
						<div class="comments">
							'.$comment_html.'
						</div>
					</div>
					<input class="btn btn-primary" id="show_more" count_show="20" count_add="20" type="button" value="Показать еще">
				</div>
			';

		if (!$attr['start'] && !$attr['limit'])
		{
			return $comments_html;
		}
		else
		{
			return $comment_html;
		}
	}

	public function lastcomments_vidget()
	{
		$comments = $this->model_comments->get("*,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = 1) as `likes`,
		(SELECT COUNT(*) FROM `gorod24.online`.`gorod_comments_likes` WHERE like_id = `gorod24.online`.`gorod_comments`.`com_id` AND like_status = -1) as `dislikes`")
		                                 ->where("com_status = 2 AND com_parent_id = 0")->order("com_date DESC")->limit(3)->commit();

		$comment_html = '';

		foreach ($comments AS $comment)
		{
			if ($comment['com_for_table'] == "gorod_news")
			{
				$our = $this->model_news->getItemWhere("id =".$comment['com_main_id'], "news_head, url");
				$controller = "news";
				$head = $our['news_head'];
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/'.$our['url'];
			}
			elseif ($comment['com_for_table'] == "webcam.chanels")
			{
				//$our = $this->model_news->getItemWhere("id =".$comment['com_main_id'], "news_head, url");
				$our = $GLOBALS['DB']['80.93.183.242']->GetRow("SELECT `cpu` as `url`, `name` FROM `webcam`.`chanels` WHERE id = ?i", $comment['com_main_id']);
				$controller = "webcam";
				$head = "WEB - камера: ".$our['name'];
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/'.$our['url'];
			}
			else
			{
				$controller = "";
				$head = "Мнения";
				$url = $GLOBALS['CONFIG']['HTTP_HOST'].'/'.$controller.'/opinions';
			}

			$comment_html .= '
				<div class="item">
					<div class="item-title">
						<a href="'.$url.'"><h3>'.$head.'</h3></a>
					</div>
					<div class="item-content">
						<p>'.$comment['com_text'].'</p>
					</div>
					<div class="item-foot">
						<div class="time">
							<span class="icon-clock"></span>
							<time class="text-clock" datetime="'.$comment['com_date'].'" title="'.$comment['com_date'].'"></time>
						</div>
					</div>
				</div>
			';
		}

		$comments_html = '
				<div class="blue-box hidden-sm hidden-xs">
					<div class="blue-title">
						<h2>Последние комментарии</h2>
					</div>
					<div class="blue-content">
						'.$comment_html.'
					</div>
				</div>
			';
		return $comments_html;
	}

	public function action_comments_clear()
	{
		$GLOBALS['DB']['localhost']->query("DELETE FROM `gorod24.online`.`gorod_comments`");
	}

	public function action_comments_init()
	{
		$this->model_comments->getItemsWhere();
		$this->model_comments_likes->getItemsWhere();
	}

	//Парсер com_comments в gorod_comments
	public function action_comments_parse($params = array())
	{
		$page = $_GET['p'];
		if(!$_GET['p']) $page = 1;

		$limit = 200;
		$start = ($limit * $page) - $limit;

		$query = "
			SELECT *,
			    `com_pages`.`id` as `com_page_id`,
			    `com_comments`.`id` as `comment_id`
			  FROM `new_feo_ua`.`com_comments`, `new_feo_ua`.`com_pages` 
			  WHERE 
			    `com_comments`.`page_id`=`com_pages`.`id`
			  ORDER BY `date`, `time` LIMIT ".$start.",".$limit."
		";

		$comments = $GLOBALS['DB']['80.93.183.242']->getAll($query);
		$model_gorod_news= new model_gorod_news();

		if($comments)
		{
			$test = '';
			foreach($comments as $i => $new)
			{
				$item_routes = explode('/', $new['url']);
				$domain = $item_routes[2];
				$contoller = $item_routes[3];
				$action = $item_routes[4];

				$test .= '<p>Домен: '.$domain.' | Контроллер: '.$contoller.' | Экшен: '.$action.'</p><br>';

				if (in_array($contoller, array("news", "%D0%BD%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8", "новости")))
				{
					$url = explode("-", $action);
					$news_id=substr($url[0], 0, strlen($url[0])-1);
					$our=substr($url[0], -1);
					if(is_numeric($our))
					{
						$id = $news_id;
					}
					else
					{
						$our = ($our == 'o' ? 1 : 0);
						$gorod_new = $model_gorod_news->getItemWhere("`news_id`='{$news_id}' and `our`='{$our}'");
						$id = $gorod_new['id'];
					}

					$date = $new['date']." ".$new['time'];

					//Парсим комментарии
					if ($id == 0 || $new['on_off'] == 0)
					{
						$this->model_comments->Insert([
							'com_text' => $new['text'],
							'com_avtor' => $new['uid'],
							'com_date' => $date,
							'com_for_table' => "gorod_news",
							'com_for_column' => "id",
							'com_main_id' => $id,
							'com_parent_id' => $new['pid'],
							'com_status' => "0",
							'com_ip' => $new['ip'],
							'com_old_id' => $new['comment_id'],
							'com_gazeta_user_id' => $new['gazeta_user_id'],
							'com_gazeta_sub_key' => $new['gazeta_sub_key'],
							'com_user_alias' => $new['user_alias']
						]);
					}
					else
					{
						if ($new['pid'] == 0)
						{
							$this->model_comments->Insert([
								'com_text' => $new['text'],
								'com_avtor' => $new['uid'],
								'com_date' => $date,
								'com_for_table' => "gorod_news",
								'com_for_column' => "id",
								'com_main_id' => $id,
								'com_parent_id' => "0",
								'com_status' => "2",
								'com_ip' => $new['ip'],
								'com_old_id' => $new['comment_id'],
								'com_gazeta_user_id' => $new['gazeta_user_id'],
								'com_gazeta_sub_key' => $new['gazeta_sub_key'],
								'com_user_alias' => $new['user_alias']
							]);
						}
						else
						{
							$this->model_comments->Insert([
								'com_text' => $new['text'],
								'com_avtor' => $new['uid'],
								'com_date' => $date,
								'com_for_table' => "gorod_news",
								'com_for_column' => "id",
								'com_main_id' => $id,
								'com_parent_id' => $new['pid'],
								'com_status' => "2",
								'com_ip' => $new['ip'],
								'com_old_id' => $new['comment_id'],
								'com_gazeta_user_id' => $new['gazeta_user_id'],
								'com_gazeta_sub_key' => $new['gazeta_sub_key'],
								'com_user_alias' => $new['user_alias']
							]);
						}
					}

				}
				elseif (in_array($contoller, array("webcam", "%D0%B2%D0%B5%D0%B1_%D0%BA%D0%B0%D0%BC%D0%B5%D1%80%D1%8B", "веб_камеры")))
				{
					if ($new['page_id'] <= 23 && $new['on_off'] != 0)
					{
						$this->model_comments->Insert([
							'com_text' => $new['text'],
							'com_avtor' => $new['uid'],
							'com_date' => $new['date']." ".$new['time'],
							'com_for_table' => "webcam.chanels",
							'com_for_column' => "id",
							'com_main_id' => $new['page_id'],
							'com_parent_id' => $new['pid'],
							'com_status' => "2",
							'com_ip' => $new['ip'],
							'com_old_id' => $new['comment_id'],
							'com_gazeta_user_id' => $new['gazeta_user_id'],
							'com_gazeta_sub_key' => $new['gazeta_sub_key'],
							'com_user_alias' => $new['user_alias']
						]);
					}
				}
			}
			echo $test;
			$next = $page + 1;
			header("Content-type: text/html; charset=UTF-8");
			echo "{$page}<script type='text/javascript'>document.location.href = 'https://gsp1.feomedia.ru/comments/comments.parse?p={$next}';</script>";
		}
	}

	public function action_comments_parse_answer($params = array())
	{
		$page = $_GET['p'];
		if(!$_GET['p']) $page = 1;

		$limit = 200;
		$start = ($limit * $page) - $limit;

		$model = new model_gorod_comments();

		$comments = $model->getItemsWhere("com_parent_id != 0 AND com_old_id != 0", "com_id", $start, $limit, "com_parent_id");


		if($comments)
		{
			foreach ($comments AS $item)
			{
				$a = $model->getItemWhere("com_old_id = ".$item['com_parent_id'], "com_id");
				//$a = "SELECT `com_id` FROM `gorod24.online`.`gorod_comments` WHERE `com_old_id` = ".$item['com_parent_id'];

				$GLOBALS['DB']['localhost']->query("UPDATE `gorod24.online`.`gorod_comments` SET `com_parent_id` = ?i WHERE `com_parent_id` = ?i", $a['com_id'], $item['com_parent_id']);
			}

			$next = $page + 1;
			header("Content-type: text/html; charset=UTF-8");
			echo "{$page}<script type='text/javascript'>document.location.href = 'https://gsp1.feomedia.ru/comments/comments.parse.answer?p={$next}';</script>";
		}
	}
}