<main>
	<div class="container main-index">
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
		<section class="section-1">
			<div class="row">
				<div class="col-sm-9 left-slider-col">
					<div class="main-slider">
                        <?php foreach ((array) $this->data['getNewsSlider'] as $news): ?>
                        <div>
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>">
                                <div class="bgc-image" style="background-image: url(https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_1280_1024.jpg)"></div>
							</a>
							<div class="content">
								<div class="tags">
									<?php
                                            $tags = str_replace(";", "", explode("#",$news['news_tag']));
                                            $flag = true;
                                            $tags_items = '';
                                            array_shift($tags);
                                            foreach ($tags AS $key => $tag)
									{
									if ($flag)
									{
									$flag = false;
									}
									else
									{
									echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
									}
									}
									?>
								</div>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>"><h1><?=$news['news_head'];?></h1></a>
								<p><?=$news['news_lid'];?></p>
								<div class="time">
									<span class="icon-clock"></span>
									<time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
								</div>
							</div>
                        </div>
                        <?php endforeach; ?>
					</div>
				</div>
				<div class="col-sm-3 right-slider-col" id="news-slider-block">
					<div class="slider-news">
                        <?php foreach (array_slice($this->data['getTopNews'],0,2) as $news): ?>
						<div class="single-news">
							<div class="tags">
                                <?php
                                    $tags = str_replace(";", "", explode("#",$news['news_tag']));
                                    $flag = true;
                                    $tags_items = '';
                                    array_shift($tags);
                                    foreach ($tags AS $key => $tag)
                                    {
                                        if ($flag)
                                        {
                                            $flag = false;
                                        }
                                        else
                                        {
                                            echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                        }
                                    }
                                ?>
							</div>
							<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="news-descr">
                                <?=$news['news_head'];?>
							</a>
						</div>
                        <?php endforeach; ?>
                        <?php foreach (array_slice($this->data['getTopNews'],2,1) as $news): ?>
                        <div class="single-news hidden-sm">
                            <div class="tags">
                                <?php
                                    $tags = str_replace(";", "", explode("#",$news['news_tag']));
                                    $flag = true;
                                    $tags_items = '';
                                    array_shift($tags);
                                    foreach ($tags AS $key => $tag)
                                    {
                                        if ($flag)
                                        {
                                            $flag = false;
                                        }
                                        else
                                        {
                                            echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                        }
                                    }
                                ?>
                            </div>
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="news-descr">
                                <?=$news['news_head'];?>
                            </a>
                        </div>
                        <?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<!--<section class="section-2">
			<div class="row">
				<div class="col-md-6 col-sm-4 col-xs-12" id="news-items-block">
					<div class="info-block-horizontale">
						<?php foreach (array_slice($this->data['getLastNews'],0,1) AS $news): ?>
						<div class="info-item hidden-sm hidden-xs">
							<div class="row">
								<div class="col-md-5">
									<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr"></a>
									<div class="img-wrap">
										<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
									</div>
								</div>
								<div class="col-md-7">
									<div class="content">
										<div class="tags">
                                            <?php
                                                $tags = explode("#",$news['news_tag']);
                                                $flag = true;
                                                $tags_items = '';
                                                array_shift($tags);
                                                foreach ($tags AS $key => $tag)
                                                {
                                                    if ($flag)
                                                    {
                                                        $flag = false;
                                                    }
                                                    else
                                                    {
                                                        echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                                    }
                                                }
                                            ?>
										</div>
										<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
											<span><?=$news['news_head'];?></span>
										</a>
										<div class="time">
											<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
										</div>
                                        <div class="right-block">
                                            <div class="photos hidden-sm hidden-xs">
                                                <i class="fa fa-camera" aria-hidden="true"></i></span><span class="text-photos"><?=$news['photos_sum'];?></span>
                                            </div>
                                            <div class="comments hidden-sm hidden-xs">
                                                <i class="fa fa-comment" aria-hidden="true"></i><span class="text-comments"><?=$news['comments_sum'];?></span>
                                            </div>
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views"><?=$news['looks'];?></span>
                                            </div>
                                        </div>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
                        <?php foreach (array_slice($this->data['getLastNews'],1,1) AS $news): ?>
                        <div class="info-item">
                            <div class="row">
                                <div class="col-md-5">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr"></a>
                                    <div class="img-wrap">
                                        <img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="content">
                                        <div class="tags">
                                            <?php
                                                $tags = explode("#",$news['news_tag']);
                                                $flag = true;
                                                $tags_items = '';
                                                array_shift($tags);
                                                foreach ($tags AS $key => $tag)
                                            {
                                            if ($flag)
                                            {
                                            $flag = false;
                                            }
                                            else
                                            {
                                            echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                            }
                                            }
                                            ?>
                                        </div>
                                        <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
                                            <span><?=$news['news_head'];?></span>
                                        </a>
                                        <div class="time">
                                            <span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
                                        </div>
                                        <div class="right-block">
                                            <div class="photos hidden-sm hidden-xs">
                                                <i class="fa fa-camera" aria-hidden="true"></i></span><span class="text-photos"><?=$news['photos_sum'];?></span>
                                            </div>
                                            <div class="comments hidden-sm hidden-xs">
                                                <i class="fa fa-comment" aria-hidden="true"></i><span class="text-comments"><?=$news['comments_sum'];?></span>
                                            </div>
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views"><?=$news['looks'];?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
					</div>
				</div>
                <?php foreach (array_slice($this->data['getLastNews'],2,2) AS $news): ?>
				<div class="col-md-3 col-sm-4 col-xs-12 info-item-wrap">
					<div class="info-block-verticale">
						<div class="info-item">
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
                                <div class="img-wrap">
                                    <img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                </div>
                            </a>
							<div class="content">
								<div class="tags">
									<?php
									    $tags = explode("#",$news['news_tag']);
                                        $flag = true;
                                        $tags_items = '';
                                        array_shift($tags);
                                        foreach ($tags AS $key => $tag)
                                        {
                                            if ($flag)
                                            {
                                                $flag = false;
                                            }
                                            else
                                            {
                                                echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                            }
                                        }
									?>
								</div>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
									<span><?=$news['news_head'];?></span>
								</a>
								<div class="time">
									<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
								</div>
								<div class="views">
									<span class="icon-eye"></span><span class="text-views"><?=$news['looks'];?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
                <?php endforeach; ?>
			</div>
		</section>-->
		<div class="main-banners">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-wrap banner-wrap-80">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-2.jpg" alt="alt">
						</a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="banner-wrap banner-wrap-80">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-3.jpg" alt="alt">
						</a>
					</div>
				</div>
			</div>
		</div>
		<section class="section-3">
			<div class="row">
				<div class="col-md-9 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="title-block-slider">
								<h2>Новости <?=$this->data['city_name'];?></h2>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/" class="link-1">Все новости</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-1"></div>
									<div class="rightArrow" data-slider-id="slider-1"></div>
								</div>
								<a href="#" class="link-2 hidden-sm hidden-xs">Добавить новость</a>
							</div>
							<div class="slider-style-1 info-block-horizontale" id="slider-1">
                                <?php foreach (array_slice($this->data['getLastNews'],0,6) AS $news): ?>
								<div class="info-item">
									<div class="row">
										<div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
                                                <div class="img-wrap">
                                                    <img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
										</div>
										<div class="col-md-7">
											<div class="content">
												<div class="tags">
                                                    <?php
                                                        $tags = str_replace(";", "", explode("#",$news['news_tag']));
                                                        $flag = true;
                                                        $tags_items = '';
                                                        array_shift($tags);
                                                        foreach ($tags AS $key => $tag)
                                                        {
                                                            if ($flag)
                                                            {
                                                                $flag = false;
                                                            }
                                                            else
                                                            {
                                                                echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                                            }
                                                        }
                                                    ?>
												</div>
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
													<span><?=$news['news_head'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
												</div>
                                                <div class="right-block">
                                                    <div class="photos hidden-sm hidden-xs">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span><span class="text-photos"><?=$news['photos_sum'];?></span>
                                                    </div>
                                                    <div class="comments hidden-sm hidden-xs">
                                                        <i class="fa fa-comment" aria-hidden="true"></i><span class="text-comments"><?=$news['comments_sum'];?></span>
                                                    </div>
                                                    <div class="views">
                                                        <span class="icon-eye"></span><span class="text-views"><?=$news['looks'];?></span>
                                                    </div>
                                                </div>
											</div>
										</div>
									</div>
								</div>
                                <?php endforeach; ?>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<div class="title-block">
								<h2>Реклама</h2>
							</div>
							<div class="vertical-banners">
								<div class="banner-wrap">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
									</a>
								</div>
							</div>
						</div>
						<div class="col-md-8 col-sm-12 col-xs-12">
							<div class="title-block-slider">
								<h2 class="hidden-xs"><?=$this->data['city_name'];?> фильмы на сегодня</h2>
								<h2 class="hidden-lg hidden-md hidden-sm">Фильмы сегодня</h2>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-2"></div>
									<div class="rightArrow" data-slider-id="slider-2"></div>
								</div>
								<a href="#" class="link-2">Вся афиша</a>
							</div>
							<div class="slider-style-2" id="slider-2">
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
								<div class="item">
									<div class="img-wrap">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/kino-afisha/item-1.jpg" alt="alt">
									</div>
									<div class="descr">
										<span class="title">Мост шпионов</span>
										<span class="date">С 11 января</span>
									</div>
								</div>
							</div>
							<div class="main-banners hidden-sm hidden-xs">
								<div class="row">
									<div class="col-md-12">
										<div class="banner-wrap banner-wrap-80">
											<a href="#">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-2.jpg" alt="alt">
											</a>
										</div>
									</div>
									<div class="col-md-12">
										<div class="banner-wrap banner-wrap-80">
											<a href="#">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-3.jpg" alt="alt">
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 hidden-sm hidden-xs">
							<div class="title-block">
								<h2>Конкурс дня</h2>
							</div>
							<div class="competition-day">
								<div class="img-wrap">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/foto-dnya.jpg" alt="alt">
									</a>
								</div>
								<div class="descr">
										<span>
											Фото конкурс<br>
											"Лучшие фото Феодосии"
										</span>
								</div>
							</div>
						</div>
						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="title-block-slider">
								<h2>Новости Крыма</h2>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/" class="link-1">Все новости</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-3"></div>
									<div class="rightArrow" data-slider-id="slider-3"></div>
								</div>
								<a href="#" class="link-2 hidden-sm hidden-xs">Добавить новость</a>
							</div>
							<div class="slider-style-1 info-block-horizontale" id="slider-3">
                                <?php foreach ($this->data['getLastNewsRegion'] AS $news): ?>
                                <div class="info-item">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
                                                <div class="img-wrap">
                                                    <img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="content">
                                                <div class="tags">
                                                    <?php
                                                        $tags = str_replace(";", "", explode("#",$news['news_tag']));
                                                        $flag = true;
                                                        $tags_items = '';
                                                        array_shift($tags);
                                                        foreach ($tags AS $key => $tag)
                                                    {
                                                    if ($flag)
                                                    {
                                                    $flag = false;
                                                    }
                                                    else
                                                    {
                                                    echo "<a href=\"".$GLOBALS['CONFIG']['HTTP_HOST']."/news/tag=".$tag."\">"."#".$tag."</a>";
                                                    }
                                                    }
                                                    ?>
                                                </div>
                                                <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'];?>/news/<?=$news['url'];?>" class="info-descr">
                                                    <span><?=$news['news_head'];?></span>
                                                </a>
                                                <div class="time">
                                                    <span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
                                                </div>
                                                <div class="right-block">
                                                    <div class="photos hidden-sm hidden-xs">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span><span class="text-photos"><?=$news['photos_sum'];?></span>
                                                    </div>
                                                    <div class="comments hidden-sm hidden-xs">
                                                        <i class="fa fa-comment" aria-hidden="true"></i><span class="text-comments"><?=$news['comments_sum'];?></span>
                                                    </div>
                                                    <div class="views">
                                                        <span class="icon-eye"></span><span class="text-views"><?=$news['looks'];?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 hidden-lg hidden-md">
							<div class="title-block">
								<h2>Реклама</h2>
							</div>
							<div class="vertical-banners">
								<div class="banner-wrap">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
									</a>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 hidden-sm hidden-xs">
							<div class="interviews-wrap">
								<?=$this->interviews->get_interview_vidget_rand();?>
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="title-block-slider">
								<h2>Объявления</h2>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/" class="link-1">Все объявления</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-4"></div>
									<div class="rightArrow" data-slider-id="slider-4"></div>
								</div>
								<a href="#" class="link-2 hidden-sm hidden-xs">Добавить объявление</a>
							</div>
							<div class="slider-style-3 info-block-verticale" id="slider-4">
                                <?php foreach ($this->data['getAds'] as $ads): ?>
									<?php $arr = json_decode($ads['json_photos'], true); ?>
									<?php if ($arr['main']): ?>
										<div class="info-item">
											<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>" class="info-descr">
												<div class="img-wrap">
													<img src="https://feo.ua/app_adv/engine/upload/photos/<?=$arr['main']['id'];?>.<?=$arr['main']['ext'];?>">
												</div>
											</a>
											<div class="content">
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>" class="info-descr">
													<span><?=$ads['name'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $ads['date']);?>" title="<?=date('Y-m-d H:i:s', $ads['date']);?>"></time>
												</div>
												<div class="views">
													<span class="icon-eye"></span><span class="text-views"><?=$ads['view_count'];?></span>
												</div>
											</div>
										</div>
									<?php endif; ?>
                                <?php endforeach; ?>
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="title-block-slider">
								<h2>Работа <?=$this->data['city_name'];?></h2>
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/group_13/" class="link-1">Все объявления</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-5"></div>
									<div class="rightArrow" data-slider-id="slider-5"></div>
								</div>
								<a href="#" class="link-2 hidden-sm hidden-xs">Добавить объявление</a>
							</div>
							<div class="slider-style-3 info-block-verticale" id="slider-5">
                                <?php foreach ($this->data['getAdsWork'] as $ads): ?>
                                <div class="info-item">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>" class="info-descr">
                                        <div class="img-wrap">
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
                                        </div>
                                    </a>
                                    <div class="content">
                                        <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/ads/i<?=$ads['id'];?>" class="info-descr">
                                            <span><?=$ads['name'];?></span>
                                        </a>
                                        <div class="time">
                                            <span class="icon-clock"></span><time class="text-clock" datetime="<?=date('Y-m-d H:i:s', $ads['date']);?>" title="<?=date('Y-m-d H:i:s', $ads['date']);?>"></time>
                                        </div>
                                        <div class="views">
                                            <span class="icon-eye"></span><span class="text-views"><?=$ads['view_count'];?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
							</div>
						</div>
						<div class="col-md-8 col-sm-12 col-xs-12">
							<div class="title-block-slider">
								<h2>Веб-камеры</h2>
								<a href="#" class="link-1">Все камеры</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-6"></div>
									<div class="rightArrow" data-slider-id="slider-6"></div>
								</div>
							</div>
							<div class="slider-style-4" id="slider-6">
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 hidden-sm hidden-xs">
							<div class="blue-box links">
								<div class="blue-title">
									<h2>Отдых <?=$this->data['city_name'];?></h2>
								</div>
								<div class="blue-content">
									<div class="links-list">
										<ul>
											<li>
												<a href="#">
													Отдых в Феодосии ООО Ника-Тур, Туристическая фирма
												</a>
											</li>
											<li>
												<a href="#">
													Гостевой дом Крепость Кафа Кафе Праванс
												</a>
											</li>
											<li>
												<a href="#">
													Медицинский центр Здоровье Кафе Лукоморье
												</a>
											</li>
											<li>
												<a href="#">
													Ресторанчик Велес, м-н Крендель, гостевые комнаты
												</a>
											</li>
											<li>
												<a href="#">
													Золотой Грифон. Гостевой дом. Пивной Паб Пивляндия
												</a>
											</li>
											<li>
												<a href="#">
													Отдых в Феодосии ООО Ника-Тур, Туристическая фирма
												</a>
											</li>
											<li>
												<a href="#">
													Гостевой дом Крепость Кафа Кафе Праванс
												</a>
											</li>
											<li>
												<a href="#">
													Медицинский центр Здоровье Кафе Лукоморье
												</a>
											</li>
											<li>
												<a href="#">
													Ресторанчик Велес, м-н Крендель, гостевые комнаты
												</a>
											</li>
											<li>
												<a href="#">
													Золотой Грифон. Гостевой дом. Пивной Паб Пивляндия
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8 col-sm-12 col-xs-12">
							<div class="title-block-slider">
								<h2>Видео <?=$this->data['city_name'];?></h2>
								<a href="#" class="link-1">Все видео</a>
								<div class="sliderarrowsmain hidden-xs">
									<div class="leftArrow" data-slider-id="slider-7"></div>
									<div class="rightArrow" data-slider-id="slider-7"></div>
								</div>
							</div>
							<div class="slider-style-4" id="slider-7">
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
								<div class="col-md-12">
									<div class="item">
										<a href="#">
											<div class="img-wrap">
												<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/cams/item-1.jpg" alt="alt">
											</div>
											<div class="descr">
												<span>Набережная</span>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 hidden-sm hidden-xs">
							<div class="title-block">
								<h2>Реклама</h2>
							</div>
							<div class="vertical-banners">
								<div class="banner-wrap">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<?=do_shortcode('[sidebar_adv]');?>
				</div>
			</div>
		</section>
	</div>
</main>