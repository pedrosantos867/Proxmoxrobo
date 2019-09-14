<? if (isset($cryptonator)) { ?>
    <form action="<?= $cryptonator->getFormAction(); ?>" method="get">
        <?php foreach ($cryptonator->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/cryptonator/template/front/default/img/cryptonator.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>