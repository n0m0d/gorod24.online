<main>
    <div class="container main-add-asd-page">
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
                    <h2>Шаг: 2 Информация объявления</h2>
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
                <div class="col-md-6 col-sm-12 add-asd-page-content-center-box">
                    <div class="add-asd-page-wrap">
                        <div class="prev-form-info">
                            <p><span>Ваше имя: </span><?=$this->data['adv_info']['user_name'];?></p>
                            <p><span>Ваш e-mail: </span><?=$this->data['adv_info']['user_email'];?></p>
                            <p><span>Ваш телефон: </span><?=$this->data['adv_info']['user_phone'];?></p>
                            <p><span>Ваш регион: </span><?=$this->data['adv_info']['region'];?></p>
                            <p><span>Ваш город: </span><?=$this->data['adv_info']['city'];?></p>
                        </div>
                        <form method="POST" id="add-form-2" class="adsaddform">
                            <div class="input-wrap requared-input">
                                <span class="icon-wrap"><span class="icon-docs"></span></span>
                                <select name="cat" id="region-select" class="select-filter" required>
                                    <option value="">Выбрать рубрику</option>
                                    <?php foreach ($this->data['getCats'] as $item): ?>
                                    <option value="<?=$item['id'];?>"><?=$item['name'];?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-wrap requared-input" id="filters-load">
                                <span class="icon-wrap"><span class="icon-doc"></span></span>
                                <select name="sub_cat" class="select-filter" id="cats-select" required>
                                    <option value="">Выбрать категорию</option>
                                </select>
                            </div>
                            <div class="dop-filters-container">

                            </div>
                            <div class="input-wrap requared-input">
                                <label>
                                    <span class="icon-wrap"><span class="icon-pencil"></span></span>
                                    <input type="text" name="title" placeholder="Заголовок" required>
                                </label>
                            </div>
                            <div class="input-wrap input-wrap-big">
                                <label>
                                    <span class="icon-wrap"><span class="icon-note"></span></span>
                                    <textarea name="descr" placeholder="Описание" style="width: 100%"></textarea>
                                </label>
                            </div>
                            <div class="input-wrap">
                                <label>
                                    <span class="icon-wrap"><span class="icon-home"></span></span>
                                    <input type="text" name="site" placeholder="Адрес в интернете (без http://)">
                                </label>
                            </div>
                            <div class="input-wrap-date">
                                <select name="price-from-to" class="select-filter">
                                    <option value="">---</option>
                                    <option value="1">От</option>
                                    <option value="2">До</option>
                                </select>
                                <label>
                                    <input type="text" name="price" placeholder="Цена" required>
                                </label>
                                <select name="price-currency" class="select-filter">
                                    <option value="Руб.">Руб</option>
                                </select>
                                <select name="price-izm" class="select-filter">
                                    <option value="">---</option>
                                    <?php foreach ($this->data['edizm'] as $item): ?>
                                    <option value="<?=$item['id'];?>"><?=$item['name'];?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="check-filters-wrap">
                                    <label class="check-filter">
                                        <span class="info-lalbel">Договорная</span>
                                        <input type="checkbox" name="price-discuse">
                                    </label>
                                    <label class="check-filter">
                                        <span class="info-lalbel">Даром</span>
                                        <input type="checkbox" name="price-free">
                                    </label>
                                </div>
                            </div>
                            <div class="input-wrap">
                                <label>
                                    <span class="icon-wrap"><span class="icon-diamond"></span></span>
                                    <input type="text" name="vip_code" placeholder="VIP код">
                                </label>
                            </div>
                            <div class="input-wrap">
                                <label>
                                    <span class="icon-wrap"><span class="icon-picture"></span></span>
                                    <input type="file" name="photos" title="Фотографии" accept="image/*" multiple>
                                </label>
                            </div>
                            <div class="input-wrap">
                                <label>
                                    <span class="icon-wrap"><span class="icon-camrecorder"></span></span>
                                    <input type="text" name="video" placeholder="Ссылка на видео">
                                </label>
                            </div>
                            <div class="button-wrap-filter">
                                <input type="hidden" name="adv_id" value="<?=$this->data['adv_info']['id'];?>">
                                <button id="submit-form-2" class="button" type="submit">Далее</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 hidden-sm hidden-xs ads-aside-right-box block-sticker-wrap">
                    <aside>
                        <?=do_shortcode('[sidebar_adv]');?>
                    </aside>
                </div>
            </div>
        </section>
    </div>
</main>