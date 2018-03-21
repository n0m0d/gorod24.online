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
                    <h2>Шаг: 1 Контактные данные</h2>
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
                        <form method="POST" id="add-form-1" class="adsaddform">
                            <div class="input-wrap input-wrap-pol requared-input">
                                <label>
                                    <span class="icon-wrap"><span class="icon-envelope"></span></span>
                                    <input type="email" name="email" placeholder="E-mail" value="<?=$this->data['user_email'];?>" required>
                                </label>
                            </div>
                            <div class="input-wrap input-wrap-descr">
                                <label class="check-filter">
                                    <span class="info-lalbel">Показывать e-mail</span>
                                    <input type="checkbox" name="email_show">
                                </label>
                            </div>
                            <div class="input-wrap requared-input">
                                <label>
                                    <span class="icon-wrap"><span class="icon-user"></span></span>
                                    <input type="text" name="name" placeholder="Имя и Фамилия" value="<?=$this->data['user_name'];?>" required>
                                </label>
                            </div>
                            <div class="input-wrap requared-input">
                                <label>
                                    <span class="icon-wrap"><span class="icon-phone"></span></span>
                                    <input type="text" id="form-add-phone" name="phone" placeholder="Телефон" value="<?=$this->data['user_phone'];?>" required>
                                </label>
                            </div>
                            <div class="input-wrap requared-input">
                                <span class="icon-wrap"><span class="icon-globe"></span></span>
                                <select name="region" class="select-filter" required>
                                    <option value="">Выбрать регион</option>
                                    <option value="261">Крым</option>
                                </select>
                            </div>
                            <div class="input-wrap requared-input">
                                <span class="icon-wrap"><span class="icon-location-pin"></span></span>
                                <select name="city" class="select-filter" required>
                                    <option value="">Выбрать город</option>
                                    <?php foreach ((array) $this->data['projects'] as $item): ?>
                                    <option value="<?=$item['city_id']?>"><?=$item['name']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="button-wrap-filter">
                                <button id="submit-form-1" class="button" type="submit">Далее</button>
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