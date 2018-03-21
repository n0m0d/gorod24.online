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
                    <h2>Шаг: 3 Опубликовать объявление в газете</h2>
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
                <div class="col-md-9 col-sm-12 add-asd-page-content-center-box">
                    <div class="add-asd-page-wrap">
                        <div class="prev-form-info">
                            <p><span>Ваше имя: </span><?=$this->data['adv_info']['user_name'];?></p>
                            <p><span>Ваш e-mail: </span><?=$this->data['adv_info']['user_email'];?></p>
                            <p><span>Ваш телефон: </span><?=$this->data['adv_info']['user_phone'];?></p>
                            <p><span>Ваш регион: </span><?=$this->data['adv_info']['region'];?></p>
                            <p><span>Ваш город: </span><?=$this->data['adv_info']['city'];?></p>
                        </div>
                        <form method="POST" id="add-form-3" class="adsaddform">
                            <span class="warning">
                                Не пишите в описании цену и контакты. Объявления будут удалены. Максимально слов 10 символов 180.
                            </span>
                            <div class="input-wrap input-wrap-big">
                                <label>
                                    <span class="icon-wrap"><span class="icon-note"></span></span>
                                    <textarea name="gazeta_text" placeholder="Текст объявления в газете" style="width: 100%"></textarea>
                                </label>
                            </div>
                            <div class="input-wrap">
                                <label class="check-filter">
                                    <input type="checkbox" name="gazeta_1" class="gazeta-check" data-id="101">
                                    <span class="info-lalbel">#10, Дата выхода 2018-03-19, Понедельник</span>
                                </label>
                                <label class="check-filter">
                                    <input type="checkbox" name="gazeta_2" class="gazeta-check" data-id="102">
                                    <span class="info-lalbel">#11, Дата выхода 2018-03-26, Понедельник</span>
                                </label>
                            </div>
                            <div class="button-wrap-filter">
                                <input type="hidden" name="adv_id" value="<?=$this->data['adv_info']['id'];?>">
                                <button id="submit-form-3" class="button" type="submit">Далее</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>