<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="public_key"><?=$_->l('PUBLIC KEY')?></label>
            <input type="text" id="public_key" name="public_key"
                   value="<?= isset($pconfig->unitpay->public_key) ? $pconfig->unitpay->public_key : '' ?>"
                   class="form-control" placeholder="<?=$_->l('PUBLIC KEY')?>">
        </div>
        <div class="form-group">
            <label for="secret_key"><?=$_->l('SECRET KEY')?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->unitpay->secret_key) ? $pconfig->unitpay->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('SECRET KEY')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->unitpay->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>