<?= $_->JS('messenger/messenger.js'); ?>
<?= $_->JS('messenger/messenger-theme-future.js'); ?>

<?= $_->CSS('messenger/messenger.css'); ?>
<?= $_->CSS('messenger/messenger-theme-future.css'); ?>
<?= $_->JS('checker/checker.js'); ?>
<?= $_->JS('jquery.cookie.js') ?>
<?= $_->JS('jhash-2.1.js') ?>
<?= $_->JS('filter.js') ?>
<?= $_->CSS('style.css') ?>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= $_->link('admin') ?>"><img style="max-width:167px; margin-top: -10px;"
                                                                         src="<?= $_->path('img/logo.png') ?>"> </a>
        </div>


        <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li <?= ($_->link($request) == $_->link('admin/clients') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/clients') ?>"><span class="glyphicon glyphicon-user"
                                                                         aria-hidden="true"></span><span class="hidden-sm"><?=$_->l('Клиенты')?></span></a>
                    </li>

                    <li <?= ($_->link($request) == $_->link('admin/bills') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/bills') ?>"><span class="glyphicon glyphicon-book"
                                                                       aria-hidden="true"></span><span class="hidden-sm"><?=$_->l('Счета')?></span></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false"><span class="glyphicon glyphicon-folder-open"></span> <span class="hidden-sm"><?=$_->l('Услуги')?></span>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown-submenu">
                                <a ><span class="glyphicon glyphicon-cloud"></span>
                                    <?=$_->l('Хостинг') ?></a>
                                <ul class="dropdown-menu">
                                    <li <?= ($_->link($request) == $_->link('admin/orders') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/orders') ?>"><span
                                                class="glyphicon glyphicon-shopping-cart"
                                                aria-hidden="true"></span><?=$_->l('Заказы')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/servers') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/servers') ?>"><span
                                                class="glyphicon glyphicon-hdd"></span> <?=$_->l('Сервера')?></a></li>
                                    <li <?= ($_->link($request) == $_->link('admin/plan/params') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/plan/params') ?>"><span
                                                class="glyphicon glyphicon-list" aria-hidden="true"></span>
                                            <?=$_->l('Опции')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/plans') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/plans') ?>"><span
                                                class="glyphicon glyphicon-stats"></span> <?=$_->l('Тарифы')?></a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a  ><span class="glyphicon glyphicon-registration-mark"></span>
                                    <?=$_->l('Регистрация доменов' )?> </a>
                                <ul class="dropdown-menu">
                                    <li <?= ($_->link($request) == $_->link('admin/domain-orders') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/domain-orders') ?>"><span
                                                class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><?=$_->l('Заказы')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/domains') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/domains') ?>"><span
                                                class="glyphicon glyphicon-globe"
                                                aria-hidden="true"></span><?=$_->l('Доменные зоны')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/domain-registrars') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/domain-registrars') ?>"><span
                                                class="glyphicon glyphicon-user"
                                                aria-hidden="true"></span><?=$_->l('Регистраторы доменов')?></a>
                                    </li>

                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a  ><span class="glyphicon glyphicon-menu-hamburger"></span>
                                    <?=$_->l('Дополнительные услуги')?> </a>
                                <ul class="dropdown-menu">
                                    <li <?= ($_->link($request) == $_->link('admin/service-orders') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/service-orders') ?>"><span
                                                class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><?=$_->l('Заказы')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/service-categories') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/service-categories') ?>"><span
                                                class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> <?=$_->l('Категории')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/services') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/services') ?>"><span
                                                class="glyphicon glyphicon-asterisk"
                                                aria-hidden="true"></span> <?=$_->l('Услуги')?></a>
                                    </li>


                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a><span class="glyphicon glyphicon-hdd"></span>
                                    <?=$_->l('VPS\VDS')?>  </a>

                                <ul class="dropdown-menu">
                                    <li <?= ($_->link($request) == $_->link('admin/vps-orders') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/vps-orders') ?>"><span
                                                class="glyphicon glyphicon-shopping-cart"
                                                aria-hidden="true"></span><?=$_->l('Заказы')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/vps-servers') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/vps-servers') ?>"><span
                                                class="glyphicon glyphicon-hdd"></span> <?=$_->l('Сервера')?></a></li>

                                    <li <?= ($_->link($request) == $_->link('admin/vps-params') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/vps-params') ?>"><span
                                                class="glyphicon glyphicon-list" aria-hidden="true"></span>
                                            <?=$_->l('Опции')?></a>
                                    </li>
                                    <li <?= ($_->link($request) == $_->link('admin/vps-plans') ? 'class="active"' : '') ?>>
                                        <a href="<?= $_->link('admin/vps-plans') ?>"><span
                                                class="glyphicon glyphicon-stats"></span> <?=$_->l('Тарифы')?></a></li>


                                </ul>

                            </li>
                            <li <?= ($_->link($request) == $_->link('admin/promocodes') ? 'class="active"' : '') ?>>
                                <a href="<?= $_->link('admin/promocodes') ?>"><span class="glyphicon glyphicon-info-sign"></span><span><?=$_->l('Промокоды')?></span></a>
                            </li>

                            <?foreach($menu['services'] as $item){?>

                                <li  <?=(isset($item->items)) ? 'class="dropdown-submenu"' : ''?>>
                                    <a
                                        href="<?= $item->link ?>"><span
                                            class="fa <?= $item->icon ?>"
                                            aria-hidden="true"></span>
                                        &nbsp;<?= $item->name ?>
                                    </a>

                                    <?if(isset($item->items)){?>
                                        <ul class="dropdown-menu">
                                           <?foreach ($item->items as $submenu){?>
                                            <li <?= ($_->link($request) == $submenu->link ? 'class="active"' : '') ?>>
                                                <a href="<?= $submenu->link ?>"><span
                                                        class="glyphicon <?= $submenu->icon ?>"
                                                        aria-hidden="true"></span><?=$submenu->name?></a>
                                            </li>
                                           <?}?>
                                        </ul>
                                    <?}?>
                                </li>

                            <? } ?>
                        </ul>
                    </li>

                    <li <?= ($_->link($request) == $_->link('admin/tickets') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/tickets') ?>"><span class="glyphicon glyphicon-info-sign"></span><span class="hidden-sm"><?=$_->l('Тикеты')?></span> <span style="display: none" class="label label-danger open-ticket-count"></span></a>
                    </li>
                    <li <?= ($_->link($request) == $_->link('admin/employees') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/employees') ?>"><span class="glyphicon glyphicon-user"></span><span class="hidden-sm"><?=$_->l('Сотрудники')?></span></a>
                    </li>
                    <li <?= ($_->link($request) == $_->link('admin/pages') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/pages') ?>"><span class="glyphicon glyphicon-file"></span><span class="hidden-sm"><?=$_->l('Страницы')?></span></a>
                    </li>
                    <li <?= ($_->link($request) == $_->link('admin/settings') ? 'class="active"' : '') ?>>
                        <a href="<?= $_->link('admin/settings') ?>"><span class="glyphicon glyphicon-cog"></span><span class="hidden-sm"><?=$_->l('Настройки')?></span></a>
                    </li>

                </ul>
                <script>
                    $('li.active').removeClass('active');
                    $('.dropdown .dropdown-menu .dropdown-submenu > a').on('click', function (e) {
                            $(this).find('.dropdown-menu').toggle();
                            e.preventDefault();
                            e.stopPropagation();
                    });
                </script>
                <ul class="nav navbar-nav navbar-right ">
                    <?if($config->enable_lang_switcher_for_admin && count($languages) > 1){?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                <img src="<?=$_->link('storage/i18n/flags/'.$lang->iso_code.'.png')?>" height="23px">
                                <span class="caret"></span></a>

                            <ul class="dropdown-menu" role="menu">

                                <? foreach ($languages as $l) { ?>
                                    <li>
                                        <a href="<?= $_->link($request, 'lang='.$l->id) ?>">
                                            <img src="<?=$_->link('storage/i18n/flags/'.$l->iso_code.'.png')?>" height="23px"> <?=$l->name?>
                                        </a>
                                    </li>
                                <? } ?>
                            </ul>
                        </li>
                    <?}?>
                    <li class="hidden-sm hidden-md">
                        <a href="<?= $_->link('/') ?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                    </li>
                    <li>
                        <a  href="<?= $_->link('admin/logout') ?>"><span class="glyphicon glyphicon-log-out"></span><span class="hidden-sm hidden-md"><?=$_->l('Выйти')?></span></a>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->

    </div>
</nav>
<div id="noty-holder"></div>
<div class="container">
    <?= $content ?>
</div>
<?if(isset($demo_mode) && $demo_mode){?>
    <script>
        $(document).on("ready", function () {
            createNoty("<?=$_->l("Функция не доступна в демо режиме!")?>", "danger");
        })
    </script>
<?}?>
<!-- BUG BUTTON-->
<style>
    .btn-circle.btn-lg {
        width: 40px;
        height: 40px;
        padding: 5px 8px;
        font-size: 12px;
        line-height: 1.33;
        border-radius: 25px;
    }

    .feedback {
        position: fixed;
        z-index: 9;
    }

    .feedback textarea {
        height: 180px;
    }

    .feedback .screenshot {
        position: relative;
        top: -24px;
        right: 10px;
        opacity: .6
    }

    .feedback .screenshot:hover {
        opacity: 1
    }

    .feedback .reported p, .feedback .failed p {
        height: 190px
    }

    .feedback.left {
        left: 5px;
        bottom: 15px
    }

    .feedback.right {
        right: 5px;
        bottom: 15px
    }

    .feedback .dropdown-menu {
        width: 290px;
        height: 355px;
        bottom: 50px;
    }

    .feedback.left .dropdown-menu {
        left: 0
    }

    .feedback.right .dropdown-menu {
        right: 0
    }

    .feedback .hideme {
        display: none
    }
    .messenger-message-inner strong{
        font-weight: bold;
        font-size: 16px;
    }
    .messenger-message-inner{
        text-align: left;
    }
</style>
<script>
    (function ($) {
        $.fn.feedback = function (success, fail) {
            self = $(this);
            self.find('.dropdown-menu-form').on('click', function (e) {
                e.stopPropagation()
            });

            self.find('.screenshot').on('click', function () {
                self.find('.cam').removeClass('fa-camera fa-check').addClass('fa-refresh fa-spin');
                html2canvas($(document.body), {
                    onrendered: function (canvas) {
                        self.find('.screen-uri').val(canvas.toDataURL("image/png"));
                        self.find('.cam').removeClass('fa-refresh fa-spin').addClass('fa-check');
                    }
                });
            });

            self.find('.do-close').on('click', function () {
                self.find('.dropdown-toggle').dropdown('toggle');
                self.find('.reported, .failed').hide();
                self.find('.report').show();
                self.find('.cam').removeClass('fa-check').addClass('fa-camera');
                self.find('.screen-uri').val('');
                self.find('textarea').val('');
            });

            failed = function () {
                self.find('.loading').hide();
                self.find('.failed').show();
                if (fail) fail();
            };

            self.find('form').on('submit', function () {
                self.find('.report').hide();
                self.find('.loading').show();
                $.post($(this).attr('action'), $(this).serialize(), null, 'json').done(function (res) {
                    if (res.result == 'success') {
                        self.find('.loading').hide();
                        self.find('.reported').show();
                        if (success) success();
                    } else failed();
                }).fail(function () {
                    failed();
                });
                return false;
            });
        };
    }(jQuery));

    $(document).ready(function () {
        $('.feedback').feedback();
        
        //Checker for tickets
        var ticketChecker = Object.create(Checker);
        ticketChecker.start('/admin/ticket/checker/get-count', function (item, data, that) {
            if(that.additionalData.count_t < data.additional.count_t) {
                <?if(!isset($ticketList)){?>
                Messenger().post({
                    message: "<strong><?=$_->l('Новый тикет')?></strong>"
                    + "</br><?=$_->l('От')?>: " + item.client
                    + "</br><?=$_->l('Тема')?>: " + item.subject,
                    type: "info",
                    events: {
                        "click": function (e) {
                            window.location = '/admin/ticket/' + item.id;
                        }
                    }
                });
                <?}else{?>
                getTableWithFilter();
                <?}?>
            }
            else if(that.additionalData.count_ta < data.additional.count_ta) {
                <?if(!isset($ticketAnwerList)){?>
                Messenger().post({
                    message:  "<strong><?=$_->l('Новоее сообщение в тикете')?></strong>"
                    + "</br><?=$_->l('От')?>: "   + item.client
                    + "</br><?=$_->l('Тема тикета')?>: " + item.subject,
                    type: "info",
                    events: {
                        "click": function(e){
                            window.location = '/admin/ticket/' + item.ticket_id;
                        }
                    }
                });
                <?}else{?>
                getTableWithFilter();
                <?}?>
            }
        }, 2000, {count_t: -1, count_ta: -1});
        ticketChecker.afterStep = function (data) {
            if(data.open_ticket_count > 0) $('.open-ticket-count').text(data.open_ticket_count).show();
            this.additionalData.count_t = data.additional.count_t;
            this.additionalData.count_ta = data.additional.count_ta;
        };

    });
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
<div class="feedback right">
    <div class="tooltips">
        <div class="btn-group dropup">
            <button type="button" class="btn btn-primary dropdown-toggle btn-circle btn-lg" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bug fa-2x" title="Report Bug"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-form">
                <li>
                    <div class="report">
                        <h3 class="text-center"><?=$_->l('Сообщение об ошибке')?></h3>

                        <form class="doo" method="post" action="<?= $_->link('bug-report') ?>">
                            <div class="col-sm-12">
                                <input name="email" required="required" class="form-control" placeholder="<?=$_->l('Ваш Email')?>">
                                <textarea required="required" style="margin-top:4px" name="comment" class="form-control"
                                          placeholder="<?=$_->l('Расскажите нам об обнаруженной ошибке. Вы можете прикрепить скриншот, нажмите на изображение фотоаппарата.')?>"></textarea>
                                <input name="screenshot" type="hidden" class="screen-uri">
                                <span class="screenshot pull-right"><i class="fa fa-camera cam"
                                                                       title="<?=$_->l('Сделать скриншот')?>"></i></span>
                            </div>
                            <div class="col-sm-12 clearfix">
                                <button class="btn btn-primary btn-block"><?=$_->l('Отправить отчет')?></button>
                            </div>
                        </form>
                    </div>
                    <div class="loading text-center hideme">
                        <h2>Please wait...</h2>

                        <h2><i class="fa fa-refresh fa-spin"></i></h2>
                    </div>
                    <div class="reported text-center hideme">
                        <h2>Thank you!</h2>

                        <p>Your submission has been received, we will review it shortly.</p>

                        <div class="col-sm-12 clearfix">
                            <button class="btn btn-success btn-block do-close">Close</button>
                        </div>
                    </div>
                    <div class="failed text-center hideme">
                        <h2>Oh no!</h2>

                        <p>It looks like your submission was not sent.<br><br><a href="mailto:">Try contacting us by the
                                old method.</a></p>

                        <div class="col-sm-12 clearfix">
                            <button class="btn btn-danger btn-block do-close">Close</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<!--END BUG BUTTON-->


<span id="top-link-block" class="hidden">
    <a href="#top" class="well well-sm" onclick="$('html,body').animate({scrollTop:0},'slow');return false;">
        <i class="glyphicon glyphicon-chevron-up"></i> Back to Top
    </a>
</span><!-- /top-link-block -->

<!-- /.container -->
<footer class="footer">
    <div class="container text-right">
        <p class="text-muted ">V <?=$config->app_version?></p>
    </div>
</footer>

<script>
        $(document).on('click', 'button.hide_row', function () {
            var id_row = $(this).parents('tr').data('id');
            var el = $(this);
            $.ajax({
                method: 'post',
                dataType: 'json',
                data: {ajax: 1, action: 'HideShowRow', id: id_row, type: 1},
                success: function (data) {
                    if (data.result == 1) {
                        $(el).parents('tr').addClass('row_hidden');
                        $(el).replaceWith(' <button class="btn btn-default btn-xs show_row">&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>');
                    }
                    else if (data.result == 0) {
                        createNoty("<?=$_->l("Функция не доступна в демо режиме!")?>", "danger");
                    }
                }
            });
        });

        $(document).on('click', '.show_row', function () {
            var id_row = $(this).parents('tr').data('id');
            var el = $(this);
            $.ajax({
                method: 'post',
                dataType: 'json',
                data: {ajax: 1, action: 'HideShowRow', id: id_row, type: 0},
                success: function (data) {
                    if (data.result == 1) {
                        $(el).parents('tr').removeClass('row_hidden');
                        $(el).replaceWith(' <button class="btn btn-default btn-xs hide_row">&nbsp;<span class="glyphicon glyphicon-eye-close"></span></button>');
                    }
                    else {
                        createNoty("<?=$_->l("Функция не доступна в демо режиме!")?>", "danger");
                    }
                }
            });
        });
</script>

