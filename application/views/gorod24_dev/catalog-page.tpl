<main>
	<div class="container main-catalog-page">
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
					<h2><?=$this->headers['title']?></h2>
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
				<div class="col-md-6 col-sm-12 catalog-page-content-center-box">
					<div class="catalog-page-wrap">
						<div class="row">
							<?php foreach ((array) $this->data['getFirm'] as $firm): ?>
                                <?php if ($firm['on_off']<>'2'): ?>
                                    <?php if ($firm['oplata']>=date("Y-m-d")): ?>
                                        <div class="ads-block-horizontale col-sm-12 col-xs-6">
                                            <div class="ads-item-wrap">
                                                <div class="row">
                                                    <div class="col-sm-4">
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
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="content">
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="item-title">
                                                                        <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$firm['url'];?>">
                                                                            <?=$firm['name'];?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-8">
                                                                    <div class="item-content">
                                                                        <p class="ads-content-text">
                                                                            <?=$firm['activ'];?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4 hidden-xs">
                                                                    <div class="item-content-right">
                                                                        <span class="rating">Рейтинг</span>
                                                                        <span class="rating">интернет - <?=$firm['rating'];?></span>
                                                                        <span class="rating">телефон - <?=$firm['rating_tel'];?></span>
                                                                        <span class="rating">общий - <?=$firm['rating_ob'];?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
										<div class="clearfix"></div>
                                        <div class="col-md-12">
                                            <div class="cat-item-link">
                                                <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$firm['url'];?>"><?=$firm['name'];?></a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="col-md-12">
                                        <div class="cat-item-link">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/catalog/<?=$firm['url'];?>"><?=$firm['name'];?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>
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