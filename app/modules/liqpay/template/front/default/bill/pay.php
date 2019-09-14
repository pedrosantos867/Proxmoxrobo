<? if (isset($liqpay)) { ?>
    <form action="<?= $liqpay->getFormAction(); ?>" method="get">
        <?php foreach ($liqpay->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/liqpay/template/front/default/img/liqpay.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>