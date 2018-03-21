<?php
class controller_interviews extends Controller
{
	function __construct()
	{

	}

	public function action_index()
	{

	}

	public function action_send_inter()
	{

		if (!$_SESSION['user']['id'])
		{
			echo json_encode(array('result' => "false"));
		}
		else
		{
			$sending = $_POST['sending'];

			$GLOBALS['DB']['80.93.183.242']->query('DELETE FROM `new_feo_ua`.`feo_interviews_votes` WHERE vote_uid = ?i AND vote_inter_id = ?i', $_SESSION['user']['id'], $sending['option_inter_id']);

			if (is_array($sending['option_id']))
			{
				foreach ($sending['option_id'] as $i=> $option_id)
				{
					$GLOBALS['DB']['80.93.183.242']->query('INSERT INTO `new_feo_ua`.`feo_interviews_votes` SET vote_date = NOW(), vote_uid = ?i, vote_inter_id = ?i, vote_option_id = ?i, vote_ip = ?s, vote_agent = ?s', $_SESSION['user']['id'],
						$sending['option_inter_id'], $option_id, getIp(), $_SERVER['HTTP_USER_AGENT']);

					$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `new_feo_ua`.`feo_interviews_votes_actions` SET action_date = NOW(), action_uid = ?i, action_name = 'add_vote', action_inter_id = ?i, action_option_id = ?i, action_ip = ?s, action_agent = ?s", $_SESSION['user']['id'],
						$sending['option_inter_id'], $option_id, getIp(), $_SERVER['HTTP_USER_AGENT']);
				}
			}
			else
			{
				$GLOBALS['DB']['80.93.183.242']->query('INSERT INTO `new_feo_ua`.`feo_interviews_votes` SET vote_date = NOW(), vote_uid = ?i, vote_inter_id = ?i, vote_option_id = ?i, vote_ip = ?s, vote_agent = ?s', $_SESSION['user']['id'],
					$sending['option_inter_id'], $sending['option_id'], getIp(), $_SERVER['HTTP_USER_AGENT']);

				$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `new_feo_ua`.`feo_interviews_votes_actions` SET action_date = NOW(), action_uid = ?i, action_name = 'add_vote', action_inter_id = ?i, action_option_id = ?i, action_ip = ?s, action_agent = ?s", $_SESSION['user']['id'],
					$sending['option_inter_id'], $sending['option_id'], getIp(), $_SERVER['HTTP_USER_AGENT']);
			}

			$interview = $GLOBALS['DB']['80.93.183.242']->GetRow('SELECT * FROM `new_feo_ua`.`feo_interviews` WHERE inter_status = 1 AND inter_id = ?i', $sending['option_inter_id']);

			echo json_encode(array('result' => "true", 'html' => $this->interviews_vidget($interview)));
		}
	}

	public function action_del_inter()
	{

		if (!$_SESSION['user']['id'])
		{
			echo json_encode(array('result' => "false"));
		}
		else
		{
			$sending = $_POST['sending'];

			$options = $GLOBALS['DB']['80.93.183.242']->GetCol('SELECT vote_option_id FROM `new_feo_ua`.`feo_interviews_votes` WHERE vote_uid = ?i AND vote_inter_id = ?i', $_SESSION['user']['id'], $sending['option_inter_id']);

			$GLOBALS['DB']['80.93.183.242']->query('DELETE FROM `new_feo_ua`.`feo_interviews_votes` WHERE vote_uid = ?i AND vote_inter_id = ?i', $_SESSION['user']['id'], $sending['option_inter_id']);

			$GLOBALS['DB']['80.93.183.242']->query("INSERT INTO `new_feo_ua`.`feo_interviews_votes_actions` SET action_date = NOW(), action_uid = ?i, action_name = 'delete_vote', action_inter_id = ?i, action_option_id = ?s, action_ip = ?s, action_agent = ?s", $_SESSION['user']['id'],
				$sending['option_inter_id'], implode(',', $options), getIp(), $_SERVER['HTTP_USER_AGENT']);

			$interview = $GLOBALS['DB']['80.93.183.242']->GetRow('SELECT * FROM `new_feo_ua`.`feo_interviews` WHERE inter_status = 1 AND inter_id = ?i', $sending['option_inter_id']);

			echo json_encode(array('result' => "true", 'html' => $this->interviews_vidget($interview)));
		}
	}

	public function get_interview_vidget($id)
	{
		$class = array("class" => "default");
		$interview = $GLOBALS['DB']['80.93.183.242']->GetRow('SELECT * FROM `new_feo_ua`.`feo_interviews` WHERE inter_status = 1 AND inter_id = ?i', $id);
		return $this->interviews_vidget(array_merge($interview, $class));
	}

	public function get_interview_vidget_rand()
	{
		$class = array("class" => "interview-content");
		$interview = $GLOBALS['DB']['80.93.183.242']->GetRow('SELECT * FROM `new_feo_ua`.`feo_interviews` WHERE inter_status = 1 ORDER BY RAND() LIMIT 1');
		return $this->interviews_vidget(array_merge($interview, $class));
	}

	public function interviews_vidget($interview)
	{

		$result = '
			<div class="blue-box">
				<div class="blue-title">
					<h2>Опрос</h2>
				</div>
				<div class="blue-content '.$interview['class'].'">
					<div class="user-survey-wrap">
						<div class="content">
		';

		$result .= '<span class="title" data-poll-id="'.$interview['inter_id'].'">'.$interview['inter_text'].'</span>';

		$my_vote = $GLOBALS['DB']['80.93.183.242']->GetAll('SELECT `vote_option_id` FROM `new_feo_ua`.`feo_interviews_votes` WHERE `feo_interviews_votes`.`vote_inter_id` = ?i AND `feo_interviews_votes`.`vote_uid` = ?i', $interview['inter_id'], $_SESSION['user']['id']);

		$options = $GLOBALS['DB']['80.93.183.242']->GetAll('SELECT 
			*, 
			(SELECT COUNT(DISTINCT vote_uid) FROM `new_feo_ua`.`feo_interviews_votes` WHERE `feo_interviews_votes`.`vote_inter_id` = `feo_interviews_options`.`option_inter_id`) as `votes`,
			(SELECT COUNT(*) FROM `new_feo_ua`.`feo_interviews_votes` WHERE `feo_interviews_votes`.`vote_inter_id` = `feo_interviews_options`.`option_inter_id` AND `feo_interviews_votes`.`vote_option_id` = `feo_interviews_options`.`option_id`) as `votes_option`
		FROM `new_feo_ua`.`feo_interviews_options` WHERE `option_status` = 1 AND `option_inter_id` = ?i ORDER BY `option_position`', $interview['inter_id']);

		if (!$my_vote)
		{
			$result .= $this->get_options_not_voted($options, $interview);
		}
		else
		{
			$result .= $this->get_options_voted($options, $interview, $my_vote);
		}

		$result .= '	</div>
					</div>
				</div>
			</div>';

		return $result;
	}

	public function get_options_not_voted($options, $interview)
	{

		$result = '';

		if ($interview['inter_max_votes'] > 1)
		{
			foreach($options as $i => $option)
			{
				$result .= '
					<div class="surv-item">
						<div class="inter-option">
							<input class="inter_'.$option['option_inter_id'].'" type="checkbox" id="option_'.$option['option_id'].'" value="'.$option['option_id'].'" name="inter-option">
							<label for="option_'.$option['option_id'].'">'.$option['option_text'].'</label>
						</div>
					</div>
				';
			}

			$result .= '
				<div class="foot">
					<a href="#" id="send_inters" data-max-votes="'.$interview['inter_max_votes'].'" data-inter-id="'.$option['option_inter_id'].'">Голосовать</a>
					<a href="#">Комментарии</a>
				</div>
			';
		}
		elseif ($interview['inter_max_votes'] == 1)
		{
			foreach ($options as $i => $option)
			{
				$result .= '
					<div class="surv-item">
						<div class="inter-option">
							<input type="radio" id="option_'.$option['option_id'].'" name="inter-option" value="'.$option['option_id'].'">
							<label for="option_'.$option['option_id'].'">'.$option['option_text'].'</label>
						</div>
					</div>
				';
			}

			$result .= '
				<div class="foot">
					<a href="#" id="send_inter" data-option-id="'.$option['option_id'].'" data-inter-id="'.$option['option_inter_id'].'">Голосовать</a>
					<a href="#">Комментарии</a>
				</div>
			';
		}

		return $result;
	}

	public function get_options_voted($options, $interview, $my_vote)
	{

		$result = '';

		foreach ($options as $i => $option)
		{
			$v = $option['votes'];
			$vo = $option['votes_option'];
			$percent = ($vo / $v) * 100;
			$checked = '';

			foreach ($my_vote AS $my_vote_id)
			{
				if ($my_vote_id['vote_option_id'] == $option['option_id'])
				{
					$checked = '<span style="color: limegreen;"><i class="fa fa-check" aria-hidden="true"></i></span>';
				}
			}

			$result .= '
				<div class="surv-item">
					<span class="text">'.$option['option_text'].' <span>('.$vo.') </span>'.$checked.'</span>
					<div class="value"><b>'.round($percent,1).'%</b></div>
					<div class="line" style="width: '.round($percent, 0).'%"></div>
				</div>
			';
		}

		$result .= '
			<div class="foot">
				<a href="#" id="delete_inters" class="link right-link" data-inter-id="'.$option['option_inter_id'].'">Я передумал</a>
				<a href="#">Комментарии</a>
			</div>
		';

		return $result;
	}
}