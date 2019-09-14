<? if (isset($easypay)) { ?>
    <form action="<?= $easypay->getFormAction(); ?>" method="post">
        <?php foreach ($easypay->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/easypay/template/front/default/img/easypay.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>