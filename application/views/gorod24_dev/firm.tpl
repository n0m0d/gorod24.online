<main>
	<div class="container main-firm">
		<div class="main-banners">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-wrap">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-2.jpg" alt="alt">
						</a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="banner-wrap">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-3.jpg" alt="alt">
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="title-block">
			<div class="row">
				<div class="col-md-12 col-sm-8">
					<h2><?=$this->data['getFirm'][0]['name_kat'];?></h2>
				</div>
			</div>
		</div>
		<section class="section-1">
			<div class="row">
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<aside>
						<div class="button-wrap">
							<a href="#" class="button">
								<span class="icon-pencil"></span>
								<span class="button-text">Добавить объявление</span>
							</a>
						</div>
						<div class="ads-cat-block">
							<h2>Рубрики каталога</h2>
							<ul class="ads-ul-block">
								<?php foreach ((array) $this->data['main_otr'] as $item): ?>
								<li class="ads-li">
									<p style="font-size: 12px; font-weight: bold;"><?=$item['name_main']?></p>
									<ul class="ads-ul-block-inner">
										<?php foreach ((array) $this->data['list_otr'][$item['id_main']] as $sub_item): ?>

										<?php if ($sub_item['sub_count'] != 0): ?>
										<li class="ads-li-inner"><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$sub_item['url']?>"><?=$sub_item['name']?> (<?=$sub_item['sub_count']?>)</a></li>
										<?php endif; ?>

										<?php endforeach; ?>
									</ul>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div class="vertical-banners">
							<div class="banner-wrap">
								<a href="#">
									<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
								</a>
							</div>
						</div>
					</aside>
				</div>
				<div class="col-md-6 col-sm-12 firm-content-center-box">
					<div class="firm-wrap">
						<h1>
							<?=$this->data['getFirm'][0]['name'];?>
						</h1>
						<ul class="slider-style-5">
							<li class="active">Описание</li>
							<li>Карта</li>
							<li>Привязка</li>
							<li>Визитка</li>
							<li>Статьи</li>
							<li>Т/У</li>
							<li>Фео.Рф</li>
							<li>96,6 Фео.FM</li>
							<li>Вакансии</li>
						</ul>
						<div class="firm-content">
							<div class="row">
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-12 col-xs-6">
											<div class="img-wrap">
												<?php
													if(substr($this->data['getFirm'][0]['file'],0, 4) == "http")
													{
														echo "<img class=\"img-responsive\" src=\"".$this->data['getFirm'][0]['file']."\" alt=\"alt\">";
													}
													else
													{
														echo "<img class=\"img-responsive\" src=\"http://feo.ua".$this->data['getFirm'][0]['file']."\" alt=\"alt\">";
													}
												?>
											</div>
										</div>
										<div class="col-sm-12 col-xs-6">
											<div class="rate-wrap">
												<span>Рейтинг</span>
												<span>интернет - <?=$this->data['getFirm'][0]['rating'];?></span>
												<span>телефон - <?=$this->data['getFirm'][0]['rating_tel'];?></span>
												<span>общий - <?=$this->data['getFirm'][0]['rating_ob'];?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-9">
									<div class="firm-info">
										<span><b>Адрес:</b> <?=$this->data['getFirm'][0]['adr_f'];?></span>
										<span><b>Телефон:</b> <?=str_replace(array(";",","), "<br>", $this->data['getFirm'][0]['phones']);?></span>
										<?php if ($this->data['getFirm'][0]['web'] != 'n/a'): ?>
										<span><b>Сайт:</b> <a href="http://<?=$this->data['getFirm'][0]['web'];?>" target="_blank"><?=$this->data['getFirm'][0]['web'];?></a></span>
										<?php endif; ?>
										<span><b>Руководитель:</b> <?=$this->data['getFirm'][0]['menag'];?></span>
										<span><b>График работы:</b> c <?=str_replace(array(";",","), " до ", $this->data['getFirm'][0]['work']);?></span>
										<span><b>Выходные дни:</b> <?=$this->data['getFirm'][0]['sunday'] ? $this->data['getFirm'][0]['sunday'] : "Без выходных";?></span>
									</div>
									<div class="firm-text">
										<p><?=$this->data['getFirm'][0]['activ'];?></p>
									</div>
								</div>
							</div>
						</div>

						<!--<div class="firm-prizyazka">
							<div class="img-alert"></div>
							<div class="content">
								<p>Здравствуйте, если Вы являетесь представителем фирмы "Богатырь, СТО". Вы можете: Привязать Вашу фирму к нему (через личный кабинет) и управлять ей.</p>
							</div>
							<div class="button-wrap-person-add">
								<a href="#" class="button">Мои фирмы</a>
							</div>
						</div>

						<div class="firm-vizitka">
                            <div class="img-wrap">
                                <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/firm/item-1.jpg" alt="alt">
                            </div>
                            <div class="descr">
                                <p>Визитка будет показана до:<br> 2018-03-28</p>
                            </div>
                        </div>-->

						<!--<div class="firm-stati">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="img-wrap">
                                        <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/news-img/img-5.jpg" alt="alt">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="content">
                                        <h2>КТО есть КТО: «Богатырь», ООО</h2>
                                        <p>Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу Семантика большого языкового</p>
                                        <div class="time">
                                            <span class="icon-clock"></span><time class="text-clock" datetime="2018-03-01 09:29:17" title="2018-03-01 09:29:17"></time>
                                        </div>
                                        <div class="right-block">
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views">500</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->

						<!--<div class="firm-nb">
                            <h1>Данная фирма учавствует в голосовании "Народный Бренд"</h1>
                            <a href="#" class="link">Голосуйте за фирму Богатырь, СТО</a>
                            <div class="barcode">
                                <img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/firm/item-2.jpg" alt="alt">
                            </div>
                            <div class="nb-logo">
                                <img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/firm/item-3.jpg" alt="alt">
                            </div>
                        </div>-->

						<!--<div class="firm-ty">
                            <table>
                                <tr>
                                    <th class="column-1">Товар</th>
                                    <th class="column-2">Ед.Изм.</th>
                                    <th class="column-3">Цена</th>
                                </tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                                <tr><td class="column-1">Автоэлектрик</td><td class="column-2">кг</td><td class="column-3">300,00 руб.</td></tr>
                            </table>
                        </div>-->

						<!--<div class="firm-feorf">
                            <div class="img-wrap">
                                <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/firm/item-4.jpg" alt="alt">
                            </div>
                        </div>-->

						<!--<div class="firm-feofm">
                            <div class="audio-wrap">
                                <div class="audio">
                                    <div class="top-sect">
                                        <div class="title">В Феодосии освятили закладной...</div>
                                    </div>
                                    <span class="time-wrap"><span class="icon-time"><i class="fa fa-clock-o" aria-hidden="true"></i></span>Сегодня в 15:35</span>
                                    <div class="content-sect">
                                        <audio class="mini-radio" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/mp3/M83-Wait.mp3" preload="auto" controls></audio>
                                    </div>
                                </div>
                                <div class="audio">
                                    <div class="top-sect">
                                        <div class="title">В Феодосии освятили закладной...</div>
                                    </div>
                                    <span class="time-wrap"><span class="icon-time"><i class="fa fa-clock-o" aria-hidden="true"></i></span>Сегодня в 15:35</span>
                                    <div class="content-sect">
                                        <audio class="mini-radio" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/mp3/M83-Wait.mp3" preload="auto" controls></audio>
                                    </div>
                                </div>
                                <div class="audio">
                                    <div class="top-sect">
                                        <div class="title">В Феодосии освятили закладной...</div>
                                    </div>
                                    <span class="time-wrap"><span class="icon-time"><i class="fa fa-clock-o" aria-hidden="true"></i></span>Сегодня в 15:35</span>
                                    <div class="content-sect">
                                        <audio class="mini-radio" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/mp3/M83-Wait.mp3" preload="auto" controls></audio>
                                    </div>
                                </div>
                            </div>
                        </div>-->

						<!--<div class="firm-vakansii">
                            <div class="item">
                                <h2>Название вакансии</h2>
                                <p>Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу Семантика большого языкового океана. Маленький ручеек Даль журчит по всей стране и обеспечивает ее всеми необходимыми правила</p>
                            </div>
                            <div class="item">
                                <h2>Название вакансии</h2>
                                <p>Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу Семантика большого языкового океана. Маленький ручеек Даль журчит по всей стране и обеспечивает ее всеми необходимыми правила</p>
                            </div>
                            <div class="item">
                                <h2>Название вакансии</h2>
                                <p>Далеко-далеко за словесными горами в стране гласных и согласных живут рыбные тексты. Вдали от всех живут они в буквенных домах на берегу Семантика большого языкового океана. Маленький ручеек Даль журчит по всей стране и обеспечивает ее всеми необходимыми правила</p>
                            </div>
                        </div>-->

					</div>
					<iframe class="firm-map" src="https://21200.ru/?action=mapexp&id=<?=$this->data['firmId'];?>" width="100%" height="500" frameborder="0"></iframe>
				</div>
				<div class="col-md-3 col-sm-12 hidden-sm hidden-xs ads-aside-right-box block-sticker-wrap">
					<aside>
						<?=do_shortcode('[sidebar_adv]');?>
					</aside>
				</div>
			</div>
		</section>
	</div>
</main>