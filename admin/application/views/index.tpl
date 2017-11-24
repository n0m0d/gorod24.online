			<div class="sectcenter">
				<h2><?=$this->data['main']['header']?></h2>
				<?php
				
			function mainMenuRender($items, $level=0){
				$ul_tmpl = new Template('<ul class="dropdown-list-{#id#}">{#items#}</ul>');
				$li_tmpl = new Template('<li class="{#li_class#}"><a href="{#url#}" {#data#} class="{#a_class#}" data-ajax="true" id="{#id#}">{#title#}</a>{#items#}</li>');
				if(is_array($items)){
					$ul_tmpl->reset();
					$ul_tmpl->setVar('id', $level);
					foreach($items as $i=>$item){
						$li_tmpl->reset();
						$id=uniqid();
						$li_tmpl->setVar('url', $item['url'])->setVar('title', $item['title'])->setVar('id', $id);
						if(!empty($item['items'])) { 
							$li_tmpl->setVar('li_class', '')->setVar('a_class', 'dropdown-button')->setVar('items', mainMenuRender($item['items'], $id)); 
						} 
						else {
							$li_tmpl->setVar('li_class', 'sectcenter-li')->setVar('a_class', 'ajax-load')->setVar('data', 'data-center="false"'); 
						}
						$ul_tmpl->addVar('items', $li_tmpl);
					}
					return $ul_tmpl;
				}
			}
				echo mainMenuRender($this->data['main']['menu']);
				
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
