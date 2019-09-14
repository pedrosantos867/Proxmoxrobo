<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="public_key"><?=$_->l('Public Key')?></label>
            <input type="text" id="public_key" name="public_key"
                   value="<?= isset($pconfig->liqpay->public_key) ? $pconfig->liqpay->public_key : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Public Key')?>">
        </div>
        <div class="form-group">
            <label for="private_key"><?=$_->l('Private Key')?></label>
            <input type="text" id="private_key" name="private_key"
                   value="<?= isset($pconfig->liqpay->private_key) ? $pconfig->liqpay->private_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Private Key')?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>