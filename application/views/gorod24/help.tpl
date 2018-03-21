<style>
h2 {
    color: #000;
    text-transform: uppercase;
    font-family: intro;
    font-size: 28px;
    line-height: 45px;
    margin-top: 15px;
    margin-bottom: 35px;
}
.sections {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    width: 100%;
    height: calc(100vh - 75px);
    /*height: 100%;*/
}
.sectcenter {
    -webkit-box-flex: 1;
    -webkit-flex: 1 0 240px;
    -ms-flex: 1 0 240px;
    flex: 1 0 240px;
    background-color: #fff;
    padding: 15px 25px;
	overflow-y: auto;
}
.sectright {
    background-color: #f7faff;
    -webkit-box-flex: 1;
    -webkit-flex: 1 1 100%;
    -ms-flex: 1 1 100%;
    flex: 1 1 75%;
    padding: 15px 25px;
    overflow: auto;
}

.sectcenter ul{margin:0;padding:25px 0 0 20px}
.sectcenter ul li{display:block;padding:0 0 20px 0;position:relative}
.sectcenter ul li a,.sectcenter ul li p{text-decoration:none;cursor:pointer;display:block;font-family:Roboto-Medium,sans-serif;color:#373e48;line-height:14px}
.sectcenter ul li a::before,.sectcenter ul li p::before{content:"\f105";display:block;font-family:FontAwesome;color:#59c2e6;position:absolute;left:-15px;top:0;font-weight:700;line-height:1;-webkit-transition:all .25s ease;transition:all .25s ease}
.sectcenter ul li ul{padding:20px 0 0 15px;display:none}
.sectcenter ul li ul .sectcenter-li{float:none;width:100%;padding:0;margin:0;position:relative;padding-bottom:20px}
.sectcenter ul li ul .sectcenter-li a,.sectcenter ul li ul .sectcenter-li p{margin:0;font-family:Roboto-Light,sans-serif;color:#797979}
.sectcenter ul li ul .sectcenter-li a::before,.sectcenter ul li ul .sectcenter-li p::before{content:"\f105";display:block;font-family:FontAwesome;color:#59c2e6;position:absolute;left:-15px;top:0;font-weight:700;line-height:1;-webkit-transition:all .25s ease;transition:all .25s ease}
.sectcenter ul li ul .sectcenter-li ul{display:none}
.sectcenter ul li ul .sectcenter-li ul li{position:relative}
.sectcenter ul li ul .sectcenter-li ul li:last-child{padding-bottom:0}
.sectcenter ul li ul .sectcenter-li ul li a::before,.sectcenter ul li ul .sectcenter-li ul li p::before{content:'';width:5px;height:5px;background-color:#59c2e6;display:block;position:absolute;left:-15px;top:4px;-webkit-border-radius:5em;border-radius:5em}

.scrollup {
    width: 40px;
    height: 40px;
    opacity: 0.3;
    position: fixed;
    bottom: 50px;
    right: 100px;
    display: none;
    text-indent: -9999px;
    background: url(/admin/application/views/img/icon_top.png) no-repeat;
	cursor:pointer;
}

@media screen and (max-width: 992px){
	.sections {
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-webkit-flex-direction: column;
		-ms-flex-direction: column;
		flex-direction: column;
	}
	.sectcenter {
		height: auto;
		-webkit-box-flex: unset;
		-webkit-flex: none;
		flex: none;
	}

}


</style>
<script type="text/javascript">
var top_show = 150; // В каком положении полосы прокрутки начинать показ кнопки "Наверх"
var delay = 500; // Задержка прокрутки
$(function() {
	
	$(".sectright").scroll(function () { 
		if ($(this).scrollTop() > top_show) $('.scrollup').fadeIn();
		else $('.scrollup').fadeOut();
    });
	
    $('.scrollup').click(function () {$('.sectright').animate({scrollTop: 0}, delay);});
	
	$(document).on("click", '.dropdown-button', function(e) {
		e.preventDefault();
		$(this).toggleClass('changed');
		var id = $(this).attr("id");
		$(".dropdown-list-" + id).slideToggle('fast');

	});

	$(document).on("click", '.indropdown-button', function() {
		$(this).toggleClass('changed');
		var id = $(this).attr("id");
		$(".indropdown-list-" + id).slideToggle('fast');
	});
	

});
</script>
	<div class="sections">
	<div class="sectcenter">
		<h2><?=$this->data['main']['header']?></h2>
		<?php
		
	function mainMenuRender($items, $level=0, $open=false){
		$ul_tmpl = new Template('<ul class="dropdown-list-{#id#}" style="display:{#style#};" >{#items#}</ul>');
		$li_tmpl = new Template('<li class="{#li_class#}"><a href="{#url#}" {#data#} class="{#a_class#}" data-ajax="true" id="{#id#}">{#title#}</a>{#items#}</li>');
		if(is_array($items)){
			$ul_tmpl->reset();
			if($open or $level==0) $ul_tmpl->setVar('style', 'block'); else $ul_tmpl->setVar('style', 'none');
			$ul_tmpl->setVar('id', $level);
			foreach($items as $i=>$item){
				$li_tmpl->reset();
				$id=uniqid();
				$li_tmpl->setVar('url', $item['url'])->setVar('title', $item['title'])->setVar('id', $id);
				$open = ($item['selected']?true:false);
				if(!empty($item['items'])) { 
					$li_tmpl->setVar('li_class', '')->setVar('a_class', 'dropdown-button')->setVar('items', mainMenuRender($item['items'], $id, $open)); 
				} 
				else {
					$li_tmpl->setVar('li_class', 'sectcenter-li')->setVar('a_class', 'ajax-load')->setVar('data', 'data-center="false"'); 
				}
				$ul_tmpl->addVar('items', $li_tmpl);
			}
			return $ul_tmpl;
		}
	}
		if(!empty($this->data['main']['menu'])){
			echo mainMenuRender($this->data['main']['menu']);
		}
		
		?>
	</div>
	
	<div class="sectright">

		<div class="sectright-breadcrumbs">
			<?php
			$breadcrumbs_last_templ = new Template('<p>{#name#}<p>');
			$breadcrumbs_templ = new Template('<a href="{#breadcrumb.href#}" class="ajax-load" data-center="false">{#breadcrumb.name#}</a><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>');
			
			if(is_array($this->data['breadcrumbs']))foreach($this->data['breadcrumbs'] as $name=>$url){
				if ($url == end($this->data['breadcrumbs'])) {
					$breadcrumbs_last_templ->reset();
					$breadcrumbs_last_templ->setVar('name', $name);
					echo $breadcrumbs_last_templ;
				}
				else {
					$breadcrumbs_templ->reset();
					$breadcrumbs_templ->setObject('breadcrumb', [ 'href' => $url, 'name' => $name, ]);
					echo $breadcrumbs_templ;
				}
			}
			?>
		</div>
		<h2><?=$this->data['header']?></h2>
		<div class="sectright-table">
		<?=$this->data['content']?>
		</div>
	</div>
	<div class="scrollup"></div>
	</div>
