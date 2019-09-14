<?= $_->JS('validator.js') ?>

<?= $_->JS('slider/bootstrap-slider.min.js') ?>
<?= $_->CSS('slider/bootstrap-slider.min.css') ?>

<script>
    var sprice = '<?=$service->price?>';
    var changed_price = [];

    $(function () {
        $('form').validate({messages: validate_messages});

    })
</script>
<script>
    function calculateResultPrice(){
        var addition_sum = 0;
        for(var i in changed_price){
            addition_sum+=changed_price[i];
        }
        console.log('addition sum: '+addition_sum);

        $('select[name=pay_period] option').each(function (i, option) {
            var new_price = parseInt($(option).data('period')) * (parseFloat(sprice) + addition_sum);
            $(option).text($(option).data('text') + ' - ' + currency.displayPrice(new_price));

            $(option).data('price', new_price);
        });

    }



</script>
<form method="post">

    <?foreach($fields as $field){?>
        <div class="form-group">
            <?if($field->type == 1){?>
                <label><?=$field->name?></label>
                <input type="text" name="<?=$field->id?>" data-validate="<?=$field->validate?>" class="form-control" >
            <? } elseif($field->type == 2){ ?>
                <label><?=$field->name?></label>
                <input type="password" name="<?=$field->id?>" class="form-control" >
            <? } elseif($field->type == 3){ ?>
                <label><?=$field->name?></label>
                <textarea data-validate="<?=$field->validate?>"  name="<?=$field->id?>" class="form-control"></textarea>
            <? } elseif($field->type == 4 && $field->id){  ?>

                <label><?=$field->name?></label>
                <select id="select<?=$field->id?>" data-validate="<?=$field->validate?>" name="<?=$field->id?>" class="form-control select">
                    <option value=""> --- </option>
                    <?foreach ( $field->values as $id => $val){?>
                        <option value="<?=$id?>" data-price="<?=$val->price?>"><?=$val->value?> <?=$val->price ? '('.$currency->displayPrice($val->price) . ($service->type == 0 ? '/месяц':'').')' : ''?></option>
                    <?}?>
                </select>

                <script>
                    $('#select<?=$field->id?>').on('change', function () {
                        var price = parseFloat($(this).find('option:selected').data('price'));
                        if(isNaN(price)) {
                            price = 0;
                        }
                        changed_price[<?=$field->id?>] = price;
                        calculateResultPrice();

                    })
                </script>

            <? } elseif($field->type == 5 && $field->id){  ?>
                <label><?=$field->name?></label>
                <div>
                <input id="range<?=$field->id?>" name="<?=$field->id?>" data-slider-id='ex1Slider' type="text" data-slider-min="<?=$field->slider->from?>" data-slider-max="<?=$field->slider->to?>" data-slider-step="<?=$field->slider->step?>" data-slider-value="<?=$field->slider->from?>"/>
                </div>
                <script>

                    var price<?=$field->id?> = '<?=$field->slider->price?>';
                    // Without JQuery
                    var slider<?=$field->id?> = new Slider('#range<?=$field->id?>', {
                        formatter: function(value) {
                            return  value;
                        }
                    });
                    slider<?=$field->id?>.on("slide", function(slideEvt) {

                        changed_price[<?=$field->id?>] = slideEvt*price<?=$field->id?>;

                        calculateResultPrice();
                    });
                    var value<?=$field->id?> = slider<?=$field->id?>.getValue();
                    changed_price[<?=$field->id?>] = value<?=$field->id?>*price<?=$field->id?>;
                </script>
            <? } ?>
        </div>
    <?}?>

    <script>
        $(function () {
            calculateResultPrice();
        });
    </script>






    <?if($service->type == 0){?>
        <div class="form-group">
            <label><?= $_->l('Период оплаты') ?></label>
            <select name="pay_period" class="form-control">
                <option data-period="1" data-price="<?=$service->price*1?>" data-text="1 <?= $_->l('месяц') ?>" value="1">1 <?= $_->l('месяц') ?>
                    - <?= $currency->displayPrice($service->price) ?>       </option>
                <option data-period="2" data-price="<?=$service->price*2?>" data-text="2 <?= $_->l('месяца') ?>" value="2">2 <?= $_->l('месяца') ?>
                    - <?= $currency->displayPrice($service->price * 2) ?>   </option>
                <option data-period="6" data-price="<?=$service->price*6?>" data-text="6 <?= $_->l('месяцев') ?>" value="6">6 <?= $_->l('месяцев') ?>
                    - <?= $currency->displayPrice($service->price* 6 ) ?>   </option>
                <option data-period="12" data-price="<?=$service->price*12?>" data-text="12 <?= $_->l('месяцев') ?>" value="12">12 <?= $_->l('месяцев') ?>
                    - <?= $currency->displayPrice($service->price * 12) ?>  </option>
            </select>
        </div>
    <?}?>

    <div class="form-group">
        <label><input type="checkbox" name="promocode_on">
            <?= $_->l('Использовать промокод') ?></label>
    </div>

    <div class="form-group promocode-inp-inner">
        <label><?= $_->l('Промокод') ?></label>
        <input type="text" name="promocode" class="form-control" placeholder="<?=$_->l('Промокод')?>" data-validate="ajax">
    </div>

    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-play-circle"></span> <?= $_->l('Сформировать заказ') ?>
    </button>
</form>