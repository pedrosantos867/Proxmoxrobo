<div class="row">
    <div class="col-md-12">
        <? if ($_->rget('error')) { ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <span class="glyphicon glyphicon-warning-sign"></span> <b><?=$_->l('Внимание!')?></b><br>
                <?=$_->l('Возникла техническая ошибка.')?>

                <?=$_->l('Напишите в %link для оперативного решения проблемы.', array('link' => '<a class="alert-link" href="'. $_->link('support') .'">'.$_->l('службу поддержки').'</a>'))?>
                Спасибо!
            </div>
        <? } ?>
        <form method="post">
            <div class="page-header text-center">
                <h1><?=$_->l('Подберите домен')?></h1>

            </div>
            <div>

            </div>

            <div class="input-group input-group-lg">
                <input type="text" name="domain" class="form-control"
                       aria-label="Text input with segmented button dropdown">


                <div class="input-group-btn">
                    <button type="submit" onclick="loader.display();" class="btn btn-default"><?= $_->l('Проверить') ?></button>

                </div>
            </div>
        </form>
    </div>
</div>

<? if (!$_->p()) { ?>
    <div class="row top10">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th><?=$_->l('Доменное имя')?></th>
                    <th><?=$_->l('Стоимость')?></th>
                    <th><?=$_->l('Стоимость продления')?></th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($domains as $domain) { ?>
                    <tr>
                        <td><?= $domain->name ?></td>
                        <td><?= $currency->displayPrice($domain->price) ?></td>
                        <td><?= $currency->displayPrice($domain->extension_price) ?></td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    </div>
<? } ?>

<div class="row top10">
    <div class="col-md-12">
        <? if (($res['available'])) { ?>
            <form id="sell-domains" method="post">
                <div class="panel panel-success">
                    <div class="panel-heading"><?=$_->l('Домены доступные для заказа')?></div>
                    <div class="panel-body">
                        <table class="table">
                            <? foreach ($res['available'] as $domain => $data) { ?>
                                <tr>
                                    <td width="5%">
                                        <input type="hidden" name="registrant_id[<?= $domain ?>]"
                                               value="<?= $data->registrant_id ?>">
                                        <input type="hidden" name="domain_id[<?= $domain ?>]" value="<?= $data->id ?>">
                                        <input name="sell[]" value="<?= $domain ?>" type="checkbox">
                                    </td>
                                    <td><?= $domain ?></td>
                                    <td><?= $currency->displayPrice($data->price) ?></td>
                                </tr>
                            <? } ?>
                        </table>
                    </div>
                </div>
            </form>
        <? } ?>
        <? if ($res['no_available']) { ?>
            <div class="panel panel-danger">
                <div class="panel-heading"><?=$_->l('Домены не доступные для заказа')?></div>
                <div class="panel-body">
                    <table class="table">
                        <? foreach ($res['no_available'] as $domain => $data) { ?>
                            <tr>
                                <td width="5%"><input type="checkbox" disabled="disabled"></td>
                                <td><?= $domain ?></td>

                            </tr>
                        <? } ?>
                    </table>
                </div>
            </div>
        <? } ?>
        <? if (($res['booked'])) { ?>
            <div class="panel panel-danger">
                <div class="panel-heading"><?=$_->l('Домены уже кем-то занятые')?></div>
                <div class="panel-body">
                    <table class="table">
                        <? foreach ($res['booked'] as $domain => $data) { ?>
                            <tr>
                                <td width="5%"><input type="checkbox" disabled="disabled"></td>
                                <td><?= $domain ?> <a style="font-size: 9px;font-style: oblique;"
                                                      href="http://dig.ua/search/<?= $domain ?>"
                                                      target="_blank">WHOIS</a></td>

                            </tr>
                        <? } ?>
                    </table>
                </div>
            </div>
        <? } ?>
        <? if (($res['orders'])) { ?>
            <div class="panel panel-danger">
                <div class="panel-heading"><?=$_->l('Домены уже кем-то заказаны, но еще не оплачены')?></div>
                <div class="panel-body">
                    <table class="table">
                        <? foreach ($res['orders'] as $domain => $data) { ?>
                            <tr>
                                <td width="5%"><input type="checkbox" disabled="disabled"></td>
                                <td><?= $domain ?> <a style="font-size: 9px;font-style: oblique;"
                                                      href="http://dig.ua/search/<?= $domain ?>"
                                                      target="_blank">WHOIS</a></td>

                            </tr>
                        <? } ?>
                    </table>
                </div>
            </div>
        <? } ?>
    </div>
</div>
<? if (($res['available'])) { ?>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary pull-right" onclick="$('#sell-domains').submit();">
                <span class="glyphicon glyphicon-shopping-cart"></span>
                <?=$_->l('Заказать')?></button>

        </div>
    </div>
<? } ?>