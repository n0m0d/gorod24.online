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
                        <form method="POST" id="add-form-4" class="adsaddform">
                            <h1>Вы можете сделать Ваше объявление заметнее</h1>
                            <div class="pay-items-wrap">
                                <div class="row">
                                    <?php foreach($this->data['getPaymentPackages'] as $item): ?>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="pay-item" id="pay-item-1">
                                            <div class="pay-head">
                                                <span class="head-title"><?=$item['title'];?></span>
                                                <div class="head-stick green">
                                                    <span class="name"><?=$item['name'];?></span>
                                                    <span class="period"><?=$item['items'][0]['period'];?></span>
                                                </div>
                                            </div>
                                            <div class="pay-price">
                                                <span class="price"><?=$item['items'][0]['price'];?>,00</span>
                                            </div>
                                            <div class="pay-old-price">
                                                <span class="old-price" hidden>400,00</span>
                                            </div>
                                            <div class="pay-select">
                                                <span class="title">Выберите срок услуги</span>
                                                <div class="select-wrap">
                                                    <select name="item-1-period" data-type="1" class="item-select-1">
                                                        <?php foreach($item['items'] as $sub_item): ?>
                                                        <option value="<?=$sub_item['id'];?>" data-cost="<?=$sub_item['price'];?>,00" data-discount="<?=$sub_item['discount'];?>" data-specdiscount="0"><?=$sub_item['period'];?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="pay-button">
                                                <button type="button" class="item-button-1" data-id="pay-item-1" data-for="in-type-1">Выбрать</button>
                                            </div>
                                            <div class="pay-footer">
                                                <?=$item['descr'];?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <!--<div class="col-md-3 col-sm-3">
                                        <div class="pay-item" id="pay-item-2">
                                            <div class="pay-head">
                                                <span class="head-title">Интернет</span>
                                                <div class="head-stick blue">
                                                    <span class="name">VIP</span>
                                                    <span class="period">7 дней</span>
                                                </div>
                                            </div>
                                            <div class="pay-price">
                                                <span class="price">50,00</span>
                                            </div>
                                            <div class="pay-old-price">
                                                <span class="old-price" hidden>400,00</span>
                                            </div>
                                            <div class="pay-select">
                                                <span class="title">Выберите срок услуги</span>
                                                <div class="select-wrap">
                                                    <select name="item-2-period" data-type="2" class="item-select-2">
                                                        <option value="3" data-cost="50,00" data-price="50,00" data-discount="0" data-specdiscount="0">7 дней</option>
                                                        <option value="4" data-cost="100,00" data-price="90,00" data-discount="10" data-specdiscount="0">14 дней</option>
                                                        <option value="5" data-cost="150,00" data-price="120,00" data-discount="20" data-specdiscount="0">21 день</option>
                                                        <option value="6" data-cost="200,00" data-price="140,00" data-discount="30" data-specdiscount="0">30 дней</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="pay-button">
                                                <button type="button" class="item-button-2" data-id="pay-item-2" data-for="in-type-2">Выбрать</button>
                                            </div>
                                            <div class="pay-footer">
                                                <ul>
                                                    <li>Быстрая проверка</li>
                                                    <li>Vip-статус</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="pay-item" id="pay-item-3">
                                            <div class="pay-head">
                                                <span class="head-title">Интернет</span>
                                                <div class="head-stick pink">
                                                    <span class="name">VIP++</span>
                                                    <span class="period">7 дней</span>
                                                </div>
                                            </div>
                                            <div class="pay-price">
                                                <span class="price">100,00</span>
                                            </div>
                                            <div class="pay-old-price">
                                                <span class="old-price" hidden>400,00</span>
                                            </div>
                                            <div class="pay-select">
                                                <span class="title">Выберите срок услуги</span>
                                                <div class="select-wrap">
                                                    <select name="item-3-period" data-type="3" class="item-select-3">
                                                        <option value="7" data-cost="100,00" data-price="100,00" data-discount="0" data-specdiscount="0">7 дней</option>
                                                        <option value="8" data-cost="200,00" data-price="180,00" data-discount="10" data-specdiscount="0">14 дней</option>
                                                        <option value="9" data-cost="300,00" data-price="240,00" data-discount="20" data-specdiscount="0">21 день</option>
                                                        <option value="10" data-cost="400,00" data-price="280,00" data-discount="30" data-specdiscount="0">30 дней</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="pay-button">
                                                <button type="button" class="item-button-3" data-id="pay-item-3" data-for="in-type-3">Выбрать</button>
                                            </div>
                                            <div class="pay-footer">
                                                <ul>
                                                    <li>Быстрая проверка</li>
                                                    <li>Vip-статус</li>
                                                    <li>+ 1 автоматическое поднятие</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="pay-item" id="pay-item-4">
                                            <div class="pay-head">
                                                <span class="head-title">Газета + Интернет</span>
                                                <div class="head-stick aquamarine">
                                                    <span class="super">Super</span>
                                                    <span class="name">VIP</span>
                                                    <span class="period">7 дней</span>
                                                </div>
                                            </div>
                                            <div class="pay-price">
                                                <span class="price">200,00</span>
                                            </div>
                                            <div class="pay-old-price">
                                                <span class="old-price" hidden>400,00</span>
                                            </div>
                                            <div class="pay-select">
                                                <span class="title">Выберите срок услуги</span>
                                                <div class="select-wrap">
                                                    <select name="item-4-period" data-type="4" class="item-select-4">
                                                        <option value="11" data-cost="200,00" data-price="200,00" data-discount="0" data-specdiscount="0">7 дней</option>
                                                        <option value="12" data-cost="400,00" data-price="360,00" data-discount="10" data-specdiscount="0">14 дней</option>
                                                        <option value="13" data-cost="600,00" data-price="480,00" data-discount="20" data-specdiscount="0">21 день</option>
                                                        <option value="14" data-cost="800,00" data-price="560,00" data-discount="30" data-specdiscount="0">30 дней</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="pay-button">
                                                <button type="button" class="item-button-4" data-id="pay-item-4" data-for="in-type-4">Выбрать</button>
                                            </div>
                                            <div class="pay-footer">
                                                <ul>
                                                    <li>Быстрая проверка</li>
                                                    <li>Рамка в газете</li>
                                                    <li>Vip-статус</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                            <div class="accept-wrap requared-input">
                                <h2>Пользовательское соглашение <span class="icon-info"></span></h2>
                                <label class="check-filter">
                                    <input type="checkbox" name="accept" required>
                                    <span class="info-lalbel">Я принимаю</span>
                                </label>
                            </div>
                            <div class="button-wrap-filter">
                                <input type="hidden" name="adv_id" value="<?=$this->data['adv_info']['id'];?>">
                                <button id="submit-form-4" class="button" type="submit">Далее</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>