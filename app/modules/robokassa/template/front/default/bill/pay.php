
<? if (isset($robokassa)) { ?>
    <form action="<?= $robokassa->getFormAction(); ?>" method="post">
        <?php foreach ($robokassa->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>

        <button type="submit" class="payment_button"
                style="background: url(<?= $_->path('img/robokassa.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>

