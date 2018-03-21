<main>
	<div class="container main-catalog">
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
				<h2>Отраслевой каталог Феодосии</h2>
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
			<div class="col-md-6 col-sm-12 catalog-content-center-box">
				<div class="catalog-wrap">
					<div class="catalog-search-wrap">
						<form action="../catalog/search" method="GET">
							<input type="text" name="search" placeholder="Поиск фирмы">
							<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>
					</div>
					<div class="firm-item-wrap">
						<div class="row">
							<?php foreach ((array) $this->data['getCatalogFirms'] as $firm): ?>
							<div class="col-sm-3 col-xs-6">
								<div class="item">
									<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$firm['url'];?>">
										<div class="img-wrap">
										<?php
											if(substr($firm['file'],0, 4) == "http")
											{
												echo "<img class=\"img-responsive\" src=\"".$firm['file']."\" alt=\"alt\">";
											}
											else
											{
												echo "<img class=\"img-responsive\" src=\"https://feo.ua".$firm['file']."\" alt=\"alt\">";
											}
										?>
										</div>

										<div class="content">
											<p><?=$firm['name'];?></p>
											<span><?=str_replace(array(";", ","), " ", $firm['phones']);?></span>
										</div>
									</a>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="catalog-items-wrap">
                        <?php foreach ((array) $this->data['main_otr'] as $main): ?>
                        <div class="item">
                            <div class="title">
                                <h3><span><img src="<?=$main['icon'];?>" alt="alt"></span><?=$main['name_main'];?></h3>
                            </div>
                            <ul>
                                <?php foreach ((array) $this->data['list_otr'][$main['id_main']] as $list): ?>
								<?php if ($list['sub_count'] != 0): ?>
                                <li><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$list['url'];?>"><?=$list['name']?> <span>(<?=$list['sub_count']?>)</span></a></li>
								<?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>
				</div>
			</div>
			<div class="col-md-3 col-sm-12 ads-aside-right-box block-sticker-wrap">
				<aside>
					<?=do_shortcode('[sidebar_adv]');?>
				</aside>
			</div>
		</div>
	</section>
</div>
</main>
