<? if (isset($paysera)) { ?>
    <form action="<?= $paysera->getFormAction(); ?>" method="get">
        <?php foreach ($paysera->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/paysera/template/front/default/img/paysera.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>