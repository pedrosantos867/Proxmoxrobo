<?= $_->JS('bootstrap-switch.min.js') ?>
<?= $_->CSS('bootstrap-switch.css') ?>
<script>
    $(function () {
        $('input[name="currency-refresh"]').bootstrapSwitch();
        $('input[name="currency-refresh"]').on('switchChange.bootstrapSwitch', function (event, state) {
            $.ajax({
                method: 'post',
                data: {action: 'currencyRefreshState', state: ((state == true) ? 1 : 0), ajax: 1}

            })
        });
    })
</script>
<div>
    <div class="top-menu">
        <a href="<?= $_->link('admin/settings/currency/add') ?>" class="btn btn-default"><span
                class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?></a>

        <a href="<?= $_->link('admin/settings/currency/refresh') ?>" class="btn btn-default"><span
                class="glyphicon glyphicon-refresh" aria-hidden="true"></span><?=$_->l('Обновить курс валют')?></a>

        <a href="<?= $_->link('admin/settings/currencies/setting') ?>" class="btn btn-default"><span
                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Настройки обновления')?></a>

        <div class="pull-right">
            <?=$_->l('Автоматически обновлять курс валют каждый день:')?> <input
                type="checkbox" <?= ($config->currency_refrash ? 'checked="checked"' : '') ?> name="currency-refresh"
                data-size="small">
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th><?=$_->l('Название')?></th>
            <th><?=$_->l('Курс')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($currencies as $currency) { ?>
            <tr>
                <td><?= $currency->id ?></td>
                <td><?= $currency->name ?></td>
                <td><?= $currency->coefficient ?></td>
                <td>
                    <a href="<?= $_->link('admin/settings/currency/' . $currency->id) ?>" class="btn btn-default"><span
                            class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Изменить')?></a>
                    <? if ($currency->id != $config->currency_default) { ?>
                    <a href="<?= $_->link('admin/settings/currency/remove/' . $currency->id) ?>"
                       class="btn btn-danger ajax-action"><span class="glyphicon glyphicon-trash"
                                                                aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    <? } ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>