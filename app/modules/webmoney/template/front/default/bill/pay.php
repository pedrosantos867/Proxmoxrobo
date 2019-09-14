<? if (isset($webmoney)) { ?>
    <form action="<?= $webmoney->getFormAction(); ?>" method="post" accept-charset="windows-1251">
        <?php foreach ($webmoney->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->path('img/webmoney.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>
