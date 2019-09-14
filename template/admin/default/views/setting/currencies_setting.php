<?
use \model\Currency;

?>
<form method="post">
    <fieldset>


        <div class="form-group">
            <label for="disabledTextInput"><?=$_->l('Основная валюта')?></label>
            <select class="form-control" name="currency_default">
                <? foreach ($currencies as $currency) { ?>
                    <option <?= ($config->currency_default == $currency->id ? 'selected="selected"' : '') ?>
                        data-iso="<?= $currency->iso ?>" value="<?= $currency->id ?>"><?= $currency->name ?></option>
                    <? if ($config->currency_default == $currency->id) {
                        $default = $currency->iso;
                    } ?>
                <? } ?>
            </select>
        </div>


        <script>
            var currencies = {
                'UAH': {
                    '<?= Currency::SERVER_PRIVAT24 ?>': '<?=$_->l('Приват24')?>',
                    '<?= Currency::SERVER_NBU ?>': '<?=$_->l('НБУ')?>'
                },
                'RUR': {
                    '<?= Currency::SERVER_CBR ?>': '<?=$_->l('Центральный банк Российской Федерации')?>'
                },
                'EUR': {
                    '<?= Currency::SERVER_ECB ?>': '<?=$_->l('European Central Bank')?>'
                }
            };

            $(function () {
                $('select[name=currency_default]').on('change', function () {
                    var iso = ($(this).find('option:checked').data('iso'));
                    $('select[name=currency_server]').html('');
                    for (var i in currencies[iso]) {
                        $('select[name=currency_server]').append('<option value="' + i + '">' + currencies[iso][i] + '</option>');
                    }
                    var currency_server = '<?=$config->currency_server?>';

                    $('select[name=currency_server] option[value=' + currency_server + ']').attr('selected', 'selected');
                }).trigger('change');
            });

        </script>

        <div class="form-group">
            <label for="disabledTextInput"><?= $_->l('Сервер для получения курса валют') ?></label>
            <select class="form-control" name="currency_server">

            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
    </fieldset>
</form>