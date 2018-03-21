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
		<div class="title-block">
			<h2>Мнения</h2>
		</div>
		<div class="breadcrumbs" style="background-color: #fff; padding: 15px; margin-top: 20px;">
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
					<div class="comments-init">
						<?=$this->comments->comments_opinions(array(0,20));?>
					</div>
				</div>
				<div class="col-md-3 hidden-sm hidden-xs block-sticker-wrap">
					<aside>
						<?=do_shortcode('[sidebar_adv]');?>
					</aside>
				</div>
			</div>
		</section>
	</div>
</main>