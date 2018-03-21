<main>
	<div class="container main-ads-single-page">
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
					<h2>Объявления</h2>
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
							<h2>Рубрики объявлений</h2>
							<ul class="ads-ul-block">
								<?php foreach ((array) $this->data['getCats'] as $item): ?>
								<li class="ads-li">
									<?php if (!$item['cpu']): ?>
									<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/group_<?=$item['id']?>"><?=$item['name']?> (<?=$item['count']?>)</a>
									<ul class="ads-ul-block-inner">
										<?php foreach ((array) $this->data['getSubCats'] as $sub_item): ?>

										<?php if ($sub_item['pid'] == $item['id']): ?>
										<?php if (!$sub_item['cpu']): ?>
										<li class="ads-li-inner"><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/group_<?=$item['id']?>/category_<?=$sub_item['id']?>"><?=$sub_item['name']?> (<?=$sub_item['count']?>)</a></li>
										<?php else: ?>
										<li class="ads-li-inner"><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/group_<?=$item['id']?>/<?=$sub_item['cpu']?>"><?=$sub_item['name']?> (<?=$sub_item['count']?>)</a></li>
										<?php endif; ?>
										<?php endif; ?>

										<?php endforeach; ?>
									</ul>
									<?php else: ?>
									<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/<?=$item['cpu']?>"><?=$item['name']?> (<?=$item['count']?>)</a>
									<ul class="ads-ul-block-inner">
										<?php foreach ((array) $this->data['getSubCats'] as $sub_item): ?>

										<?php if ($sub_item['pid'] == $item['id']): ?>
										<?php if (!$sub_item['cpu']): ?>
										<li class="ads-li-inner"><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/<?=$item['cpu']?>/category_<?=$sub_item['id']?>"><?=$sub_item['name']?> (<?=$sub_item['count']?>)</a></li>
										<?php else: ?>
										<li class="ads-li-inner"><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/<?=$item['cpu']?>/<?=$sub_item['cpu']?>"><?=$sub_item['name']?> (<?=$sub_item['count']?>)</a></li>
										<?php endif; ?>
										<?php endif; ?>

										<?php endforeach; ?>
									</ul>
									<?php endif; ?>
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
				<div class="col-md-6 col-sm-12 ads-content-center-box">
					<div class="ads-page-wrap">
						<div class="top-sect">
							<div class="row">
								<div class="col-xs-8">
									<h1>
										<?=$this->data['getAds'][0]['caption'];?>
									</h1>
								</div>
								<div class="col-xs-4">
									<span class="price"><?=$this->data['getAds'][0]['price'];?> руб.</span>
								</div>
							</div>
						</div>
						<div class="middle-sect">
							<div class="user-name">
								<span class="icon-user"></span>
								<span class="text-user"><?=$this->data['getAds'][0]['user_name'];?></span>
							</div>
							<div class="location">
								<span class="icon-location-pin"></span>
								<span class="text-location"><?=$this->data['getAds'][0]['city'];?></span>
							</div>
							<div class="blue-button-wrap">
								<a href="#" class="blue-button" id="show_phone" data-ads-id="<?=$this->data['getAds'][0]['id'];?>">Показать телефон</a>
							</div>
						</div>
						<div class="bottom-sect">
							<div class="time">
								<span class="icon-clock"></span><time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $this->data['getAds'][0]['add_time']);?>" title="<?=date('Y-m-d H:i:s', $this->data['getAds'][0]['add_time']);?>"></time>
							</div>
							<div class="get-vip">
								<span class="icon-diamond"></span><a href="#" class="text-vip">Получить VIP</a>
							</div>
							<div class="ads-item-up">
								<i class="fa fa-long-arrow-up" aria-hidden="true"></i>
								<a href="#" class="text-location">Поднять объявление</a>
							</div>
						</div>
						<div class="fotorama" data-allowfullscreen="true" data-click="true" data-nav="thumbs" data-loop="true" data-width="100%" data-fit="cover">
							<?php
								$arr = json_decode($this->data['getAds'][0]['json_photos'], true);

								if(!$arr['all'])
								{
									if(!$arr['main'])
									{
										echo '<img src="https://feo.ua/app_adv/engine/upload/photos/'.$arr['main']['id'].'.'.$arr['main']['ext'].'">';
									}
									else
									{
										echo '<img src="https://feo.ua/app_adv/engine/views/net_foto_ob.jpg">';
									}
								}
								else
								{
									foreach($arr['all'] AS $img){ echo '<img src="https://feo.ua/app_adv/engine/upload/photos/'.$img['id'].'.'.$img['ext'].'">'; }
								}
							?>
						</div>
						<div class="info-content-block">
							<span>ID объявления: <?=$this->data['getAds'][0]['id'];?></span>
							<span>Показов: <?=$this->data['getAds'][0]['view_count'];?></span>
							<span>Открытий: <?=$this->data['getAds'][0]['open_count'];?></span>
						</div>
						<div class="content-wrap">
							<!--<div class="ads-info-head">
								<span><b>Тип кузова:</b> Седан</span>
								<span><b>Модель:</b> 2115</span>
								<span><b>Модификация:</b> Classic</span>
								<span><b>Пробег:</b> 180000</span>
							</div>-->
							<p><?=$this->data['getAds'][0]['descr'];?></p>
						</div>
					</div>
					<div class="row">
						<?php foreach ((array) $this->data['getAds4'] as $ads): ?>
						<div class="ads-block-horizontale col-sm-12 col-xs-6">
							<div class="ads-item-wrap">
								<div class="row">
									<div class="col-sm-4">
										<div class="img-wrap">
											<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>">
												<?php
											$arr = json_decode($ads['json_photos'], true);

											if(!$arr['main'])
											{
												echo '<img src="https://feo.ua/app_adv/engine/views/net_foto_ob.jpg" style="max-width: 100%;">';
												}
												else
												{
												echo '<img src="https://feo.ua/app_adv/engine/upload/photos/'.$arr['main']['id'].'.'.$arr['main']['ext'].'">';
												}
												?>
											</a>
										</div>
									</div>
									<div class="col-sm-8">
										<div class="content">
											<div class="row">
												<div class="col-sm-12">
													<div class="item-title">
														<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>">
															<?=$ads['caption'];?>
														</a>
													</div>
													<div class="item-select hidden-xs">
														<i class="fa fa-star-o active" aria-hidden="true"></i>
														<i class="fa fa-star" aria-hidden="true"></i>
													</div>
												</div>
												<div class="col-sm-8">
													<div class="item-content">
														<div class="price">
															<span><?=$ads['price'];?> руб.</span>
														</div>
														<div class="user-name hidden-xs">
															<span><?=$ads['user_name'];?></span>
														</div>
														<p class="ads-content-text hidden-xs">
															<?=$ads['descr'];?>
														</p>
													</div>
												</div>
												<div class="col-sm-4 hidden-xs">
													<div class="item-content-right">
														<a href="#" class="get-vip">Получить VIP</a>
														<a href="#" class="report">Пожаловаться</a>
														<span class="cat-name">
														<?php
															switch ($ads['main_catid']) {
																case 1:
																	echo "Одежда и обувь";
																	break;
																case 2:
																	echo "Всё для дома и сада";
																	break;
																case 3:
																	echo "Бытовая техника";
																	break;
																case 4:
																	echo "Цифровая техника";
																	break;
																case 6:
																	echo "Домашние животные";
																	break;
																case 7:
																	echo "Хобби и спорт";
																	break;
																case 8:
																	echo "Услуги";
																	break;
																case 9:
																	echo "Всё для транспорта";
																	break;
																case 13:
																	echo "Работа";
																	break;
																case 15:
																	echo "Транспорт";
																	break;
																case 16:
																	echo "Недвижимость";
																	break;
															}
														?>
													</span>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="item-foot">
														<div class="time">
															<span class="icon-clock hidden-xs"></span>
															<time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $ads['add_time']);?>" title="<?=date('Y-m-d H:i:s', $ads['add_time']);?>">
														</div>
														<div class="location hidden-xs">
															<span class="icon-location-pin"></span>
															<span class="text-location"><?=$ads['city'];?></span>
														</div>
														<div class="ads-item-up hidden-xs">
															<i class="fa fa-long-arrow-up" aria-hidden="true"></i>
															<a href="#" class="text-location">Поднять объявление</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>

				</div>
				<div class="col-md-3 col-sm-12 ads-aside-right-box block-sticker-wrap">
					<aside>
						<div class="yellow-box">
							<div class="yellow-title">
								<h2>VIP - объявления</h2>
							</div>
							<div class="yellow-content">
								<div class="row slider-style-6">
									<?php foreach ((array) $this->data['getVipAds'] as $ads): ?>
									<div class="col-md-12 col-sm-4 item">
										<div class="item-img">
											<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>">
												<?php
											$arr = json_decode($ads['json_photos'], true);

											if(!$arr['main'])
											{
												echo '<img class="img-responsive" src="https://feo.ua/app_adv/engine/views/net_foto_ob.jpg" style="max-width: 100%;">';
												}
												else
												{
												echo '<img class="img-responsive" src="https://feo.ua/app_adv/engine/upload/photos/'.$arr['main']['id'].'.'.$arr['main']['ext'].'">';
												}
												?>
											</a>
										</div>
										<div class="item-content">
											<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>">
												<?=$ads['caption'];?>
											</a>
										</div>
										<div class="item-foot">
											<div class="price">
												<span><?=$ads['price'];?> руб.</span>
											</div>
											<div class="location">
												<span class="icon-location-pin"></span>
												<span class="text-location"><?=$ads['city'];?></span>
											</div>
											<div class="time">
												<span class="icon-clock"></span>
												<time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $ads['add_time']);?>" title="<?=date('Y-m-d H:i:s', $ads['add_time']);?>">
											</div>
										</div>
									</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<?=$this->comments->lastcomments_vidget();?>
					</aside>
				</div>
			</div>
		</section>
	</div>
</main>