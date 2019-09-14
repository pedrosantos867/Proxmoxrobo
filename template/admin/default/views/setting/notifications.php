<?= $_->JS('bootstrap-switch.min.js') ?>
<?= $_->CSS('bootstrap-switch.css') ?>
<script>
    $(function () {
        $('input.checkbox').bootstrapSwitch({size: 'mini'});

    })
</script>


<div class="top-menu">
    
    <div class="text-right">
        <a href="<?= $_->link('admin/settings/notifications/setting') ?>" class="btn btn-default"><span
                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Настройки уведомлений')?></a>

    </div>
</div>

<form action="" method="post" class="form">
    <table class="table table-bordered" style="width: 100%">
        <thead>

            <tr>
                <td colspan="3" class="styled_td" style="width: 50%"><b><?= $_->l('Настройка уведомлений для администратора') ?></b></td>
            </tr>
        </thead><thead>
            <tr>
                <td  class="styled_td" style="width: 50%"><b><?= $_->l('Описание') ?></b></td>
                <td  class="styled_td" style="text-align: center;"><b><?= $_->l('Email') ?></b></td>
                <td  class="styled_td" style="text-align: center;"><b><?= $_->l('SMS') ?></b></td>
            </tr>

        </thead>
        <tbody>
        <tr>
            <td><?= $_->l('Уведомлять о новом заказе (администратору)') ?></td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($email_notifications['new_order']) ? 'checked="checked"' : '') ?>
                            name="email_notifications[new_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($sms_notifications['new_order']) ? 'checked="checked"' : '') ?>
                            name="sms_notifications[new_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>


        <tr>
            <td><?= $_->l('Уведомлять о создании нового тикета (администратору)') ?></td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($email_notifications['new_ticket']) ? 'checked="checked"' : '') ?>
                            name="email_notifications[new_ticket]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($sms_notifications['new_ticket']) ? 'checked="checked"' : '') ?>
                            name="sms_notifications[new_ticket]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>

        <tr>
            <td><?= $_->l('Уведомлять о новых ответах к тикетам (администратору)') ?></td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($email_notifications['ticket_answer']) ? 'checked="checked"' : '') ?>
                            name="email_notifications[ticket_answer]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td style="text-align: center;">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($sms_notifications['ticket_answer']) ? 'checked="checked"' : '') ?>
                            name="sms_notifications[ticket_answer]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>

        </tbody>
        <thead>
        <tr>
            <td colspan="3" class="styled_td" style="width: 50%"><b><?= $_->l('Настройка клиентских уведомлений (Внимание! Клиенты смогут индивидуально изменять значения, если это разрешено)') ?></b></td>
        </tr></thead>

        <thead>
            <tr>
                <td  class="styled_td" style="width: 50%"><b><?= $_->l('Описание') ?></b></td>
                <td  class="styled_td" style="text-align: center;"><b><?= $_->l('Email') ?></b></td>
                <td  class="styled_td" style="text-align: center;"><b><?= $_->l('SMS') ?></b></td>
            </tr>
        </thead>

        <tbody>
        <tr>
            <td><?= $_->l('Уведомлять о выставлении нового счета:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['new_bill']) ? 'checked="checked"' : '') ?>
                            name="notifications[new_bill]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_new_bill']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_new_bill]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять об окончании услуг хостинга:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['end_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[end_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_end_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_end_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять о блокировке хостинг аккаунта:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['suspend_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[suspend_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_suspend_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_suspend_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять о разблокировке хостинг аккаунта:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['unsuspend_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[unsuspend_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_unsuspend_hosting_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_unsuspend_hosting_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять о создании новых тикетов:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['new_ticket']) ? 'checked="checked"' : '') ?>
                            name="notifications[new_ticket]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_new_ticket']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_new_ticket]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять об ответах на тикеты:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['ticket_answer']) ? 'checked="checked"' : '') ?>
                            name="notifications[ticket_answer]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_ticket_answer']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_ticket_answer]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td><?= $_->l('Уведомлять клиента об добавлении и изменении информации о заказе:') ?></td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['info_service_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[info_service_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox" <?= (isset($notifications['sms_info_service_order']) ? 'checked="checked"' : '') ?>
                            name="notifications[sms_info_service_order]" value="1" class="checkbox">
                    </label>
                </div>
            </td>
        </tr>
        </tbody>

    </table>
    <button type="submit" class="btn btn-default" name="save"><span
            class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
    </button>
</form>