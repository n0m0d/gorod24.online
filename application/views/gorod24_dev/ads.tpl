<main>
	<div class="container main-ads-page">
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
			<div class="col-md-11 col-sm-8 col-xs-6">
				<h2>Объявления</h2>
			</div>
			<div class="col-sm-3 col-xs-3 hidden-lg hidden-md hidden-lg">
				<div class="list-button-wrap">
					<a href="#" class="list-button open-filters-button"><span class="icon-equalizer"></span></a>
				</div>
			</div>
			<div class="col-md-1 col-sm-1 col-xs-3">
				<div class="list-button-wrap">
					<a href="#" class="list-button open-cats-button"><span class="icon-menu"></span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="ads-cat">
		<?php foreach ((array) $this->data['getCats'] as $item): ?>
		<div class="item-wrap">
			<?php if (!$item['cpu']): ?>
			<a class="ajax-category" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/group_<?=$item['id']?>">
				<span>
					<img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/ads-icons/<?=$item['menu_icon']?>.png" alt="<?=$item['name']?>">
				</span>
				<?=$item['name']?>
			</a>
			<?php else: ?>
			<a class="ajax-category" href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/<?=$item['cpu']?>">
				<span>
					<img src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/ads-icons/<?=$item['menu_icon']?>.png" alt="<?=$item['name']?>">
				</span>
				<?=$item['name']?>
			</a>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<!--<div class="ads-cat-slider slider-style-5 hidden-lg hidden-md hidden-sm">
		<div class="item-wrap">
			<a href="#">Одежда и обувь</a>
		</div>
		<div class="item-wrap">
			<a href="#">Цифровая техника</a>
		</div>
		<div class="item-wrap">
			<a href="#">Услуги</a>
		</div>
		<div class="item-wrap">
			<a href="#">Помощь</a>
		</div>
		<div class="item-wrap">
			<a href="#">Все для дома и сада</a>
		</div>
		<div class="item-wrap">
			<a href="#">Домашние животные</a>
		</div>
		<div class="item-wrap">
			<a href="#">Все для транспорта</a>
		</div>
		<div class="item-wrap">
			<a href="#">Транспорт</a>
		</div>
		<div class="item-wrap">
			<a href="#">Бытовая техника</a>
		</div>
		<div class="item-wrap">
			<a href="#">Хобби и спорт</a>
		</div>
		<div class="item-wrap">
			<a href="#">Работа</a>
		</div>
		<div class="item-wrap">
			<a href="#">Недвижимость</a>
		</div>
	</div>-->
	<div class="title-block">
		<h2>Транспорт</h2>
		<span class="sort hidden-xs">Сортировать</span>
		<?php if ($_GET['filter'] && $_GET['filter'] == 'price'): ?>
		<a href="?filter=price-off" class="date-wrap">
			<div class="datepicker">Цена</div>
			<span class="icon-arrow-up"></span>
		</a>
		<?php elseif ($_GET['filter'] && $_GET['filter'] == 'price-off'): ?>
		<a href="?filter=price" class="date-wrap">
			<div class="datepicker">Цена</div>
			<span class="icon-arrow-down"></span>
		</a>
		<?php else: ?>
		<a href="?filter=price" class="date-wrap">
			<div class="datepicker">Цена</div>
			<span class="icon-arrow-down" style="display: none;"></span>
		</a>
		<?php endif; ?>

		<?php if ($_GET['filter'] && $_GET['filter'] == 'date'): ?>
		<a href="?filter=date-off" class="date-wrap">
			<div class="datepicker">Дата</div>
			<span class="icon-arrow-up"></span>
		</a>
		<?php elseif ($_GET['filter'] && $_GET['filter'] == 'price' || $_GET['filter'] == 'price-off'): ?>
		<a href="?filter=date" class="date-wrap">
			<div class="datepicker">Дата</div>
			<span class="icon-arrow-down" style="display: none;"></span>
		</a>
		<?php else: ?>
		<a href="?filter=date" class="date-wrap">
			<div class="datepicker">Дата</div>
			<span class="icon-arrow-down"></span>
		</a>
		<?php endif; ?>
	</div>
	<section class="section-1">
		<div class="row">
			<div class="col-md-3 block-sticker-wrap">
				<aside>
					<div class="button-wrap hidden-sm hidden-xs">
						<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/add/1/" class="button">
							<span class="icon-pencil"></span>
							<span class="button-text">Добавить объявление</span>
						</a>
					</div>
					<div class="realty-filter">
						<form method="GET" action="<?=$GLOBALS['CONFIG']['HTTP_HOST'].$_SERVER['REQUEST_URI']?>">

							<?php foreach ((array) array_slice($this->data['getOptions'], 1) as $item): ?>

								<?php if ($item['type'] == 'check'): ?>
									<?php foreach ((array) $item['items'] as $option): ?>
										<label>
											<input type="checkbox" class="-filter-opt" data-filter-key="filter-opt<?=$item['id']?>" data-filter-type="check" name="check[check][<?=$item['id']?>][]"

													<?php
														foreach ($_GET['check']['check'][$item['id']] as $key => $check)
														{
															if ($check == $option['id'])
															{
																echo "checked";
															}
														}
													?>

												   value="<?=$option['id']?>">
											<?=$option['name']?>
										</label>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if ($item['type'] == 'range'): ?>
									<div class="price-ui-wrap">
										<h2><?=$item['title']?></h2>
										<div class="price-ui-slider" id="<?=$item['name']?>" data-min="<?=$item['range']['min']?$item['range']['min']:1;?>" data-max="<?=$item['range']['max']?$item['range']['max']:1000000;?>"></div>
										<!--<label>
											Включить
											<input type="checkbox" class="checkbox_range" data-slider-id="<?=$item['name']?>">
										</label>-->
										<?php if ($item['id'] == '0'): ?>
											<input type="hidden" data-filter-key="filter-opt<?=$item['id']?>" data-filter-type="int" class="-filter-opt" id="range_<?=$item['name']?>_1" name="price[price][]" disabled="disabled">
											<input type="hidden" data-filter-key="filter-opt<?=$item['id']?>" data-filter-type="int" class="-filter-opt" id="range_<?=$item['name']?>_2" name="price[price][]" disabled="disabled">
										<?php else: ?>
											<input type="hidden" data-filter-key="filter-opt<?=$item['id']?>" data-filter-type="int" class="-filter-opt" id="range_<?=$item['name']?>_1" name="range[range][<?=$item['id']?>][]" disabled="disabled">
											<input type="hidden" data-filter-key="filter-opt<?=$item['id']?>" data-filter-type="int" class="-filter-opt" id="range_<?=$item['name']?>_2" name="range[range][<?=$item['id']?>][]" disabled="disabled">
										<?php endif; ?>

										<script type="text/javascript">
											var slider = document.querySelector("#<?=$item['name']?>");

											noUiSlider.create(slider, {
												start: [
                                                <?php
													if ($_GET['range']['range'][$item['id']][0] && $_GET['range']['range'][$item['id']][1])
													{
														if ($_GET['range']['range'][$item['id']])
														{
															$var1 = $_GET['range']['range'][$item['id']][0];
															$var2 = $_GET['range']['range'][$item['id']][1];
														}
													}
													else
													{
                                                        $var1 = $_GET['price']['price'][0];
                                                        $var2 = $_GET['price']['price'][1];
													}

													if ($var1 && $var2)
													{
														echo $var1.",".$var2;
													}
													else
													{
														if ($item['range']['max'])
														{
															echo $item['range']['min'].",".$item['range']['max'];
														}
														else
														{
															echo "1, 1000000";
														}
													}
                                                ?>
												],
												connect: true,
												tooltips: true,
												step: 1,
												format: wNumb({
													decimals: 0,
													postfix: " <?=$item['unit']?>",
												}),
												range: {
													'min': <?=$item['range']['min']?$item['range']['min']:1;?>,
													'max': <?=$item['range']['max']?$item['range']['max']:1000000;?>
												}
											});

											var element_1 = document.getElementById("range_<?=$item['name']?>_1");
											var element_2 = document.getElementById("range_<?=$item['name']?>_2");

											slider.noUiSlider.on('update', function (values, handle) {

												var value = values[handle];

												if (handle == 0) {
													var result_1 = element_1.value = parseInt(value);
													$("#range_<?=$item['name']?>_1").attr("value", result_1);
												}

												if (handle == 1) {
													var result_2 = element_2.value = parseInt(value);
													$("#range_<?=$item['name']?>_2").attr("value", result_2);
												}
											});

                                            slider.noUiSlider.on('change', function (values, handle) {

                                                $("#range_<?=$item['name']?>_1").attr('disabled', false);
                                                $("#range_<?=$item['name']?>_2").attr('disabled', false);
                                            });

										</script>
									</div>
								<?php endif; ?>

								<?php if ($item['type'] == 'list'): ?>

									<div class="price-ui-wrap">
										<select data-filter-type="droplist" data-filter-key="filter-opt<?=$item['id']?>" class="-filter-opt" name="list[list][<?=$item['id']?>]">
											<option disabled selected>Выбрать фильтр</option>
											<?php foreach ((array) $item['items'] as $option): ?>
											<option value="<?=$option['id']?>"

											<?php
												if ($_GET['list']['list'][$item['id']] == $option['id'])
												{
													echo "selected";
												}
											?>

											><?=$option['name']?></option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php endif; ?>

							<?php endforeach; ?>
							<button type="submit" class="price-accept">Подтвердить</button>
						</form>
					</div>
					<div class="ads-cat-block hidden-sm hidden-xs">
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
					<div class="vertical-banners hidden-sm hidden-xs">
						<div class="banner-wrap">
							<a href="#">
								<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
							</a>
						</div>
					</div>
				</aside>
			</div>
			<div class="col-md-6 col-sm-12 ads-content-center-box">
				<div class="row">
					<?php foreach ((array) $this->data['getAds'] as $ads): ?>
					<div class="ads-block-horizontale col-sm-12 col-xs-6">
						<div class="ads-item-wrap">
							<div class="row">
								<div class="col-sm-4">
									<div class="img-wrap">
                                        <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>">
										<?php
											if($ads['json_photos'])
											{
												$arr = json_decode($ads['json_photos'], true);

												if(!$arr['main'])
												{
													echo '<img src="https://feo.ua/app_adv/engine/views/net_foto_ob.jpg" style="max-width: 100%;">';
												}
												else
												{
													echo '<img src="https://feo.ua/app_adv/engine/upload/photos/'.$arr['main']['id'].'.'.$arr['main']['ext'].'">';
												}
											}
											else
											{
												echo '<img src="'.$ads['photo'].'">';
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
														<?=$ads['name'];?>
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
																case 14:
																	echo "Помощь";
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
														<time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $ads['date']);?>" title="<?=date('Y-m-d H:i:s', $ads['date']);?>">
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
                    <div class="pagination-wrap">
                        <?=$this->data['paginator']?>
                    </div>
				</div>
			</div>
			<div class="col-md-3 col-sm-12 ads-aside-right-box block-sticker-wrap">
				<aside>
					<?php
						if (!$this->data['getVipAds'])
						{
							echo '<div class="yellow-box" style="display: none;">';
						}
						else
						{
							echo '<div class="yellow-box">';
						}
					?>
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