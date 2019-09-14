
<form method="post">
    <div class="checkbox">
        <input type="hidden" name="submit" value="1">
        <label>
            <input
                type="checkbox" <?= (($config->enable_client_sms_notification_control) ? 'checked="checked"' : '') ?>
                name="enable_client_sms_notification_control" value="1" class="checkbox">

            <?=$_->l('Разрешить клиентам управлять SMS оповещениями')?>
        </label>
    </div>

    <div class="checkbox">
        <label>
            <input
                type="checkbox" <?= (($config->enable_client_email_notification_control) ? 'checked="checked"' : '') ?>
                name="enable_client_email_notification_control" value="1" class="checkbox">

            <?=$_->l('Разрешить клиентам управлять Email оповещениями')?>
        </label>
    </div>
    <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>

</form>