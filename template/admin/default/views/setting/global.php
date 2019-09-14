
<form method="post">
    <h3><?=$_->l('Общие настройки')?></h3>

    <div class="form-group">
        <label for="sitename"><?=$_->l('Название сайта')?></label>
        <input type="sitename" class="form-control" name="sitename" id="sitename" placeholder="Название сайта"
               value="<?= $config->sitename ?>">
    </div>

    <div class="form-group">
        <label for="site_email"><?=$_->l('Email-адрес для уведомлений')?></label>
        <input type="email" class="form-control" name="site_email" placeholder="<?=$_->l('Email-адрес')?>"
               value="<?= $config->site_email ?>">
    </div>

    <div class="form-group">
        <label for="site_sms"><?=$_->l('Номер телефона для SMS уведомлений')?></label>
        <input type="text" class="form-control" name="site_sms" placeholder="<?=$_->l('Номер телефона в формате +380631235476')?>"
               value="<?= $config->site_sms ?>">
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->only_ssl == 1 ? 'checked="checked"' : '') ?>
                   name="only_ssl" value="1"> <?=$_->l('Принудительно использовать SSL')?>
            <div class="alert-danger"><?=$_->l('Внимание, перед этим убедитесь, что сайт доступен по протоколу https')?></div>
        </label>
    </div>

    <h3><?= $_->l('CRON') ?></h3>

    <div class="form-group">

        <input class="form-control" readonly="readonly"
               value="0 1 * * * php -q <?php echo \System\Path::getRoot('app/cron.php') ?>">
        <span class="help-inline"
              style="color: #a94442;"><?= $_->l('Внимание, CRON запись действует только для локального запуска') ?></span>
    </div>

    <div class="form-group"><b><?= $_->l('или') ?></b></div>

    <div class="form-group">

        <input class="form-control" readonly="readonly"
               value="0 1 * * * wget -q -O /dev/null <?php echo \System\Path::getURL('cron') ?>/<?= $config->uniq_key ?>">
    </div>

    <h3><?= $_->l('Персонализация') ?></h3>
    <div class="form-group">
        <label for="admin_template"><?=$_->l('Шаблон панели администратора')?></label>
        <select name="admin_template" class="form-control">
            <? foreach ($admin_templates as $admin_template) { ?>
               <option <?=$config->admin_template == $admin_template ? 'selected="selected"' : ''?>><?=$admin_template?></option>
            <? }?>
        </select>
    </div>
    <div class="form-group">
        <label for="front_template"><?=$_->l('Шаблон панели клиента')?></label>
        <select name="front_template" class="form-control">
            <? foreach ($front_templates as $front_template) { ?>
                <option <?=$config->front_template == $front_template ? 'selected="selected"' : ''?>><?=$front_template?></option>
            <? }?>
        </select>
    </div>
    <div class="form-group">
        <label for="email_template"><?= $_->l('Шаблон EMAIL уведомлений') ?></label>
        <select name="email_template" class="form-control">
            <? foreach ($email_templates as $email_template) { ?>
                <option <?= $config->email_template == $email_template ? 'selected="selected"' : '' ?>><?= $email_template ?></option>
            <? } ?>
        </select>
    </div>

    <h3><?=$_->l('Авторизация')?></h3>
    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enabled_sms_login == 1 ? 'checked="checked"' : '') ?>
                   name="enabled_sms_login" value="1"> <?=$_->l('Включить двухфакторную авторизация')?>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enable_social_auth == 1 ? 'checked="checked"' : '') ?>
                   name="enable_social_auth" value="1"> <?=$_->l('Включить возможность авторизации и регистрации через социальные сети')?>
            <a href="<?= $_->link('admin/settings/social-auth')?>"><?=$_->l('Настроить')?></a>
        </label>
    </div>
    <h3><?=$_->l('Регистрация')?></h3>

    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enabled_sms_confirm == 1 ? 'checked="checked"' : '') ?>
                   name="enabled_sms_confirm" value="1"> <?=$_->l('Требовать подтверждения номера телефона при регистрации')?>
        </label>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enabled_captcha == 1 ? 'checked="checked"' : '') ?>
                   name="enabled_captcha" value="1"> <?=$_->l('Использовать Captcha при регистрации')?> <a
                href="<?= $_->link('admin/settings/recaptcha') ?>"><?=$_->l('Настроить')?></a>
        </label>
    </div>

    <h3><?=$_->l('Настройки почты')?></h3>

    <div class="radio">
        <label>
            <input type="radio" name="email_method" id="email_method1"
                   value="mail" <?= $config->email_method == 'mail' || !$config->email_method ? 'checked="checked"' : '' ?>>
            <?=$_->l('Использовать php функцию mail()')?>
        </label>
    </div>

    <div class="for_mail">
        <div class="form-group">
            <label for="notification_email"><?= $_->l('Email-адрес отправителя уведомлений') ?></label>
            <input type="email" class="form-control" name="notification_email"
                   placeholder="Email-адрес отправителя уведомлений"
                   value="<?= $config->notification_email ?>">
        </div>
    </div>

    <div class="radio">
        <label>
            <input type="radio" name="email_method" id="email_method2" value="smtp" <?= $config->email_method == 'smtp' ? 'checked="checked"' : '' ?>>
            <?=$_->l('Использовать SMTP сервер')?>
        </label>
    </div>

    <div class="for_smtp">
        <div class="form-group">
            <label for="smtp_protocol"><?= $_->l('SMTP протокол') ?></label>
            <div class="radio">
                <label>
                    <input type="radio" name="smtp_protocol"
                           value="1" <?= $config->smtp_protocol == 1 ? 'checked="checked"' : '' ?>>
                    <?= $_->l('TLS') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="smtp_protocol"
                           value="0" <?= $config->smtp_protocol == 0 ? 'checked="checked"' : '' ?>>
                    <?= $_->l('SSL') ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="smtp_server"><?=$_->l('SMTP сервер')?></label>
            <input type="text" class="form-control" name="smtp_server" placeholder="<?=$_->l('SMTP сервер')?>"
                   value="<?= $config->smtp_server ?>">
        </div>
        <div class="form-group">
            <label for="smtp_port"><?=$_->l('SMTP порт')?></label>
            <input type="text" class="form-control" name="smtp_port" placeholder="<?=$_->l('SMTP порт')?>"
                   value="<?= $config->smtp_port ?>">
        </div>
        <div class="form-group">
            <label for="smtp_email"><?=$_->l('Email отправителя')?></label>
            <input type="text" class="form-control" name="smtp_email" placeholder="<?=$_->l('Email отправителя')?>"
                   value="<?= $config->smtp_email ?>">
        </div>
        <div class="form-group">
            <label for="smtp_username"><?=$_->l('Имя пользователя')?></label>
            <input type="text" class="form-control" name="smtp_username" placeholder="<?=$_->l('Имя пользователя')?>"
                   value="<?= $config->smtp_username ?>">
        </div>
        <div class="form-group">
            <label for="smtp_password"><?=$_->l('Пароль')?></label>
            <input type="password" class="form-control" name="smtp_password" placeholder="<?=$_->l('Пароль')?>"
                   value="<?= $config->smtp_password ?>">
        </div>
    </div>
    <a href="<?= $_->link('admin/settings/send-text-message') ?>"
       class="btn btn-default ajax-action"><span class="glyphicon glyphicon glyphicon-send"
                                                 aria-hidden="true"></span><?= $_->l('Отправить тестовое сообщение') ?>
    </a>
    <script>
        $(function () {

            if($('input[name=email_method]:checked').val() == 'smtp'){
                $('.for_smtp').show();
                $('.for_mail').hide();
            } else {
                $('.for_smtp').hide();
                $('.for_mail').show();
            }
        });
        $('input[name=email_method]').on('change', function () {
            if($(this).val() == 'smtp'){
                $('.for_smtp').show();
                $('.for_mail').hide();
            } else {
                $('.for_smtp').hide();
                $('.for_mail').show();
            }
        })
    </script>

    <h3><?=$_->l('Партнерская программа')?></h3>

    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->refprogram_enable == 1 ? 'checked="checked"' : '') ?>
                   name="refprogram_enable" value="1"> <?=$_->l('Включить партнерскую программу')?>
        </label>
    </div>

    <div class="form-group">
        <label for="refprogram_percent"><?=$_->l('Партнерский процент')?></label>
        <input type="number" class="form-control" name="refprogram_percent" placeholder="<?=$_->l('Партнерский процент')?>"
               value="<?= $config->refprogram_percent ?>">
    </div>

    <h3><?=$_->l('Компоненты')?></h3>

    <h4><?=$_->l('Хостинг')?></h4>
    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enable_component_hosting == 1 ? 'checked="checked"' : '') ?>
                   name="enable_component_hosting" value="1"> <?=$_->l('Включить возможность заказа хостинга')?>
        </label>
    </div>

    <script>
        $('input[name=enable_component_hosting]').on('change', function () {
            if($(this).prop('checked')) {
                $('.hosting_group').show();
            } else {
                $('.hosting_group').hide();
            }
        });
        $(function () {
            if($('input[name=enable_component_hosting]').prop('checked')) {
                $('.hosting_group').show();
            } else {
                $('.hosting_group').hide();
            }
        })
    </script>
    <div class="hosting_group">
        <div class="form-group">
            <label for="hosting_rules"><?= $_->l('Договор') ?></label>
            <select name="hosting_rules" class="form-control">
                <option value="0"><?= $_->l('Не использовать') ?></option>
                <? foreach ($pages as $page) {?>
                    <option <?=$config->hosting_rules == $page->id ? 'selected="selected"' : ''?> value="<?=$page->id?>"><?=$page->name?></option>
                <?}?>

            </select>
        </div>
    </div>


    <h4><?=$_->l('Домены')?></h4>
    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enable_component_domain == 1 ? 'checked="checked"' : '') ?>
                   name="enable_component_domain" value="1"> <?=$_->l('Включить возможность продажи доменов')?>
        </label>
    </div>

    <script>
        $('input[name=enable_component_domain]').on('change', function () {
            if ($(this).prop('checked')) {
                $('.domain_group').show();
            } else {
                $('.domain_group').hide();
            }
        });
        $(function () {
            if ($('input[name=enable_component_domain]').prop('checked')) {
                $('.domain_group').show();
            } else {
                $('.domain_group').hide();
            }
        })
    </script>
    <div class="domain_group">
        <div class="form-group">
            <label for="ns1"><?= $_->l('NS1:') ?></label>
            <input type="text" class="form-control" name="ns1" value="<?= $config->ns1 ?>"/>
        </div>
        <div class="form-group">
            <label for="ns2"><?= $_->l('NS2:') ?></label>
            <input type="text" class="form-control" name="ns2" value="<?= $config->ns2 ?>"/>
        </div>
        <div class="form-group">
            <label for="ns3"><?= $_->l('NS3:') ?></label>
            <input type="text" class="form-control" name="ns3" value="<?= $config->ns3 ?>"/>
        </div>
        <div class="form-group">
            <label for="ns4"><?= $_->l('NS4:') ?></label>
            <input type="text" class="form-control" name="ns4" value="<?= $config->ns4 ?>"/>
        </div>
    </div>


    <h4><?=$_->l('VPS')?></h4>
    <div class="checkbox">
        <label>
            <input type="checkbox" <?= ($config->enable_component_vps == 1 ? 'checked="checked"' : '') ?>
                   name="enable_component_vps" value="1"> <?=$_->l('Включить возможность заказа VPS')?>
        </label>
    </div>

    

    <button class="btn btn-success" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?></button>
</form>