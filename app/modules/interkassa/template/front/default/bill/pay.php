<? if (isset($interkassa)) { ?>
    <form action="<?= $interkassa->getFormAction(); ?>" method="post">
        <?php foreach ($interkassa->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->path('img/interkassa.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>
