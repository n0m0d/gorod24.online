<main>
	<div class="container main-news-page">
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
		<div class="title-block" style="margin-bottom: 0;">
			<h2>Новости Феодосии</h2>
            <div class="breadcrumbs" style="/*background-color: #fff; padding: 15px; margin-bottom: 20px;*/ display: inline-block;">
                <?php
				$breadcrumbs_last_templ = new Template('<p>{#name#}<p>');
                    $breadcrumbs_templ = new Template('<a href="{#breadcrumb.href#}">{#breadcrumb.name#}</a><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>');

                    if (is_array($this->data['breadcrumbs'])) foreach($this->data['breadcrumbs'] as $name => $url)
                    {
                    if ($url == end($this->data['breadcrumbs']))
                    {
                    $breadcrumbs_last_templ->reset();
                    $breadcrumbs_last_templ->setVar('name', $name);
                    echo $breadcrumbs_last_templ;
                    }
                    else
                    {
                    $breadcrumbs_templ->reset();
                    $breadcrumbs_templ->setObject('breadcrumb', [ 'href' => $url, 'name' => $name, ]);
                    echo $breadcrumbs_templ;
                    }
                    }
                    ?>
            </div>
		</div>
		<!--<div class="breadcrumbs" style="background-color: #fff; padding: 15px;">
			<?php
				$breadcrumbs_last_templ = new Template('<p>{#name#}<p>');
				$breadcrumbs_templ = new Template('<a href="{#breadcrumb.href#}">{#breadcrumb.name#}</a><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>');

				if (is_array($this->data['breadcrumbs'])) foreach($this->data['breadcrumbs'] as $name => $url)
				{
				if ($url == end($this->data['breadcrumbs']))
				{
				$breadcrumbs_last_templ->reset();
				$breadcrumbs_last_templ->setVar('name', $name);
				echo $breadcrumbs_last_templ;
				}
				else
				{
				$breadcrumbs_templ->reset();
				$breadcrumbs_templ->setObject('breadcrumb', [ 'href' => $url, 'name' => $name, ]);
				echo $breadcrumbs_templ;
				}
				}
				?>
		</div>-->
		<section class="section-1">
			<div class="row">
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<aside>
						<div class="read-more">
							<h2>Читать так-же</h2>
							<?php foreach ((array) $this->data['getReadMoreNews'] as $news): ?>
							<div class="item-wrap">
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="item">
									<?=$news['news_head'];?>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
						<div class="vertical-banners">
							<div class="banner-wrap">
								<a href="#">
									<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-6.jpg" alt="alt">
								</a>
							</div>
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
				<div class="col-md-6">
					<div class="post-wrap">
						<div class="tags">
							<?php
                                $tags = str_replace(";", "", explode("#",$this->data['getNews']['news_tag']));
								$flag = true;
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
						<h1>
							<?=$this->data['getNews']['news_head'];?>
						</h1>
						<div class="time">
							<span class="icon-clock"></span><time class="text-clock" datetime="<?=$this->data['getNews']['news_date'];?>" title="<?=$this->data['getNews']['news_date'];?>"></time>
						</div>
						<div class="views">
							<span class="icon-eye"></span><span class="text-views"><?=$this->data['getNews']['looks'];?></span>
						</div>
						<div class="fotorama" data-allowfullscreen="true" data-click="true" data-nav="thumbs" data-loop="true" data-width="100%" data-max-height="400px" data-fit="cover">
							<?php
								if(count($this->data['getNewsImages']) > 0)
								{
									foreach($this->data['getNewsImages'] AS $img)
									{
										echo '<img src="'.$img['original'].'" alt="'.$img['title'].'">';
									}
								}
								else
								{
									echo '<img src="'.$this->data['getNewsImages'][0]['original'].'" alt="'.$this->data['getNewsImages'][0]['title'].'">';
								}
							?>
						</div>
						<?=do_shortcode('[yashare]');?>
						<div class="content-wrap">
							<?php
								$str = stripslashes($this->data['getNews']['news_body']);
								echo str_replace("rn", "", $str);

								if ($this->data['getNews']['news_panorama'])
								{
									echo do_shortcode('[video url="'.$this->data['getNews']['news_panorama'].'"]');
								}

								if ($this->data['getNews']['news_video_you'])
								{
									echo do_shortcode('[video url="'.$this->data['getNews']['news_video_you'].'"]');
								}

                                if ($this->data['getNews']['news_zamer_id'])
                                {
                                    echo $this->merchandise->merchandise_vidget($this->data['getNews']['news_zamer_id']);
                                }
							?>
						</div>
                        <div class="interviews-wrap">
                            <?php
                                if ($this->data['getNews']['news_inter_id'])
                                {
                                    echo $this->interviews->get_interview_vidget($this->data['getNews']['news_inter_id']);
                                }
                            ?>
                        </div>
					</div>
					<div class="banner-wrap">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/banners/banner-2.jpg" alt="alt">
						</a>
					</div>
					<div class="comments-init">
						<?=$this->comments->comments_rend(array("com_for_table" => "gorod_news", "com_for_column" => "id", "com_main_id" => $this->data['news_parent_id']));?>
					</div>
					<div class="news-today">
						<h2>Новости за сегодня:</h2>
						<ul>
							<?php foreach ((array) $this->data['getNewsToday'] as $news): ?>
							<li><span><?=substr($news['news_date'],11,5);?></span><a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>"><?=$news['news_head']?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<aside>
						<?=$this->comments->lastcomments_vidget();?>
						<?=do_shortcode('[sidebar_adv]');?>
					</aside>
				</div>
			</div>
		</section>
	</div>
</main>