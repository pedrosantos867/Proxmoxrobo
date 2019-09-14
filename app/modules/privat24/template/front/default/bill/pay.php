<? if (isset($privat24)) { ?>
    <form action="<?= $privat24->getFormAction(); ?>" method="post">
        <?php foreach ($privat24->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->path('img/privat24.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>