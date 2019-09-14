<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="purse"><?=$_->l('Кошелек')?></label>
            <input type="text" id="purse" name="purse"
                   value="<?= isset($pconfig->webmoney->purse) ? $pconfig->webmoney->purse : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Кошелек')?>">
        </div>
        <div class="form-group">
            <label for="secret_key"><?=$_->l('Секретный ключ')?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->webmoney->secret_key) ? $pconfig->webmoney->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретный ключ')?>">
        </div>

        <div class="form-group">
            <label for="secret_key"><?=$_->l('Секретный ключ X20')?></label>
            <input type="text" id="secret_keyx20" name="secret_keyx20"
                   value="<?= isset($pconfig->webmoney->secret_keyx20) ? $pconfig->webmoney->secret_keyx20 : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Секретный ключ')?>">
        </div>


        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>