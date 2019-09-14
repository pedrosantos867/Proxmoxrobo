<?= $_->JS('messenger/messenger.js'); ?>
<?= $_->JS('messenger/messenger-theme-future.js'); ?>

<?= $_->CSS('messenger/messenger.css'); ?>
<?= $_->CSS('messenger/messenger-theme-future.css'); ?>
<?= $_->JS('checker/checker.js') ?>
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
                <span class="sr-only"><?= $_->l('Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand hidden-xs hidden-sm" href="<?= $_->link('') ?>">
                <img style="max-width:167px; margin-top: -10px;"
                     src="<?= $_->path('img/logo.png') ?>">

            </a>

        </div>

        <? if ($client) { ?>

            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li <?= ($_->link($request) == $_->link('bills') ? 'class="active"' : '') ?> >
                        <a href="<?= $_->link('bills') ?>"><span class="glyphicon glyphicon-book"
                                                                 aria-hidden="true"></span>
                        <span class="hidden-sm">    <?= $_->l('Счета') ?>

                            <span class="label label-danger checker-bills"></span>

                        </span>
                        </a>
                    </li>
                    <li <?= ($_->link($request) == $_->link('orders') ? 'class="active"' : '') ?>>
                        <a href="" class="dropdown-toggle" data-toggle="dropdown"
                        ><span class="glyphicon glyphicon-shopping-cart"
                               aria-hidden="true"></span> <span class="hidden-sm"><?= $_->l('Заказы') ?></span> <span class="caret"></span></a>

                        <ul class="dropdown-menu" role="menu">
                            <?if($config->enable_component_hosting){?>
                                <li <?= ($_->link($request) == $_->link('hosting-orders') ? 'class="active"' : '') ?>><a
                                        href="<?= $_->link('hosting-orders') ?>"><span class="glyphicon glyphicon-cloud"
                                                                                       aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('Хостинг') ?>
                                    </a>
                                </li>
                            <?}?>
                            <?if($config->enable_component_domain){?>
                                <li <?= ($_->link($request) == $_->link('domain-orders') ? 'class="active"' : '') ?>><a
                                        href="<?= $_->link('domain-orders') ?>"><span class="glyphicon glyphicon-globe"
                                                                                      aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('Домен') ?>
                                    </a>
                                </li>
                            <? } ?>
                            <?if($config->enable_component_vps){?>
                                <li <?= ($_->link($request) == $_->link('vps-orders') ? 'class="active"' : '') ?>><a
                                        href="<?= $_->link('vps-orders') ?>"><span class="glyphicon glyphicon-hdd"
                                                                                       aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('VPS') ?>
                                    </a>
                                </li>
                            <?}?>

                            <?foreach($menu['orders'] as $item){?>

                                <li >
                                    <a
                                        href="<?= $item->link ?>"><span
                                            class="fa <?= $item->icon ?>"
                                            aria-hidden="true"></span>
                                        &nbsp;<?= $item->name ?>
                                    </a>
                                </li>

                            <? } ?>

                            <?foreach($service_categories as $category){?>

                                <li >
                                    <a
                                        href="<?= $_->link('service-orders/category-'.$category->id) ?>"><span
                                            class="fa <?= $category->icon ?>"
                                            aria-hidden="true"></span>
                                        &nbsp;<?= $category->name ?>
                                    </a>
                                </li>

                            <? } ?>

                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            <span class="hidden-sm"><?= $_->l('Создать заказ') ?> </span>
                            <span class="caret"></span></a>

                        <ul class="dropdown-menu" role="menu">
                            <?if($config->enable_component_hosting){?>
                                <li <?= ($_->link($request) == $_->link('hosting-orders/new') ? 'class="active"' : '') ?>><a
                                        href="<?= $_->link('hosting-orders/new') ?>"><span class="glyphicon glyphicon-cloud"
                                                                                           aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('Хостинг') ?>
                                    </a>
                                </li>
                            <?}?>
                            <?if($config->enable_component_domain){?>
                                <li <?= ($_->link($request) == $_->link('domain-orders/order') ? 'class="active"' : '') ?>>
                                    <a
                                        href="<?= $_->link('domain-orders/order') ?>"><span
                                            class="glyphicon glyphicon-globe"
                                            aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('Домен') ?>
                                    </a>
                                </li>
                            <? } ?>
                            <?if($config->enable_component_vps){?>
                                <li <?= ($_->link($request) == $_->link('vps-orders/new') ? 'class="active"' : '') ?>><a
                                        href="<?= $_->link('vps-orders/new') ?>"><span class="glyphicon glyphicon-hdd"
                                                                                       aria-hidden="true"></span>
                                        &nbsp;<?= $_->l('VPS') ?>
                                    </a>
                                </li>
                            <?}?>
                            <?foreach($menu['create_orders'] as $item){?>

                                <li >
                                    <a
                                        href="<?= $item->link ?>"><span
                                            class="fa <?= $item->icon ?>"
                                            aria-hidden="true"></span>
                                        &nbsp;<?= $item->name ?>
                                    </a>
                                </li>

                            <? } ?>
                            <?if($service_categories){?>
                                <?=$_->css('font-awesome.min.css')?>
                                <?foreach($service_categories as $category){?>

                                    <li >
                                        <a
                                            href="<?= $_->link('service-order/category-'.$category->id) ?>"><span
                                                class="fa <?= $category->icon ?>"
                                                aria-hidden="true"></span>
                                            &nbsp;<?= $category->name ?>
                                        </a>
                                    </li>

                                <? } ?>
                            <? } ?>
                        </ul>

                    </li>

                    <?if(count($currencies) > 1){?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"> <span class="glyphicon glyphicon-usd" aria-hidden="true"></span>
                                <span class="hidden-sm"> <?= $_->l('Валюта') ?> </span>
                                <span class="caret"></span></a>

                            <ul class="dropdown-menu" role="menu">
                                <? foreach ($currencies as $c) { ?>
                                    <li>
                                        <a href="<?= $_->link('currency/set/' . $c->id . '?back=' . $request) ?>"><?= $c->name ?></a>
                                    </li>
                                <? } ?>
                            </ul>
                        </li>
                    <? } ?>


                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?if($config->enable_lang_switcher_for_client && count($languages) > 1){?>
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

                    <? if (isset($_COOKIE['employee'])) { ?>
                        <li>
                            <a href="<?= $_->link('/admin') ?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                        </li>
                    <? } ?>
                    <li>
                        <a href="<?= $_->link('support') ?>"><span class="glyphicon glyphicon-question-sign"></span>
                            <span class="hidden-sm hidden-md"> <?= $_->l('Поддержка') ?>

                                <span class="label label-danger checker-messages"></span>

                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $_->link('balance') ?>">
                            <span class="glyphicon glyphicon-credit-card"></span>&nbsp;&nbsp;
                            <?= $currency->displayPrice($client->balance) ?></a>
                    </li>


                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <?= $client->username ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">

                            <li><a href="<?= $_->link('setting') ?>"> <span
                                        class="glyphicon glyphicon-cog"></span> <?= $_->l('Настройки') ?></a></li>
                            <? if ($config->refprogram_enable) { ?>
                                <li><a href="<?= $_->link('partner') ?>"> <span
                                            class="glyphicon glyphicon-briefcase"></span> <?= $_->l('Партнерская программа') ?>
                                    </a></li>
                            <? } ?>
                            <li><a href="<?= $_->link('logout') ?>"> <span
                                        class="glyphicon glyphicon-log-out"></span> <?= $_->l('Выход') ?></a></li>
                        </ul>
                    </li>

                </ul>
            </div><!--/.nav-collapse -->
        <? } ?>


    </div>
</nav>
<?if(isset($demo_mode) && $demo_mode){?>
    <script>
        $(document).on("ready", function () {
            createNoty("<?=$_->l("Функция не доступна в демо режиме!")?>", "danger");
        })
    </script>
<?}?>
<div class="container">
    <?= $content ?>
</div>
<script>
    //Checker for ticket messages
    var messageChecker = Object.create(Checker);
    messageChecker.start('/support/checker/get-count', function (message) {
        <?if(!isset($ticketAnwerList)){?>
        Messenger().post({
            message: "<strong><?=$_->l('Поддержка')?></strong>"
            + "</br><?=$_->l('Новоее сообщение')?> "
            + "</br><?=$_->l('Тикет')?>: " + message.subject,
            type: "info",
            events: {
                "click": function (e) {
                    window.location = '/support/ticket/show?ticket_id=' + message.ticket_id;
                }
                }
        });
        <?} else {?>
        getTableWithFilter();
        <?}?>
    });
</script>
<!-- /.container -->


<?if($client){?>

    <!-- /.container -->
    <footer class="footer">
        <div class="container text-right">
            <p class="text-muted ">V <?=$config->app_version?></p>
        </div>
    </footer>
<?}?>
