<?php

class Controller_ajax extends Controller
{
	
	function action_index($array = array())
	{
		$action = $this->POST['ajax_action'];
		$action = (!empty($action))?$action:$_REQUEST['ajax_action'];
		if (method_exists($this, $action)){
			$this->$action();
			exit;
		} 
		elseif(has_action('ajax_'.$action)) {
			echo do_action('ajax_'.$action, $this->POST);
			exit;
		}
		else {
			echo "ERROR -".$action;
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
		if(!is_dir('uploads')) mkdir('uploads');
		$filePath ="uploads/$fileName";
		 
		 
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
		}
		 
		die('{"OK": 1, "info": "Upload successful."}');
	}

	function action_get_phone ()
	{

		if ($_POST['id'])
		{
			$this->model_adv = new model_adventures();
			$result = $this->model_adv->getItemWhere("id = ".$_POST['id'], "user_phone");

			$part1 = substr($result['user_phone'],0, 2);
			$part2 = substr($result['user_phone'],2, 3);
			$part3 = substr($result['user_phone'],5, 3);
			$part4 = substr($result['user_phone'],8, 2);
			$part5 = substr($result['user_phone'],10, 2);

			$phone = $part1." (".$part2.") ".$part3."-".$part4."-".$part5;

			echo json_encode(array('result' => "true", 'html' => $phone));
		}
	}

	function action_get_subcats ()
	{

		if ($_POST['id'])
		{
			$subcats = $GLOBALS['DB']['80.93.183.242']->getAll("SELECT `id`, `pid`, `name`, `cpu`, (SELECT COUNT(*) FROM `new_feo_ua`.`adv_adventures` WHERE `new_feo_ua`.`adv_adventures`.`sub_catid` = `new_feo_ua`.`adv_sub_category`.`id`) as `count` FROM `new_feo_ua`.`adv_sub_category` WHERE `new_feo_ua`.`adv_sub_category`.`pid` = ".$_POST['id']." AND `new_feo_ua`.`adv_sub_category`.`on_off` = 2 ORDER BY `pos`");

			$option = '<option value="">Выбрать категорию</option>';

			foreach ($subcats as $item)
			{
				$option .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
			}

			echo json_encode(array('result' => "true", 'html' => $option));
		}
	}

	function action_get_options ()
	{

		if ($_POST['main_id'] && $_POST['sub_id'])
		{
			$this->model_adv = new model_adventures();
			$options = $this->model_adv->getOptions($_POST['main_id'], $_POST['sub_id']);

			$options_html = '';

			foreach ($options as $item)
			{
				$req = $item['is_req'] == 1 ? "required" : "";
				$req_w = $item['is_req'] == 1 ? "requared-input" : "";

				switch ($item['type'])
				{
					//Выпадалка текст
					case 0:

						$option_items = '<option value="">'.$item['name'].'</option>';

						foreach ($item['items'] as $itm)
						{
							$option_items .= '<option value="'.$itm['id'].'">'.$itm['name'].'</option>';
						}

						$options_html .= '
							
							<div class="input-wrap '.$req_w.'" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
                        		<span class="icon-wrap"><span class="icon-settings"></span></span>
                                <select id="'.$item['id'].'" class="select-filter dop-filters dop-option" name="opt['.$item['id'].']" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'" '.$req.'>
                                    '.$option_items.'
                                </select>
                            </div>
	
						';
						break;

					//Выпадалка числа
					case 1:

						$option_items = '<option value="">'.$item['name'].'</option>';

						foreach ($item['items'] as $itm)
						{
							$option_items .= '<option value="'.$itm['id'].'">'.$itm['name'].'</option>';
						}

						$options_html .= '
							
							<div class="input-wrap '.$req_w.'" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
                        		<span class="icon-wrap"><span class="icon-settings"></span></span>
                                <select id="'.$item['id'].'" class="select-filter dop-filters dop-option" name="opt['.$item['id'].']" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'" '.$req.'>
                                    '.$option_items.'
                                </select>
                            </div>
	
						';
						break;

					//Текстовое поле
					case 2:

						$options_html .= '
							
							<div class="input-wrap '.$req_w.'" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
								<label>
	                                <span class="icon-wrap"><span class="icon-settings"></span></span>
	                                <input id="'.$item['id'].'" class="dop-option" type="text" name="opt['.$item['id'].']" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'" '.$req.'>
                                </label>
                            </div>
	
						';
						break;

					//Числовое поле
					case 3:

						$options_html .= '
							
							<div class="input-wrap '.$req_w.'" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
								<label>
	                                <span class="icon-wrap"><span class="icon-settings"></span></span>
	                                <input id="'.$item['id'].'" class="dop-option" type="number" name="opt['.$item['id'].']" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'" '.$req.'>
                                </label>
                                <span class="unit">'.$item['unit'].'</span>
                            </div>
	
						';
						break;

					//Чекбоксы
					case 4:

						$options_html .= '
							
							<div class="input-wrap" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
                                <label class="check-filter">
                                    <span class="info-lalbel">'.$item['name'].'</span>
                                    <input type="checkbox" class="dop-option" name="opt['.$item['id'].']" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'">
                                </label>
                            </div>
	
						';
						break;

					case 5:

						$options_html .= '
							
							<script type="text/javascript">
								var geopos;
								var myMap;
							
								ymaps.ready(init);
								
								$("#445").on("focusout", function() {
									
									function newmapinit() {
									    geopos = $("#445").val();
										myMap.destroy();
										ymaps.ready(init);
									}
									
									setTimeout(newmapinit, 1000);
								});

								function init() {
								    myMap = new ymaps.Map(\'map\', {
								        center: [55.753994, 37.622093],
								        zoom: 10
								    });
								
								    // Поиск координат центра Нижнего Новгорода.
								    ymaps.geocode(geopos, {
								        /**
								         * Опции запроса
								         * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode.xml
								         */
								        // Сортировка результатов от центра окна карты.
								        // boundedBy: myMap.getBounds(),
								        // strictBounds: true,
								        // Вместе с опцией boundedBy будет искать строго внутри области, указанной в boundedBy.
								        // Если нужен только один результат, экономим трафик пользователей.
								        results: 1
								    }).then(function (res) {
								            // Выбираем первый результат геокодирования.
								            var firstGeoObject = res.geoObjects.get(0),
								                // Координаты геообъекта.
								                coords = firstGeoObject.geometry.getCoordinates(),
								                // Область видимости геообъекта.
								                bounds = firstGeoObject.properties.get(\'boundedBy\');
								                
								                $("#map-longitude").val(coords[1]);
								                $("#map-latitude").val(coords[0]);
								
								            firstGeoObject.options.set(\'preset\', \'islands#darkBlueDotIconWithCaption\');
								            // Получаем строку с адресом и выводим в иконке геообъекта.
								            firstGeoObject.properties.set(\'iconCaption\', firstGeoObject.getAddressLine());
								
								            // Добавляем первый найденный геообъект на карту.
								            myMap.geoObjects.add(firstGeoObject);
								            // Масштабируем карту на область видимости геообъекта.
								            myMap.setBounds(bounds, {
								                // Проверяем наличие тайлов на данном масштабе.
								                checkZoomRange: true
								            });
								
								            /**
								             * Все данные в виде javascript-объекта.
								             */
								            console.log(\'Все данные геообъекта: \', firstGeoObject.properties.getAll());
								            /**
								             * Метаданные запроса и ответа геокодера.
								             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/GeocoderResponseMetaData.xml
								             */
								            console.log(\'Метаданные ответа геокодера: \', res.metaData);
								            /**
								             * Метаданные геокодера, возвращаемые для найденного объекта.
								             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/GeocoderMetaData.xml
								             */
								            console.log(\'Метаданные геокодера: \', firstGeoObject.properties.get(\'metaDataProperty.GeocoderMetaData\'));
								            /**
								             * Точность ответа (precision) возвращается только для домов.
								             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/precision.xml
								             */
								            console.log(\'precision\', firstGeoObject.properties.get(\'metaDataProperty.GeocoderMetaData.precision\'));
								            /**
								             * Тип найденного объекта (kind).
								             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
								             */
								            console.log(\'Тип геообъекта: %s\', firstGeoObject.properties.get(\'metaDataProperty.GeocoderMetaData.kind\'));
								            console.log(\'Название объекта: %s\', firstGeoObject.properties.get(\'name\'));
								            console.log(\'Описание объекта: %s\', firstGeoObject.properties.get(\'description\'));
								            console.log(\'Полное описание объекта: %s\', firstGeoObject.properties.get(\'text\'));
								            /**
								            * Прямые методы для работы с результатами геокодирования.
								            * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/GeocodeResult-docpage/#getAddressLine
								            */
								            console.log(\'\nГосударство: %s\', firstGeoObject.getCountry());
								            console.log(\'Населенный пункт: %s\', firstGeoObject.getLocalities().join(\', \'));
								            console.log(\'Адрес объекта: %s\', firstGeoObject.getAddressLine());
								            console.log(\'Наименование здания: %s\', firstGeoObject.getPremise() || \'-\');
								            console.log(\'Номер здания: %s\', firstGeoObject.getPremiseNumber() || \'-\');
								
								            /**
								             * Если нужно добавить по найденным геокодером координатам метку со своими стилями и контентом балуна, создаем новую метку по координатам найденной и добавляем ее на карту вместо найденной.
								             */
								            /**
								             var myPlacemark = new ymaps.Placemark(coords, {
								             iconContent: \'моя метка\',
								             balloonContent: \'Содержимое балуна <strong>моей метки</strong>\'
								             }, {
								             preset: \'islands#violetStretchyIcon\'
								             });
								
								             myMap.geoObjects.add(myPlacemark);
								             */
								        });
								}
							</script>
							
							<div class="input-wrap" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
								<label>
	                                <span class="icon-wrap"><span class="icon-settings"></span></span>
	                                <input id="'.$item['id'].'" class="dop-option" type="text" name="opt['.$item['id'].']" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'">
	                                <input type="hidden" name="map-longitude" class="map-longitude-'.$item['id'].'" id="map-longitude">
	                                <input type="hidden" name="map-latitude" class="map-latitude-'.$item['id'].'" id="map-latitude">
                                </label>
                            </div>
                            
                            <div class="input-wrap">
                            	<div id="map" style="width: 100%; height: 300px;"></div>
                            </div>
	
						';
						break;

					case 7:

						$options_html .= '

							<script type="text/javascript">
								$(\'#date_range\').datepicker({
									dateFormat: \'yy-mm-dd\',
								    range: \'period\', // режим - выбор периода
								    numberOfMonths: 2,
								    onSelect: function(dateText, inst, extensionRange) {
								        // extensionRange - объект расширения
								      $(\'[name=startDate]\').val(extensionRange.startDateText);
								      $(\'[name=endDate]\').val(extensionRange.endDateText);
								    }
								});
								
								//$(\'#date_range\').datepicker(\'setDate\', [\'+4d\', \'+8d\']);
								
								// объект расширения (хранит состояние календаря)
								var extensionRange = $(\'#date_range\').datepicker(\'widget\').data(\'datepickerExtensionRange\');
								if(extensionRange.startDateText) $(\'[name=startDate]\').val(extensionRange.startDateText);
								if(extensionRange.endDateText) $(\'[name=endDate]\').val(extensionRange.endDateText);
							</script>
							
							<div class="input-wrap" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
								<div id="date_range"></div>
								<input name="startDate">
								<input name="endDate">
							</div>
						
						';
						break;

					case 8:

						$options_html .= '

							<script type="text/javascript">
								$(\'.datepicker\').datepicker({
									dateFormat: \'yy-mm-dd\',
								    range: \'multiple\', // режим - выбор нескольких дат 
								    range_multiple_max: \'60\', // макимальное число выбираемых дат,
								    onSelect: function(dateText, inst, extensionRange) {
								      // extensionRange - объект расширения
								      $(\'#'.$item['id'].'\').val(extensionRange.datesText.join(\',\'));
								    }
								  });
								
								  // выделить послезавтра и следующие 2 дня
								  //$(\'#date_range\').datepicker(\'setDate\', [\'+2d\', \'+3d\', \'+4d\']);
								
								  // объект расширения (хранит состояние календаря)
								  var extensionRange = $(\'.datepicker\').datepicker(\'widget\').data(\'datepickerExtensionRange\');
								  //if (extensionRange.datesText) $(\'#'.$item['id'].'\').val(extensionRange.datesText.join(\',\'));
							</script>
							
							<div class="input-wrap" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
								<div class="datepicker"></div>
								<input name="opt['.$item['id'].']" class="dop-option" id="'.$item['id'].'" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'">
							</div>
						
						';
						break;

					case 9:

						$options_html .= '
							
							<div class="input-wrap" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
                                <label class="check-filter">
                                    <span class="info-lalbel">'.$item['name'].'</span>
                                    <input type="radio" name="opt['.$item['id'].']" class="dop-option" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'">
                                </label>
                            </div>
	
						';
						break;

					case 10:

						$options_html .= '
							
							<div class="input-wrap input-wrap-big" data-show-optid="'.$item['show_optid'].'" data-show-optval="'.$item['show_optval'].'" '.($item['show_optid'] != 0 ? "hidden" : "").'>
                                <label>
                                    <span class="icon-wrap"><span class="icon-note"></span></span>
                                    <textarea name="opt['.$item['id'].']" class="dop-option" data-id="'.$item['id'].'" data-label="'.$item['name'].'" data-type="'.$item['type'].'" placeholder="'.$item['name'].'" style="width: 100%"></textarea>
                                </label>
                            </div>
	
						';
						break;
				}
			}

			echo json_encode(array('result' => "true", 'html' => $options_html));
		}
	}

	function action_adv_add ()
	{
		$this->model_adv = new model_adventures();
		$this->model_gazeta = new model_gazeta();

		$regions = [
			"261"=>"Крым"
		];
		$city = [
			"478"=>"Керчь",
			"1483"=>"Феодосия" ,
			"1500537"=>"Береговое",
			"1500545"=>"Приморский",
			"1500539"=>"Коктебель",
		];

		if ($_POST['page'] == 1)
		{
			$adv_data_step1 = array(
				'id'           		=> null,
				'user_id'           => $_SESSION['user']['id'],
				'user_name'         => $_POST['name'],
				'user_email'        => $_POST['email'],
				'user_email_show'   => $_POST['email_show'],
				'user_phone'        => $_POST['phone'],
				'user_ip'           => getIp(),
				'city'              => $city[$_POST['city']],
				'region'            => $regions[$_POST['region']],
				'city_id'           => $_POST['city'],
				'region_id'         => $_POST['region'],
				'add_time'          => time(),
				'up_time'           => time(),
				'edit_time'         => time(),
				'up_time_send'      => time(),
				'on_off'            => -1,
			);

			$_SESSION['ads']['adv_id'] = $this->model_adv->InsertUpdate($adv_data_step1);
		}

		if ($_POST['adv_id'] && $_POST['page'] == 2)
		{
			$adv_data_step2 = array(
				'id'           		=> $_POST['adv_id'],
				'main_catid'        => $_POST['cat'],
				'sub_catid'         => $_POST['sub_cat'],
				'caption'           => $_POST['title'],
				'descr'             => $_POST['descr'],
				'price'             => $_POST['price'],
				'price_from_to'     => (!empty($_POST['price_from_to'])?$_POST['price_from_to']:0),
				'price_ed_izm'      => (!empty($_POST['price_izm'])?$_POST['price_izm']:0),
				'price_valut'       => 'руб.',
				'price_discuse'     => (!empty($_POST['price_discuse'])?1:0),
				'price_free'        => (!empty($_POST['price_free'])?1:0),
				'url'               => $_POST['site'],
				'video'             => $_POST['video'],
				'edit_time'         => time(),
				'json_options'      => json_encode($_POST['options'], JSON_UNESCAPED_UNICODE),
				'json_data'         => json_encode($_POST, JSON_UNESCAPED_UNICODE),
				'on_off'            => -1,
				'latitude'			=> (!empty($_POST['map_latitude'])?$_POST['map_latitude']:'NULL'),
				'longitude'			=> (!empty($_POST['map_longitude'])?$_POST['map_longitude']:'NULL'),
			);

			$this->model_adv->InsertUpdate($adv_data_step2);

			//Выбор доступных номеров газет
			/*$adv_nums = $this->model_gazeta->getAdvNums($_POST['adv_id']);
			$adv_nums_publ = $this->model_gazeta->getAdvNums($_POST['adv_id'],true);

			if (count($adv_nums) + count($adv_nums_publ))
			{
				// Есть новера - берем ближайшие
				$active_gazetas = $this->model_gazeta->getGazetas(GAZETA_USER_MAX_NUMBERS);

				if (count($active_gazetas))
				{
					$add_nums = array();

					foreach ($active_gazetas as $k => &$gaz_data)
					{
						if (count($gaz_data['nums']))
						{
							foreach ($gaz_data['nums'] as $kk => &$num_data)
							{
								$add_nums[] = array(
									'id'    => $num_data['id'],
									'pid'   => $num_data['pid']
								);
							}
						}
					}
					if (count($add_nums))
					{
						$this->model_gazeta->addAdv($_POST['adv_id'], $add_nums, false);
					}
				}
			}*/
		}

		if ($_POST['adv_id'] && $_POST['page'] == 3)
		{
			$adv_data_step3 = array(
				'id'           		=> $_POST['adv_id'],
				'adv_text'          => (!empty($_POST['gazeta_text']) ? $_POST['gazeta_text'] : ''),
				'edit_time'         => time(),
				'on_off'            => -1,
			);

			$this->model_adv->InsertUpdate($adv_data_step3);
		}

		echo json_encode(array('result' => "true", /*'gazeta' => $add_nums*/));
	}

}
