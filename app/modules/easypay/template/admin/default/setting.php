<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="public_key"><?=$_->l('Merchant ID')?></label>
            <input type="text" id="merchant_id" name="merchant_id"
                   value="<?= isset($pconfig->easypay->merchant_id) ? $pconfig->easypay->merchant_id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Merchant ID')?>">
        </div>
        <div class="form-group">
            <label for="private_key"><?=$_->l('Private Key')?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->easypay->secret_key) ? $pconfig->easypay->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Secret Key')?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>