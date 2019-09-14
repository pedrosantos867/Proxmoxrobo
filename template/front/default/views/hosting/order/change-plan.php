<div class="alert alert-warning">
    <span class="glyphicon glyphicon-warning-sign"></span> <b><?= $_->l('Внимание!') ?></b><br>
    <?= $_->l('В результате изменения тарифного плана на тарифный план стоимость которого - больше, тогда будет сформирован счет на оплату разницы суммы!') ?>
    <br><?= $_->l('Новый тарифный план будет активирован после оплаты счета.') ?>
    <br><?= $_->l('Если стоимость тарифного плана - меньше, тогда счет выставляться не будет, а смена произойдет немедленно.') ?>
</div>

<form method="post">
    <div class="form-group">
        <label><?= $_->l('Тарифный план') ?></label>
        <select name="id_plan" class="form-control">
            <option> ---</option>
            <? foreach ($plans as $plan) { ?>
                <? if ($plan->id != $current_plan->id) { ?>
                    <option value="<?= $plan->id ?>"><?= $plan->name ?>
                        - <?= $_->l('месячная
                        доплата') ?> <?= (($currency->getPrice($plan->price-$current_plan->price)) > 0 ? ($currency->displayPrice($plan->price-$current_plan->price)) : '0') ?> </option>
                <? } ?>
            <? } ?>

        </select>
    </div>

    <button type="submit" class="btn btn-default"><?= $_->l('Сформировать заказ') ?></button>
</form>