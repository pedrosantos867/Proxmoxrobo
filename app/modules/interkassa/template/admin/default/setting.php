<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="disabledTextInput"><?=$_->l('ID магазина')?></label>
            <input type="text" id="disabledTextInput" name="id"
                   value="<?= isset ($pconfig->interkassa->id) ? $pconfig->interkassa->id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('ID магазина')?>">
        </div>
        <div class="form-group">
            <label for="disabledSelect"><?=$_->l('Секретный ключ')?></label>
            <input type="text" id="disabledTextInput" name="secret_key"
                   value="<?= isset($pconfig->interkassa->secret_key) ? $pconfig->interkassa->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретный ключ')?>">
        </div>

        <div class="form-group">
            <label for="disabledSelect"><?=$_->l('Тестовый секретный ключ')?></label>
            <input type="text" id="disabledTextInput" name="test_secret_key"
                   value="<?= isset($pconfig->interkassa->test_secret_key) ? $pconfig->interkassa->test_secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Тестовый секретный ключ')?>">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="test_mode" <?= (isset($pconfig->interkassa->test_mode) && $pconfig->interkassa->test_mode ? 'checked="checked"' : '') ?>
                       value="1">
                <?=$_->l('Режим тестирования')?> <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                                       data-placement="right" title=""
                                                       data-original-title="<?=$_->l('При проведении платежа транзакция не совершается! Меняется только состояние выставленного Вами счета, без зачисления суммы платежа на Ваш баланс.')?>"></span>
            </label>
        </div>
        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->interkassa->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
    </fieldset>
</form>