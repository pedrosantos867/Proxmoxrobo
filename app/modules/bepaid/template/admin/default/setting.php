<form method="post">
    <fieldset>
        <div class="form-group">
            <label><?=$_->l('ID магазина')?></label>
            <input type="text" id="disabledTextInput" name="shop_id"
                   value="<?= isset($pconfig->bepaid->shop_id) ? $pconfig->bepaid->shop_id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('ID магазина')?>">
        </div>

        <div class="form-group">
            <label><?=$_->l('Ключ магазина')?></label>
            <input type="text" id="disabledTextInput" name="key"
                   value="<?= isset($pconfig->bepaid->key) ? $pconfig->bepaid->key : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Ключ магазин')?>">
        </div>

        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->bepaid->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>