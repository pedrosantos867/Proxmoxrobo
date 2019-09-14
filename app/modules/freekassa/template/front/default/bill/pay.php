
<? if (isset($freekassa)) { ?>
    <form action="<?= $freekassa->getFormAction(); ?>" method="get">
        <?php foreach ($freekassa->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/freekassa/template/front/default/img/freekassa.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>