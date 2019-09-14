
<? if (isset($bepaid)) { ?>
    <form action="<?= $bepaid->getFormAction(); ?>" method="get">
        <a type="submit" class="payment_button" href="<?= $bepaid->getFormAction(); ?>"
           style="background: url(<?= $_->path('img/bepaid.png') ?>) no-repeat center;"></a>
    </form>
<? } ?>
