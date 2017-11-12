			<div class="sectcenter">
				<h2>Управление</h2>
				<ul>
					<li>
						<a href="#" class="dropdown-button" data-ajax="true"  id="1">Пользователи</a>
						<ul class="dropdown-list-1">
							<li class="sectcenter-li">
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/admin/control/users/" class="ajax-load" data-center="false" id="1-1">Все пользователи</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="#" class="dropdown-button" data-ajax="true"  id="2">Страницы</a>
						<ul class="dropdown-list-2">
							<li class="sectcenter-li">
								<a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/admin/control/pages/" class="ajax-load" data-center="false" id="2-1">Все страницы</a>
							</li>
						</ul>
					</li>
				</ul>
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
				<!--
				<div class="sectright-filters">
					<nav class="sectright-filters-nav">
						<ul class="sectright-filters-nav-menu">
							<li class="sectright-filters-nav-menu-li"><a href="#">Фильтр</a></li>
							<li class="sectright-filters-nav-menu-li"><a href="#">Раздел</a></li>
							<li class="sectright-filters-nav-menu-li"><a href="#">Таб</a></li>
							<li class="sectright-filters-nav-menu-li"><a href="#"><i class="fa fa-plus" aria-hidden="true"></i></a></li>
						</ul>
					</nav>
					<form class="sectright-filters-form">
						<div class="sectright-filters-form-label">
							<label>
								<input class="filter-input" type="text" placeholder="Название">
							</label>
						</div>
						<div class="sectright-filters-form-label">
							<select class="filter-select">
								<option value="Раздел">Раздел</option>
								<option value="Раздел1">Раздел1</option>
								<option value="Раздел2">Раздел2</option>
							</select>
						</div>
						<div class="button-wrap">
							<a href="#" class="button">Найти</a>
						</div>
						<div class="button-wrap">
							<a href="#" class="button">Отменить</a>
						</div>
						<div class="right">
							<div class="button-wrap">
								<a href="#" class="button"><i class="fa fa-cog" aria-hidden="true"></i></a>
							</div>
							<div class="button-wrap">
								<a href="#" class="button"><i class="fa fa-plus" aria-hidden="true"></i></a>
							</div>
						</div>
						
					</form>
				</div>
				-->
				<!--
				<div class="sectright-table">
					<div class="button-wrap-inverse">
						<a href="#" class="button"><span><i class="fa fa-plus" aria-hidden="true"></i></span>Создать товар</a>
					</div>
					<form class="sectright-table-form">
						<select class="filter-select">
							<option value="Раздел">Раздел</option>
							<option value="Раздел1">Раздел1</option>
							<option value="Раздел2">Раздел2</option>
						</select>
					</form>
					<div class="button-wrap">
						<a href="#" class="button">Добавить</a>
					</div>
					<div class="right">
						<div class="button-wrap">
							<a href="#" class="button"><i class="fa fa-cog" aria-hidden="true"></i></a>
						</div>
					</div>
					<div class="sectright-table-content">
						<table class="table-adapt">
						<thead>
							<tr>
								<th><input type="checkbox" class="check-all" data-name="numbers[]"></th>
								<th class="left-head"><p>Название</p></th>
								<th data-breakpoints="xs sm"><p>Активность</p></th>
								<th data-breakpoints="xs"><p>Сорт.</p></th>
								<th data-breakpoints="xs"><p>Дата изменения</p></th>
								<th data-breakpoints="xs"><p>ID</p></th>
							</tr>
						</thead>
						<tbody>	
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="0"></td>
								<td class="left-content"><p>Улучшенный чизбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="1"></td>
								<td class="left-content"><p>Гамбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="2"></td>
								<td class="left-content"><p>Дабл гамбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="3"></td>
								<td class="left-content"><p>Чизбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="4"></td>
								<td class="left-content"><p>Картофель фри</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="5"></td>
								<td class="left-content"><p>Дабл чизбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
							<tr>
								<td><input type="checkbox" name="numbers[]" class="mc" value="6"></td>
								<td class="left-content"><p>Чикенбургер</p></td>
								<td><p>Да</p></td>
								<td><p>120</p></td>
								<td><p>17.04.2017 10:21:06</p></td>
								<td><p><span>743</span></p></td>
							</tr>
						</tbody>
						</table>
					</div>
				</div>
				-->
			</div>
