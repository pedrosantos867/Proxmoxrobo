<html>
<head>

    <meta charset="UTF-8">
    <title><?=$_->l('Установщик биллинг системы HopeBilling')?></title>

    <!-- Bootstrap core CSS -->
    <?php $_->CSS("bootstrap.min.css"); ?>
    <?php $_->CSS("bootstrap-theme.min.css"); ?>

    <!-- JQuery -->
    <?php $_->Js("jquery.min.js"); ?>

    <!-- Bootstrap core JS -->
    <?php $_->Js("bootstrap.min.js"); ?>
    <script src="//api.hopebilling.com/widget/HBLS.modal.ajax.js"></script>

    <link rel="icon" href="<?= $_->path('img/favicon.ico') ?>" sizes="16x16" type="image/ico">
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <div class="text-center">
            <img alt="HopeBilling" src="<?php echo $_->path('img/logo.png') ?>"/>
        </div>
    </div>
    <?if(\System\Tools::rGET('step', 0) != 0){?>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li <?php echo \System\Tools::rGET('step', 0) == 0 ? 'class="active"' : '' ?>>
                    <a href="<?php echo $_->link('install')?>"><span class="badge alert-info">1</span> <?=$_->l('Выбор языка')?></a>
                </li>
                <li <?php echo \System\Tools::rGET('step') == 1 ? 'class="active"' : '' ?>>
                    <a href="<?php echo $_->link('install?step=1')?>"><span class="badge alert-danger">2</span> <?=$_->l('Проверка параметров')?></a>
                </li>
                <li <?php echo \System\Tools::rGET('step') == 2 ? 'class="active"' : '' ?>>
                    <a href="#"> <span class="badge alert-warning">3</span> <?=$_->l('Установка базы данных')?>
                    </a>
                </li>

                <li <?php echo \System\Tools::rGET('step') == 4 ? 'class="active"' : '' ?>>
                    <a href="#"> <span class="badge alert-success">4</span> <?=$_->l('Настройки')?></a>
                </li>

            </ul>
        </div>
    </div>
    <?}?>
    <?php echo $content ?>
</div>
</body>
</html>