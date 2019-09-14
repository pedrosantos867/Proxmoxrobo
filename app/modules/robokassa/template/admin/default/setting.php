<form method="post">

        <fieldset>
            <div class="form-group">
                <label for="disabledTextInput"><?=$_->l('ID мерчанта')?></label>
                <input type="text" id="disabledTextInput" name="merchant"
                       value="<?= isset($pconfig->robokassa->merchant) ? $pconfig->robokassa->merchant : '' ?>"
                       class="form-control" placeholder="ID магазина">
            </div>
            <div class="form-group">
                <label for="disabledSelect"><?=$_->l('Пароль 1')?></label>
                <input type="text" id="disabledTextInput" name="password1"
                       value="<?= isset($pconfig->robokassa->password1) ? $pconfig->robokassa->password1 : '' ?>"
                       class="form-control"
                       placeholder="<?=$_->l('Пароль 1')?>">
            </div>
            <div class="form-group">
                <label for="disabledSelect"><?=$_->l('Пароль 2')?></label>
                <input type="text" id="disabledTextInput" name="password2"
                       value="<?= isset($pconfig->robokassa->password2) ? $pconfig->robokassa->password2 : '' ?>"
                       class="form-control"
                       placeholder="<?=$_->l('Пароль 2')?>">
            </div>




        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="test_mode" <?= (isset($pconfig->robokassa->test_mode) && $pconfig->robokassa->test_mode ? 'checked="checked"' : '') ?>
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
                    <option <?=$pconfig->robokassa->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
    </fieldset>
</form>