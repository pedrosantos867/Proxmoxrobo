<? if (isset($unitpay)) { ?>
    <form action="<?= $unitpay->getFormAction(); ?>" method="get">
        <?php foreach ($unitpay->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->path('img/unitpay.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>