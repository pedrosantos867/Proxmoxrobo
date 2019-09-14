<style type="text/css">
    body {
        padding-top: 65px;
    }

    .ref_link-block {
        margin-bottom: 25px;
    }

    .ref_link-block > span {

        display: block;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .ref_link-block > input {

    }

    table tr:first-child {
        background-color: #333;
        color: #fff;
    }

    .ref_results div > span {
        min-width: 160px;
        display: inline-block;
    }

    .ref_results div > b {
        min-width: 90px;
        display: inline-block;
    }

    .ref_results > div {
        margin-bottom: 10px;
    }

    .ref_results .btn-sm .glyphicon {
        margin-right: 6px;
    }

    .ref_result .btn-sm {
        text-transform: uppercase;
    }
</style>

<p>
    <b><i><?= $_->l('Партнерская программа') ?></i></b>
    <?= $_->l('- это эффективный способ получения дополнительного дохода для владельцев и
    менеджеров качественных
    web-проектов различной тематики.
    Размещая у себя на сайте баннер/ссылку, партнер <b><i>получает денежное вознаграждение</i></b>,
    при прямом переходе с его сайта или при повторном заходе посетителя <b><i>в течение 1 месяца</i></b>
    (мониторинг при помощи cookies).') ?>
</p>
<div class="ref_link-block">
    <span><?= $_->l('Ваша ссылка:') ?></span>
    <input type="text" class="form-control" value="<?= $plink ?>" readonly>
</div>

<table class="table table-bordered">
    <tr>
        <th><?= $_->l('Номер реферала') ?></th>
        <th><?= $_->l('Ваш заработок') ?></th>
        <th><?= $_->l('Доступно для снятия') ?></th>
    </tr>
    <? $summ = 0;
    $summ2 = 0;
    foreach ($referals as $referal) {
        ?>
        <tr>
            <td><?= $referal->id ?></td>
            <td><?=
                $currency->displayPrice($referal->rev * $config->refprogram_percent/100);
                $summ += $referal->rev * $config->refprogram_percent/100; ?> </td>
            <td><?=
                $currency->displayPrice($referal->ref_rev * $config->refprogram_percent/100);
                $summ2 += $referal->ref_rev * $config->refprogram_percent/100; ?> </td>
        </tr>
    <? } ?>
</table>
<div class="ref_results">
    <div>
        <span><?= $_->l('Итого доступно:') ?></span>
        <b><?= $currency->displayPrice($summ2) ?> </b>
        <a href="<?= $_->link('partner/getMoney') ?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-usd"
                                                                                        aria-hidden="true"></span>
            <?= $_->l('Зачислить на внутренний счет') ?></a>
    </div>
    <div>
        <span><?= $_->l('Итого заработано:') ?></span>
        <b><?= $currency->displayPrice($summ) ?> </b>
    </div>
</div>
