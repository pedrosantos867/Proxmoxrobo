<form action="<?= $_->link('modules/stripe/pay/' . $id_bill) ?>" method="post">
    <script src="https://checkout.stripe.com/checkout.js"></script>

    <button class="payment_button"
            style="background: url(<?= $_->link('app/modules/stripe/template/front/default/img/stripe.png') ?>) no-repeat center;"
            id="customButton"></button>
    <script>
        $('#customButton').click(function () {
            var token = function (res) {
                var $input = $('<input type=hidden name=stripeToken />').val(res.id);
                $('form').append($input).submit();
            };

            StripeCheckout.open({
                key: '<?php echo $stripe['publishable_key']; ?>',
                address: false,
                amount: '<?php echo $total; ?>',
                currency: '<?php echo $stripe_currency; ?>',
                name: '<?=$_->l('Услуги хостинг компании')?>',
                description: '<?=$_->l('Оплата счета № ' . $id_bill)?>',
                panelLabel: '<?=$_->l('Оплатить')?>',
                token: token
            });

            return false;
        });
    </script>
    <input type="hidden" name="desc" value="<?= $_->l('Оплата счета № ' . $id_bill) ?>"/>
    <input type="hidden" name="total" value="<?php echo $total; ?>"/>
    <input type="hidden" name="stripe_currency" value="<?php echo $stripe_currency; ?>"/>

</form>
