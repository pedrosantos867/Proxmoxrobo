<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="payee_name"><?=$_->l('Payee Name')?></label>
            <input type="text" id="payee_name" name="payee_name"
                   value="<?= isset($pconfig->perfectmoney->payee_name) ? $pconfig->perfectmoney->payee_name : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Payee Name')?>">
        </div>
        <div class="form-group">
            <label for="payee_account"><?=$_->l('Payee Account')?></label>
            <input type="text" id="payee_account" name="payee_account"
                   value="<?= isset($pconfig->perfectmoney->payee_account) ? $pconfig->perfectmoney->payee_account : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Payee Account')?>">
        </div>
        <div class="form-group">
            <label for="alternate_passphrase"><?=$_->l('Alternate Passphrase')?></label>
            <input type="text" id="alternate_passphrase" name="alternate_passphrase"
                   value="<?= isset($pconfig->perfectmoney->alternate_passphrase) ? $pconfig->perfectmoney->alternate_passphrase : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Alternate Passphrase')?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>