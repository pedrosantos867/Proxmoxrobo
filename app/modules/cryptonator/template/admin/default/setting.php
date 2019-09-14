<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="item_name"><?=$_->l('Item name')?></label>
            <input type="text" id="item_name" name="item_name"
                   value="<?= isset($pconfig->cryptonator->item_name) ? $pconfig->cryptonator->item_name : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Item name')?>">
        </div>
        <div class="form-group">
            <label for="merchant_id"><?=$_->l('Merchant id')?></label>
            <input type="text" id="merchant_id" name="merchant_id"
                   value="<?= isset($pconfig->cryptonator->merchant_id) ? $pconfig->cryptonator->merchant_id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Merchant id')?>">
        </div>
        <div class="form-group">
            <label for="secret"><?=$_->l('Secret')?></label>
            <input type="text" id="secret" name="secret"
                   value="<?= isset($pconfig->cryptonator->secret) ? $pconfig->cryptonator->secret : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Secret')?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>