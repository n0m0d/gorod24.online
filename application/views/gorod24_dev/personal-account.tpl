<main>
	<div class="container main-pa-page">
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
					<h2>Личный кабинет</h2>
				</div>
			</div>
		</div>
		<section class="section-1">
			<div class="row">
				<div class="col-md-3 hidden-sm hidden-xs">
					<aside>
						<div class="vertical-banners">
							<div class="banner-wrap">
								<a href="#">
									<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
								</a>
							</div>
						</div>
					</aside>
				</div>
				<div class="col-md-6 col-sm-12 pa-content-center-box">
					<div class="pa-wrap">
						<div class="head-sect">
							<div class="head-cont">
								<div class="img-wrap">
									<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/persons/item-2.jpg" alt="alt">
								</div>
								<div class="right-sect">
									<div class="name">
										Константин Валерьевич
									</div>
									<div class="rep-range">
										<span class="title">Уровень доверия</span>
										<div class="range-wrap">
											<div class="range" style="width: 31%;"><span>31%</span></div>
										</div>
									</div>
									<ul class="descr-mnu">
										<li><a href="#">Как поднять</a></li>
										<li><a href="#">О сертификатах</a></li>
									</ul>
									<div class="get-sert">
										<div class="icon-wrap">
											<span class="icon-badge"></span>
										</div>
										<a href="#">Получить сертификат</a>
									</div>
								</div>
							</div>
						</div>
						<div class="cont-sect">
							<ul class="menu-option-list hidden-xs">
								<li class="active"><a href="#">Я на feo.ua</a></li>
								<li>
									<a href="#">Контент</a>
									<span class="in-menu-span icon-arrow-down"></span>
									<ul class="in-1">
										<li><a href="#">Мои объявления</a></li>
										<li><a href="#">Новости</a></li>
										<li>
											<a href="#">Фото</a>
											<span class="in-menu-span icon-arrow-down"></span>
											<ul class="in-2">
												<li><a href="#">Мой фото</a></li>
												<li><a href="#">Альбомы</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<li><a href="#">Мои фирмы</a></li>
								<li><a href="#">Друзья</a></li>
								<li><a href="#">Подписка</a></li>
							</ul>
							<div class="list-button-wrap hidden-lg hidden-md hidden-sm">
								<a href="#" class="list-button open-pa-button"><span class="icon-menu"></span></a>
							</div>
							<ul class="menu-option-list-mobile hidden-lg hidden-md hidden-sm">
								<li class="active"><a href="#">Я на feo.ua</a></li>
								<li>
									<a class="in-menu" href="in-1">Контент</a>
									<span class="in-menu-span in-1-span icon-arrow-down"></span>
									<ul class="in-1">
										<li><a href="#">Мои объявления</a></li>
										<li><a href="#">Новости</a></li>
										<li>
											<a class="in-menu" href="in-2">Фото</a>
											<span class="in-menu-span in-2-span icon-arrow-down"></span>
											<ul class="in-2">
												<li><a href="#">Мой фото</a></li>
												<li><a href="#">Альбомы</a></li>
											</ul>
										</li>
									</ul>
								</li>
								<li><a href="#">Мои фирмы</a></li>
								<li><a href="#">Друзья</a></li>
								<li><a href="#">Подписка</a></li>
							</ul>
						</div>
					</div>
					<div class="profile-wrap">
						<form>
							<div class="input-wrap">
								<label>
									<span class="icon-wrap"><span class="icon-user"></span></span>
									<input type="text" name="name" placeholder="Имя" required>
									<a href="#" class="input-accept">Подтвердить</a>
								</label>
							</div>
							<div class="input-wrap">
								<label>
									<span class="icon-wrap"><span class="icon-user"></span></span>
									<input type="text" name="lastname" placeholder="Фамилия" required>
								</label>
							</div>
							<div class="input-wrap-date">
								<div class="descr">
									<span class="icon-wrap"><span class="icon-calendar"></span></span>
									Дата рождения
								</div>
								<select name="" class="select-filter">
									<?php
										for($i = 1920; $i < 2015; $i++)
										{
											if ($i == 1920)
											{
												echo "<option value=\"\">Год</oplion>";
											}
											else
											{
												echo "<option value=\"$i\">$i</option>";
											}
										}
									?>
								</select>
								<select name="" class="select-filter">
									<option value="">Месяц</option>
									<option value="1">Январь</option>
									<option value="2">Февраль</option>
									<option value="3">Март</option>
									<option value="4">Апрель</option>
									<option value="5">Май</option>
									<option value="6">Июнь</option>
									<option value="7">Июль</option>
									<option value="8">Август</option>
									<option value="9">Сентябрь</option>
									<option value="10">Октябрь</option>
									<option value="11">Ноябрь</option>
									<option value="12">Декабрь</option>
								</select>
								<select name="" class="select-filter">
									<?php
										for ($i = 0; $i < 32; $i++)
										{
											if ($i == 0)
											{
												echo "<option value=\"\">Число</oplion>";
											}
											else
											{
												echo "<option value=\"$i\">$i</oplion>";
											}
										}
									?>
								</select>
							</div>
							<div class="input-wrap">
								<label>
									<span class="icon-wrap"><span class="icon-location-pin"></span></span>
									<input type="text" name="city" placeholder="Город" required>
								</label>
							</div>
							<div class="input-wrap">
								<label>
									<span class="icon-wrap"><span class="icon-envelope"></span></span>
									<input type="text" name="email" placeholder="E-mail" required>
								</label>
							</div>
							<div class="input-wrap">
								<label>
									<span class="icon-wrap"><span class="icon-phone"></span></span>
									<input type="text" name="phone" placeholder="Телефон" required>
								</label>
							</div>
							<div class="show-profile">
								<span class="title">Показывать эти данные другим пользователям</span>
								<span class="toggle-bg">
									<input type="radio" name="toggle" class="radio-off" value="off">
									<input type="radio" name="toggle" class="radio-on" value="on" checked>
									<span class="switch"></span>
								</span>
							</div>
						</form>
						<div class="social-wrap">
							<a href="#" class="vk"><span><i class="fa fa-vk" aria-hidden="true"></i></span></a>
							<a href="#" class="od"><span><i class="fa fa-odnoklassniki" aria-hidden="true"></i></span></a>
							<a href="#" class="fb"><span><i class="fa fa-facebook" aria-hidden="true"></i></span></a>
							<a href="#" class="tw"><span><i class="fa fa-twitter" aria-hidden="true"></i></span></a>
							<div class="social-title">
								Добавить социальные сети
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-12 hidden-sm hidden-xs ads-aside-right-box block-sticker-wrap">
					<aside>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
						<div class="block-sticker">
							<h3>Мебельная фабрика Дюарт</h3>
							<p>Мебель по любым вашим размерам Подробности на сайте</p>
							<div class="phone">
								<span class="icon-phone"></span><span class="number">+7 (978) 701-98-16</span>
							</div>
						</div>
					</aside>
				</div>
			</div>
		</section>
	</div>
</main>