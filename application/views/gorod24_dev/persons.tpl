<main>
    <div class="container main-persons">
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
                    <h2>Персоны Феодосии</h2>
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
                <div class="col-md-6 col-sm-12 persons-content-center-box">
                    <div class="persons-wrap">
                        <div class="filter-wrap">
                            <div class="head-wrap">
                                <div class="top-sect">
                                    <ul class="menu-option-list">
                                        <li class="active"><a href="#">Популярные</a></li>
                                        <li><a href="#">Новые</a></li>
                                        <li><a href="#">С мнениями</a></li>
                                    </ul>
                                    <div class="button-wrap-person-add">
                                        <a href="#" class="button">Добавить персону</a>
                                    </div>
                                </div>
                                <div class="show-filter">
                                    <a href="#" class="show-filter-open">Показать фильтр</a>
                                    <a href="#" class="show-filter-exit"><span class="icon-close"></span></a>
                                </div>
                                <div class="middle-sect">
                                    <form method="POST">
                                        <div class="input-wrap">
                                            <label>
                                                <span class="icon-wrap"><span class="icon-user"></span></span>
                                                <input type="text" name="name" placeholder="ФИО">
                                            </label>
                                        </div>
                                        <div class="input-wrap-date">
                                            <div class="descr">
                                                <span class="icon-wrap"><span class="icon-calendar"></span></span>
                                                Дата рождения
                                            </div>
                                            <label>
                                                <input type="text" name="date-1" placeholder="От">
                                            </label>
                                            <label>
                                                <input type="text" name="date-2" placeholder="До">
                                            </label>
                                        </div>
                                        <div class="input-wrap">
                                            <label>
                                                <span class="icon-wrap"><span class="icon-location-pin"></span></span>
                                                <input type="text" name="job" placeholder="Место работы">
                                            </label>
                                        </div>
                                        <div class="input-wrap">
                                            <label>
                                                <span class="icon-wrap"><span class="icon-briefcase"></span></span>
                                                <input type="text" name="pos" placeholder="Должность">
                                            </label>
                                        </div>
                                        <div class="button-wrap-person">
                                            <button class="button" type="submit">Фильтровать</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="content-sect">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="person-item-wrap">
                                        <div class="head">
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views">505</span>
                                            </div>
                                            <div class="likes">
                                                <a href="#"><span class="icon-like"></span></a><span class="text-like">20</span><a href="#"><span class="icon-dislike"></span></a><span class="text-like">5</span>
                                            </div>
                                        </div>
                                        <div class="cont">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/persons/p">
                                                <div class="img-wrap">
                                                    <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/persons/item-1.jpg" alt="alt">
                                                </div>
                                                <span class="name">Фомич Сергей</span>
                                            </a>
                                            <span class="descr">Глава администрации города Феодосия</span>
                                            <a class="job" href="#">Администрация Феодосии</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="person-item-wrap">
                                        <div class="head">
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views">505</span>
                                            </div>
                                            <div class="likes">
                                                <a href="#"><span class="icon-like"></span></a><span class="text-like">20</span><a href="#"><span class="icon-dislike"></span></a><span class="text-like">5</span>
                                            </div>
                                        </div>
                                        <div class="cont">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/persons/p">
                                                <div class="img-wrap">
                                                    <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/persons/item-1.jpg" alt="alt">
                                                </div>
                                                <span class="name">Фомич Сергей</span>
                                            </a>
                                            <span class="descr">Глава администрации города Феодосия</span>
                                            <a class="job" href="#">Администрация Феодосии</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="person-item-wrap">
                                        <div class="head">
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views">505</span>
                                            </div>
                                            <div class="likes">
                                                <a href="#"><span class="icon-like"></span></a><span class="text-like">20</span><a href="#"><span class="icon-dislike"></span></a><span class="text-like">5</span>
                                            </div>
                                        </div>
                                        <div class="cont">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/persons/p">
                                                <div class="img-wrap">
                                                    <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/persons/item-1.jpg" alt="alt">
                                                </div>
                                                <span class="name">Фомич Сергей</span>
                                            </a>
                                            <span class="descr">Глава администрации города Феодосия</span>
                                            <a class="job" href="#">Администрация Феодосии</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="person-item-wrap">
                                        <div class="head">
                                            <div class="views">
                                                <span class="icon-eye"></span><span class="text-views">505</span>
                                            </div>
                                            <div class="likes">
                                                <a href="#"><span class="icon-like"></span></a><span class="text-like">20</span><a href="#"><span class="icon-dislike"></span></a><span class="text-like">5</span>
                                            </div>
                                        </div>
                                        <div class="cont">
                                            <a href="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/persons/p">
                                                <div class="img-wrap">
                                                    <img class="img-responsive" src="<?=$GLOBALS['CONFIG']['HTTP_HOST']?>/application/views/gorod24_dev/img/persons/item-1.jpg" alt="alt">
                                                </div>
                                                <span class="name">Фомич Сергей</span>
                                            </a>
                                            <span class="descr">Глава администрации города Феодосия</span>
                                            <a class="job" href="#">Администрация Феодосии</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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