<div class="ajax-block">
    <ul class="nav nav-pills" role="tablist">
        <li role="presentation" <?= ($_->link($request) == $_->link('admin/settings') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings') ?>">   <span class="glyphicon glyphicon-home"></span> <?=$_->l('Основные')?> </a>
        </li>

        <li role="presentation" <?= ($_->isActive($request, 'admin/settings/notifications(.*)') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings/notifications') ?>">   <span class="glyphicon glyphicon-bell"></span> <?=$_->l('Уведомления')?></a>
        </li>

        <li role="presentation" <?= ($_->isActive($request, 'admin/settings/currenc(.*)') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings/currencies') ?>"> <span class="glyphicon glyphicon-euro"></span> <?=$_->l('Валюты')?> </a>
        </li>

        <li role="presentation" <?= ($_->isActive($request, 'admin/settings/language(.*)') ? 'class="active"' : '') ?>>
            <span class=""></span>
            <a href="<?= $_->link('admin/settings/languages') ?>"><span class="glyphicon glyphicon-globe"></span><?=$_->l('Языки')?> </a>
        </li>

        <li role="presentation" <?= ($_->isActive($request, 'admin/settings/sms-gateway') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings/sms-gateway') ?>"><span class="glyphicon glyphicon-phone"></span> <?=$_->l('СМС шлюз')?></a>
        </li>

        <li role="presentation" <?= ($_->link($request) == $_->link('admin/settings/update') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings/update') ?>"><span class="glyphicon glyphicon-refresh"></span> <?=$_->l('Обновление')?></a></li>

        <li role="presentation" <?= ($_->link($request) == $_->link('admin/settings/license') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/settings/license') ?>"><span class="glyphicon glyphicon-info-sign"></span><?=$_->l('Лицензия')?></a></li>

        <li role="presentation" <?= ($_->link($request) == $_->link('admin/modules') ? 'class="active"' : '') ?>>
            <a href="<?= $_->link('admin/modules') ?>"><span class="glyphicon glyphicon-align-justify"></span><?=$_->l('Модули')?></a></li>

    </ul>

    <div class="well " style="margin-top: 10px">
        <?= $content ?>
    </div>

</div>