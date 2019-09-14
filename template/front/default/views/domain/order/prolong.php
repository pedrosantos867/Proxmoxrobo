<? if ($error == 'bill_exist') { ?>
    <div class="alert alert-danger" role="alert">

        <?= $_->l('У Вас есть неоплаченные счета по данному заказу. Вы должны оплатить или отменить их.') ?>
        <a href="<?= $_->link('bills?id_domain_order=' . $order->id) ?>" class="alert-link">
            <?=$_->l('Нажмите здесь чтобы просмотреть счета по заказу №%bill', array('bill'=> $order->id))?>
        </a>
    </div>
<? } ?>

<form method="post">
    <div class="form-group">
        <label>Период продления</label>
        <select name="pay_period" class="form-control">
            <? for ($i = $domain->min_extension_period; $i <= $domain->max_extension_period; $i++) { ?>
                <option value="<?= $i ?>"><?= $i ?> <?= $_->l('{'.$i.'|год|года|год}') ?> - <?= $currency->displayPrice($domain->extension_price * $i) ?></option>
            <? } ?>


        </select>
    </div>

    <div >
        <a href="<?=$_->link('domain-orders')?>" class="btn btn-danger">
            <span class="glyphicon glyphicon-remove-circle "></span> &nbsp;
            <?= $_->l('Отмена') ?>

        </a>
        <button type="submit" class="btn btn-success pull-right"  <?=($error == 'bill_exist' ? 'disabled="disabled"' : '') ?>>
            <?= $_->l('Сформировать заказ') ?> &nbsp;
            <span class="glyphicon glyphicon-arrow-right"></span>
        </button>
    </div>


</form>