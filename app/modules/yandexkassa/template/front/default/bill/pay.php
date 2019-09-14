<? if (isset($yandexkassa)) { ?>
    <form action="<?= $yandexkassa->getFormAction(); ?>" method="get">
        <?php foreach ($yandexkassa->getFormValues() as $field => $value): ?>
            <input type="hidden" name="<?= $field; ?>" value="<?= $value; ?>"/>
        <?php endforeach; ?>
        <button type="submit" class="payment_button"
                style="background: url(<?= $_->link('app/modules/yandexkassa/template/front/default/img/yandexkassa.png') ?>) no-repeat center;"></button>
    </form>
<? } ?>