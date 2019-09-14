<? if (isset($perfectmoney)) { ?>
    <form action="<?= $perfectmoney->getFormAction(); ?>" method="post">
        <?php foreach ($perfectmoney->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/perfectmoney/template/front/default/img/perfectmoney.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>