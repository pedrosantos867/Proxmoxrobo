<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="client_id"><?=$_->l('Client ID')?></label>
            <input type="text" id="client_id" name="client_id"
                   value="<?= isset($pconfig->paypal->client_id) ? $pconfig->paypal->client_id : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Client ID')?>">
        </div>
        <div class="form-group">
            <label for="secret"><?=$_->l('Secret')?></label>
            <input type="text" id="secret" name="secret"
                   value="<?= isset($pconfig->paypal->secret) ? $pconfig->paypal->secret : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Secret')?>">
        </div>
        <div class="form-group">
            <label for="percent"><?= $_->l('Additional percent') ?></label>
            <input type="text" id="percent" name="percent"
                   value="<?= isset($pconfig->paypal->percent) ? $pconfig->paypal->percent : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('Percent') ?>">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="test_mode" value="1" <?= ($pconfig->paypal->test_mode==1) ? 'checked="checked"' : '' ?>> <?=$_->l('Использовать тестовый сервер')?>
            </label>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>