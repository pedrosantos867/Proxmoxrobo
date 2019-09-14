<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="id"><?=$_->l('ID мерчанта')?></label>
            <input type="text" id="id" name="id"
                   value="<?= isset($pconfig->privat24->id) ? $pconfig->privat24->id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('ID мерчанта')?>">
        </div>
        <div class="form-group">
            <label for="secret_key"><?=$_->l('Секретный ключ')?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->privat24->secret_key) ? $pconfig->privat24->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретный ключ')?>">
        </div>
        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->privat24->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>