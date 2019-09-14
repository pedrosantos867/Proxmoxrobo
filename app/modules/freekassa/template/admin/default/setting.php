<form method="post">
    <fieldset>
        <div class="form-group">
            <label><?=$_->l('ID магазина')?></label>
            <input type="text" id="disabledTextInput" name="shop_id"
                   value="<?= isset($pconfig->freekassa->shop_id) ? $pconfig->freekassa->shop_id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('ID магазина')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Секретное слово')?></label>
            <input type="text" id="disabledTextInput" name="secret_key"
                   value="<?= isset($pconfig->freekassa->secret_key) ? $pconfig->freekassa->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретное слово')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Секретное слово 2')?></label>
            <input type="text" id="disabledTextInput" name="secret_key2"
                   value="<?= isset($pconfig->freekassa->secret_key2) ? $pconfig->freekassa->secret_key2 : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретное слово 2')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->freekassa->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>