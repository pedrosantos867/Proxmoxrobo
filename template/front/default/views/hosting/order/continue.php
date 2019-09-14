<div class="ajax-block">
<? if ($error == 'bill_exist') { ?>
    <div class="alert alert-danger" role="alert">

        <?= $_->l('У Вас есть неоплаченные счета по данному заказу. Вы должны оплатить или отменить их.') ?>
        <a href="<?= $_->link('bills?id_order=' . $order->id) ?>" class="alert-link">
        <?=$_->l('Нажмите здесь чтобы просмотреть счета по заказу №%bill', array('bill'=> $order->id))?>
        </a>
    </div>
<? } ?>
<form method="post">
    <div class="form-group">
        <label><?= $_->l('Период оплаты') ?></label>
        <select name="pay_period" class="form-control">
            <?if(!$prices){?>
                <option value="1">1 <?= $_->l('месяц') ?>
                    - <?= $currency->displayPrice($plan->price) ?>      </option>
                <option value="2">2 <?= $_->l('месяца') ?>
                    - <?= $currency->displayPrice($plan->price * 2)  ?>   </option>
                <option value="6">6 <?= $_->l('месяцев') ?>
                    - <?= $currency->displayPrice($plan->price * 6)  ?>   </option>
                <option value="12">12 <?= $_->l('месяцев') ?>
                    - <?= $currency->displayPrice($plan->price * 12)  ?>  </option>
            <?}else{?>
                <?foreach ($prices as $price){ ?>
                    <option value="<?=$price->period?>"><?=$price->name?>
                        - <?= $currency->displayPrice($price->price)  ?>  </option>
                <?}?>
            <?}?>
        </select>
    </div>

    <div >
        <a href="<?=$_->link('hosting-orders')?>" class="btn btn-danger">
            <span class="glyphicon glyphicon-remove-circle "></span> &nbsp;
            <?= $_->l('Отмена') ?>

        </a>
        <button type="submit" class="btn btn-success pull-right"
            <?=($error == 'bill_exist' ? 'disabled="disabled"' : '') ?>>

            <?= $_->l('Сформировать заказ') ?> &nbsp;
            <span class="glyphicon glyphicon-arrow-right"></span>
        </button>
    </div>
</form>
</div>