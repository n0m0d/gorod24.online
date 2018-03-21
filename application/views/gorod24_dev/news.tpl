<main>
	<div class="container main-news">
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
			<h2>Новости <?=$this->data['city_name'];?></h2>
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

		<section class="section-1">
			<div class="grid">
				<div class="grid-sizer"></div>
				<?=$this->data['getLastNews'];?>
			</div>
			<div class="pagination-wrap">
				<?=$this->data['paginator']?>
			</div>
		</section>

		<!--<section class="section-1">
			<div class="row">
				<div class="col-md-6 col-sm-4 hidden-xs" id="news-items-block">
					<div class="info-block-horizontale">
						<?php foreach ((array) array_slice($this->data['getLastNews'], 0, 1) as $key => $news): ?>
						<div class="info-item">
							<div class="row">
								<div class="col-md-5">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                        <div class="img-wrap">
											<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                        </div>
                                    </a>
								</div>
								<div class="col-md-7">
									<div class="content">
										<div class="tags">
											<?php
                            					$tags = explode("#",$news['news_tag']);
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
										<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
											<span><?=$news['news_head'];?></span>
										</a>
										<div class="time">
											<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
										</div>
										<div class="right-block">
											<div class="photos">
												<i class="fa fa-camera" aria-hidden="true"></i></span>
                                                <span class="text-photos">
													<?=$news['photos_sum'];?>
                                                </span>
											</div>
											<div class="comments">
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
                        <?php foreach ((array) array_slice($this->data['getLastNews'], 1, 3) as $key => $news): ?>
						<div class="info-item hidden-sm hidden-xs">
							<div class="row">
								<div class="col-md-5">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                        <div class="img-wrap">
											<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                        </div>
                                    </a>
								</div>
								<div class="col-md-7">
									<div class="content">
                                        <div class="tags">
											<?php
                            					$tags = explode("#",$news['news_tag']);
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
										<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
											<span><?=$news['news_head'];?></span>
										</a>
										<div class="time">
											<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
										</div>
										<div class="right-block">
                                            <div class="photos">
                                                <i class="fa fa-camera" aria-hidden="true"></i></span>
                                                <span class="text-photos">
                                                    <?=$news['photos_sum'];?>
                                                </span>
                                            </div>
											<div class="comments">
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
				<div class="col-md-6 col-sm-8 col-xs-12 left-slider-col">
					<div class="main-slider">
                        <?php foreach ((array) $this->data['getNewsSlider'] as $news): ?>
                        <div>
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>">
								<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_1280_1024.jpg" alt="alt">
                                <div class="descr">
                                    <div class="row">
                                        <div class="col-sm-9">
											<div class="wrap">
												<h1><?=$news['news_head'];?></h1>
												<p class="hidden-xs"><?=$news['news_lid'];?></p>
											</div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="time">
                                                <span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
					</div>
				</div>
                <?php foreach ((array) array_slice($this->data['getTopNews'], 0, 2) as $key => $news): ?>
				<div class="col-md-3 col-sm-4 col-xs-12 info-item-wrap">
					<div class="info-block-verticale">
						<div class="info-item">
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                <div class="img-wrap">
									<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                </div>
                            </a>
							<div class="content">
                                <div class="tags">
									<?php
                            			$tags = explode("#",$news['news_tag']);
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
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
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
                <?php foreach ((array) array_slice($this->data['getTopNews'], 2, 1) as $key => $news): ?>
				<div class="col-md-3 col-sm-4 col-xs-12 hidden-lg hidden-md info-item-wrap">
					<div class="info-block-verticale">
						<div class="info-item">
                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                <div class="img-wrap">
									<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                </div>
                            </a>
							<div class="content">
                                <div class="tags">
									<?php
                            			$tags = explode("#",$news['news_tag']);
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
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
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
		</section>
		<div class="main-banners">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-wrap">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/banners/banner-2.jpg" alt="alt">
						</a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="banner-wrap">
						<a href="#">
							<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/banners/banner-3.jpg" alt="alt">
						</a>
					</div>
				</div>
			</div>
		</div>
		<section class="section-2">
			<div class="row">
				<div class="col-md-9 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-md-8 col-sm-4 col-xs-12">
							<div class="info-block-horizontale">
                                <?php foreach ((array) array_slice($this->data['getLastNews'], 4, 1) as $key => $news): ?>
								<div class="info-item">
									<div class="row">
										<div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                                <div class="img-wrap">
													<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
										</div>
										<div class="col-md-7">
											<div class="content">
                                                <div class="tags">
													<?php
														$tags = explode("#",$news['news_tag']);
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
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
													<span><?=$news['news_head'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
												</div>
												<div class="right-block">
                                                    <div class="photos">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span>
                                                        <span class="text-photos">
                                                            <?=$news['photos_sum'];?>
                                                        </span>
                                                    </div>
													<div class="comments">
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
                                <?php foreach ((array) array_slice($this->data['getLastNews'], 5, 3) as $key => $news): ?>
								<div class="info-item hidden-sm hidden-xs">
									<div class="row">
										<div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                                <div class="img-wrap">
													<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
										</div>
										<div class="col-md-7">
											<div class="content">
                                                <div class="tags">
													<?php
														$tags = explode("#",$news['news_tag']);
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
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
													<span><?=$news['news_head'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
												</div>
												<div class="right-block">
                                                    <div class="photos">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span>
                                                        <span class="text-photos">
                                                            <?=$news['photos_sum'];?>
                                                        </span>
                                                    </div>
													<div class="comments">
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

                        <?php foreach ((array) array_slice($this->data['getTopNews'], 3, 2) as $key => $news): ?>
						<div class="col-md-4 col-sm-4 col-xs-12 info-item-wrap">
							<div class="info-block-verticale">
								<div class="info-item">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                        <div class="img-wrap">
											<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                        </div>
                                    </a>
									<div class="content">
                                        <div class="tags">
											<?php
                            					$tags = explode("#",$news['news_tag']);
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
										<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
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
					<div class="main-banners">
						<div class="row">
							<div class="col-md-8">
								<div class="banner-wrap banner-wrap-80">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/banners/banner-2.jpg" alt="alt">
									</a>
								</div>
							</div>
                            <div class="col-md-4 hidden-sm hidden-xs">
                                <div class="banner-wrap banner-wrap-80" style="height: 80px; background-color: #fff;"></div>
                            </div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-8 col-sm-4 col-xs-12">
							<div class="info-block-horizontale">
                                <?php foreach ((array) array_slice($this->data['getLastNews'], 8, 1) as $key => $news): ?>
								<div class="info-item">
									<div class="row">
										<div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                                <div class="img-wrap">
													<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
										</div>
										<div class="col-md-7">
											<div class="content">
                                                <div class="tags">
													<?php
														$tags = explode("#",$news['news_tag']);
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
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
													<span><?=$news['news_head'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
												</div>
												<div class="right-block">
                                                    <div class="photos">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span>
                                                        <span class="text-photos">
                                                            <?=$news['photos_sum'];?>
                                                        </span>
                                                    </div>
													<div class="comments">
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
                                <?php foreach ((array) array_slice($this->data['getLastNews'], 9, 3) as $key => $news): ?>
								<div class="info-item hidden-sm hidden-xs">
									<div class="row">
										<div class="col-md-5">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                                <div class="img-wrap">
													<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                                </div>
                                            </a>
										</div>
										<div class="col-md-7">
											<div class="content">
                                                <div class="tags">
													<?php
														$tags = explode("#",$news['news_tag']);
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
												<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
													<span><?=$news['news_head'];?></span>
												</a>
												<div class="time">
													<span class="icon-clock"></span><time class="text-clock" datetime="<?=$news['news_date'];?>" title="<?=$news['news_date'];?>"></time>
												</div>
												<div class="right-block">
                                                    <div class="photos">
                                                        <i class="fa fa-camera" aria-hidden="true"></i></span>
                                                        <span class="text-photos">
                                                            <?=$news['photos_sum'];?>
                                                        </span>
                                                    </div>
													<div class="comments">
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
                        <?php foreach ((array) array_slice($this->data['getTopNews'], 5, 1) as $key => $news): ?>
						<div class="col-md-4 col-sm-4 col-xs-12 info-item-wrap">
							<div class="info-block-verticale">
								<div class="info-item">
                                    <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
                                        <div class="img-wrap">
											<img src="https://gorod24.online/thrumbs/news/new_<?=$news['id'];?>_361_240.jpg" alt="alt">
                                        </div>
                                    </a>
									<div class="content">
                                        <div class="tags">
											<?php
                            					$tags = explode("#",$news['news_tag']);
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
										<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST'].'/news/'.$news['url']?>" class="info-descr">
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
						<div class="col-md-4 col-sm-4 col-xs-12">
							<div class="vertical-banners">
								<div class="banner-wrap">
									<a href="#">
										<img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24/img/banners/banner-6.jpg" alt="alt">
									</a>
								</div>
							</div>
						</div>
                        <div class="clearfix"></div>
                        <div class="pagination-wrap">
                            <?=$this->data['paginator']?>
                        </div>
					</div>
				</div>
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<?=$this->comments->lastcomments_vidget();?>
					<?=do_shortcode('[sidebar_adv]');?>
				</div>
			</div>
		</section>-->
	</div>
</main>