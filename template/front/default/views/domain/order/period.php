
<?= $_->JS('validator.js') ?>

<script>
    $(function () {
        $('form').validate({messages: validate_messages});
    })
</script>

<div class="ajax-block">
<? use domain\DomainAPI; ?>
<h2><?=$_->l('Выбор периода предоставления услуг')?></h2>

<div class="row">
    <div class="col-md-12">
        <? if (isset($errors)) { ?>

            <? foreach ($errors as $id_order => $error) { ?>
                <? if ($error->type == 'owner_reg') { ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <span class="glyphicon glyphicon-alert"></span>
                    <?=$_->l('Возникла ошибка с регистрацией владельца для домена %domain', array('domain' => $error->order->domain))?>
                    <? if ($error->code == DomainAPI::ANSWER_CONTACT_CREATE_ERROR_BDATE) { ?>
                        <p><?= $_->l('Дата рождения владельца указана не верно!') ?></p>
                    <? } ?>
                    <? if ($error->code == DomainAPI::ANSWER_CONTACT_CREATE_ERROR_PASSPORT) { ?>
                        <p><?= $_->l('Поля "Серия и номер паспорта", "Дата выдачи" или "Кем выдан паспорт" не указаны или указаны не верно!') ?></p>
                    <? } ?>
                </div>
                <? } elseif ($error->type == 'no_selected_owner') { ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-alert"></span>
                        <?=$_->l('Не выбран владелец!')?>
                    </div>
                <? } elseif ($error->type == 'no_selected_ns') { ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-alert"></span>
                        <?=$_->l('Нужно указать как минимум 2 NS сервера!')?>
                    </div>
                <? } ?>
            <? } ?>
        <? } ?>

        <form id="domains_period" method="post">
            <table class="table">
                <thead>
                <tr>
                    <th width="10%"><?=$_->l('Домен')?></th>
                    <th width="10%"><?=$_->l('Период регистрации')?></th>
                    <th width="40%"><?=$_->l('Владелец')?></th>
                    <th width="40%"><?=$_->l('NS сервера')?></th>
                </tr>
                </thead>
                <? foreach ($orders as $order) { ?>
                    <tr>
                        <td><input type="hidden" name="order[]" value="<?= $order->id ?>"><?= $order->domain ?></td>
                        <td>
                            <select name="period[<?= $order->id ?>]">
                                <? for ($i = $order->data->min_period; $i <= $order->data->max_period; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i ?> год</option>
                                <? } ?>

                            </select>
                        </td>
                        <td>
                            <select class="owner_id" id="owner_id<?= $order->id ?>" name="owner_id[<?= $order->id ?>]" style="width:50%;">
                                <option value=""><?=$_->l('Не выбрано')?></option>
                                <? foreach ($owners as $owner) { ?>
                                    <option
                                        value="<?= $owner->id ?>" <?= isset($_->rpost('owner_id')[$order->id]) && $_->rpost('owner_id')[$order->id] == $owner->id ? 'selected="selected"' : '' ?>><?= $owner->fio ?> (ID: <?=$owner->id?>)</option>
                                <? } ?>
                            </select>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-success ajax-modal" href="<?=$_->link('domain-owner/add')?>" data-order="<?= $order->id ?>">
                                <span class="glyphicon glyphicon-plus-sign"></span> <?=$_->l('Добавить')?>
                            </a>
                            <a class="btn btn-xs btn-info" href="<?=$_->link('setting/domain-owners')?>" data-order="<?= $order->id ?>">
                                <span class="glyphicon glyphicon-user"></span> <?=$_->l('Управление')?>
                            </a>
                        </td>
                        <td>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <input class="form-control" name="ns1[<?= $order->id ?>]" type="text"
                                           data-validate="domain" placeholder="ns1.hopebilling.com"
                                           value="<?= $config->ns1 ?>">
                                </div>
                                <div class="form-group col-lg-6">
                                    <input class="form-control" name="ns2[<?= $order->id ?>]" type="text"
                                           data-validate="domain" placeholder="ns2.hopebilling.com"
                                           value="<?= $config->ns2 ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <input class="form-control" name="ns3[<?= $order->id ?>]" type="text"
                                           data-validate="domain" data-validate-allow-empty="1"
                                           placeholder="ns3.hopebilling.com" value="<?= $config->ns3 ?>">
                                </div>
                                <div class="form-group col-lg-6">
                                    <input class="form-control" name="ns4[<?= $order->id ?>]" type="text"
                                           data-validate="domain" data-validate-allow-empty="1"
                                           placeholder="ns4.hopebilling.com" value="<?= $config->ns4 ?>">
                                </div>
                            </div>
                        </td>
                    </tr>
                <? } ?>
            </table>


            <div class="form-group">
                <label><input type="checkbox" name="promocode_on">
                    <?= $_->l('Использовать промокод') ?></label>
            </div>

            <div class="form-group promocode-inp-inner">
                <label><?= $_->l('Промокод') ?></label>
                <input type="text" name="promocode" data-validate-message-fail-ajax="<?=$_->l("Промокод уже использован или недействительный")?>" class="form-control" placeholder="<?=$_->l('Промокод')?>" data-validate="required|ajax">
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary pull-right" onclick="$('#domains_period').submit();"> <?=$_->l('Продолжить')?></button>
    </div>
</div>

</div>