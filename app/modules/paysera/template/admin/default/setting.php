

<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="projectid"><?=$_->l('ProjectID')?></label>
            <input type="text" id="projectid" name="projectid"
                   value="<?= (isset($pconfig->paysera->projectid) && $pconfig->paysera->projectid!=0) ? $pconfig->paysera->projectid : '' ?>"
                   class="form-control" placeholder="<?=$_->l('ProjectID')?>">
        </div>
        <div class="form-group">
            <label for="sign_password"><?=$_->l('Sign_password')?></label>
            <input type="text" id="sign_password" name="sign_password"
                   value="<?= isset($pconfig->paysera->sign_password) ? $pconfig->paysera->sign_password : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Sign_password')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->paysera->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <? }?>
            </select>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="test" <?= (isset($pconfig->paysera->test) && $pconfig->paysera->test ? 'checked="checked"' : '') ?>
                       value="1">
                <?=$_->l('Режим тестирования')?> <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                                       data-placement="right" title=""
                                                       data-original-title="<?=$_->l('При проведении платежа транзакция не совершается! Меняется только состояние выставленного Вами счета, без зачисления суммы платежа на Ваш баланс.')?>"></span>
            </label>
        </div>
                <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>