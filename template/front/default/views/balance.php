<style>
    .current_balance > h2 {
        font-size: 26px;
        text-shadow: 1px 1px 1px #000;
        border-bottom: 1px solid #000;
        position: relative;
    }

    .current_balance > h2:after {
        content: "";
        position: absolute;
        bottom: -1px;
        left: 0;
        height: 1px;
        width: 50%;
        background-color: rgb(255, 116, 0);
    }

    .current_balance > h2 > span {
        font-size: 28px;
        text-shadow: 1px 1px 1px #fff, 2px 2px 2px #000;
        margin-left: 10px;
    }

    .current_balance > a {
        transition: background-color 0.3s, color 0.3s;
    }

    .current_balance > a:hover {
        background-color: #333;
        color: #fff;
        border-color: rgb(255, 116, 0);
    }
</style>

<?= $_->JS('validator.js') ?>
<script>
    $(function () {
        $('form').validate({messages: validate_messages});
    })
</script>

<div class="current_balance">
    <h2><?= $_->l('Ваш баланс составляет:') ?> <span><?= $currency->displayPrice($client->balance) ?>
            </span></h2>


    <div class="text-center">
        <div class="col-lg-5"></div>
        <div class="col-lg-2">
            <form class="form-balance">
                <div class="form-group">
                    <input type="number" class="form-control" id="summ" data-validate="possitive_number" value="10"
                           placeholder="<?= $currency->displayPrice(10) ?>">
                </div>
            </form>

            <span class="input-group-btn">
                <a href="#" class="btn btn-warning top-up" type="button">
                    <?= $_->l('Пополнить!') ?>
                </a>
            </span>
            <script>
                $('.top-up').on('click', function () {
                    if(Number($('#summ').val()) > 0) {
                        location.href = '<?= $_->link('balance/create-bill') ?>/' + Number($('#summ').val());
                    }
                })
            </script>
        </div>
        <div class="col-lg-5"></div>
        <!-- /input-group -->
    </div>
    <!-- /.col-lg-6 -->
</div>