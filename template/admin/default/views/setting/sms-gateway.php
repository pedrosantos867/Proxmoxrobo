<form method="post">
    <div class="radio">
        <label>
            <input type="radio" name="sms-gateway" <?= ($cfg->sms_gateway == 'none') ? 'checked="checked"' : '' ?>
                   value="none" >
            <?=$_->l('Не использовать')?>
        </label>
    </div>

    <div class="radio">
        <label>
            <input type="radio" name="sms-gateway" <?= ($cfg->sms_gateway == 'turbosms') ? 'checked="checked"' : '' ?>
                   value="turbosms" >
            TurboSMS.UA <a href="<?= $_->link('admin/settings/sms-gateway/turbosms') ?>">Настройки</a>
        </label>
    </div>
    <div class="radio">
        <label>
            <input type="radio" name="sms-gateway" <?= ($cfg->sms_gateway == 'smsc') ? 'checked="checked"' : '' ?>
                   value="smsc">
            SMSC.RU <a href="<?= $_->link('admin/settings/sms-gateway/smsc') ?>"><?=$_->l('Настройки')?></a>
        </label>
    </div>
    <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
</form>