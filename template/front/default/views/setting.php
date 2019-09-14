<style>
    .nav-tabs.nav-justified {
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .nav-tabs.nav-justified > li {
        width: auto;
        display: inline-block;
        vertical-align: bottom;
        margin-bottom: 0;
        padding: 0;
    }

    .nav-tabs.nav-justified > li.active a,
    .nav-tabs.nav-justified > li > a:hover {
        background-color: #333;
        color: rgb(255, 255, 255);
        border-bottom-color: #333 !important;
    }

    .nav-tabs.nav-justified > li > a {
        background-color: #ddd;
        padding: 10px 25px;
        color: #333;
        text-transform: uppercase;
        transition: color 0.3s, background-color 0.3s, border-bottom-color 0.3s;
    }

    .settings_table {
        width: 100%;
        border-collapse: collapse;
    }

    .settings_table td {
        padding: 9px;
    }

    .settings_table tr {
        border: 1px solid #717171;
    }

    .settings_table tr td:first-child {
        padding-left: 15px;
        width: 40%;
    }

    .settings_table tr:nth-child(odd) {
        background-color: #ddd;
    }

    .settings_table tr:nth-child(even) {
        background-color: #fff;
    }

    .styled_td {
        background-color: #333;
        text-transform: uppercase;
        color: rgb(255, 255, 255);
    }

    .settings_table input, button.submit {
        background-color: #fff;
        padding: 0 8px;
        color: #000;
        border: 1px solid #7C7C7C;
        margin-right: 5px;
        height: 30px;
        box-shadow: inset 1px 1px 2px rgba(58, 58, 58, 0.55);
    }

    .settings_table input[type="checkbox"],
    .settings_table input[type="radio"] {
        border: none;
        box-shadow: none;
        height: auto;
        margin-top: 0;
        vertical-align: middle;
    }

    .settings_table input[type="submit"], button.submit, button.button {
        background-color: #333;
        color: #fff;
        box-shadow: none;
        text-transform: uppercase;
        padding: 0 15px;
        font-weight: 700;
        transition: color 0.3s, background-color 0.3s, box-shadow 0.3s;
    }

    .settings_table input[type="submit"]:hover, button.submit:hover {
        background-color: #fff;
        color: #333;
        box-shadow: 3px 3px 3px #000;
    }
    .network-auth-item {
        font-size: 12px;
        margin: 5px 5px 0 0;
        display: inline-block;
        padding: 5px 7px;
    }
    a.network-auth-item-close {
        font-size: 16px;
        margin-left: 4px;
        vertical-align: middle;
    }
    .network-auth-item-close:hover {
        text-decoration: none;
    }
</style>
<?= $_->JS('validator.js') ?>


<?$_->js('select2/select2.min.js');?>
<?$_->css('select2/select2.min.css');?>


<script>

</script>

<ul class="nav nav-tabs nav-justified">
    <li role="presentation" <?= ($page == 'main' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting') ?>"><?= $_->l('Основные') ?></a>
    </li>
    <li role="presentation" <?= ($page == 'safety' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting/safety') ?>"><?= $_->l('Безопасность') ?></a></li>
    <li role="presentation" <?= ($page == 'notifications' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting/notifications') ?>"><?= $_->l('Уведомления') ?></a></li>
    <?if($config->enable_component_domain){?>
        <li role="presentation" <?= ($page == 'domain-owners' ? 'class="active"' : '') ?>><a
                href="<?= $_->link('setting/domain-owners') ?>"><?= $_->l('Владельцы доменов') ?></a></li>
    <?}?>
</ul>
<?
if (isset($messages)) {
    foreach ($messages as $message) {
        ?>
        <? if ($message == 'ok') { ?>

            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <span class="glyphicon glyphicon-floppy-saved"></span> <?= $_->l('Изменения были успешно сохранены.') ?>
            </div>
        <? } ?>
    <? }
} ?>
<? if ($page == 'safety') { ?>
    <form action="" method="post" class="form">
        <table class="settings_table">
            <tbody>

            <tr>
                <td colspan="2" class="styled_td"><b><?= $_->l('Сменить пароль') ?></b>
                </td>
            </tr>
            <tr>
                <td width="50%"><?= $_->l('Старый пароль:') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="password" class="form-control" style=" max-width: 400px; " name="old_password"
                               value=""
                               data-validate="required|ajax"
                               data-validate-message-fail-ajax="<?= $_->l('Пароль введен неверно!') ?>">
                    </div>

                </td>
            </tr>
            <tr>
                <td width="50%"><?=$_->l('Новый пароль:')?></td>
                <td>
                    <div class="validate-block">
                        <input type="password" class="form-control" style=" max-width: 400px; " name="new_password"
                               value="" data-validate="pass">
                    </div>
                </td>
            </tr>
            <tr>
                <td width="50%"><?= $_->l('Повторите новый пароль:') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="password" class="form-control" style=" max-width: 400px; " name="new2_password"
                               value="" data-validate="required|pass2"
                               data-validate-field-pass="input[name=new_password]">
                    </div>
                </td>
            </tr>


            <tr>
                <td colspan="2" style="text-align:center"><input type="submit" name="save"
                                                                 value="<?= $_->l('Изменить данные') ?>"></td>
            </tr>
            </tbody>
        </table>
    </form>

    <form action="" method="post">
        <input type="hidden" name="remove_sessions" value="1">
        <table class="settings_table">
            <tbody>
            <tr>
                <td colspan="3" class="styled_td"><b><?= $_->l('Список сессий') ?></b>
                </td>
            </tr>
            <tr>
                <td class=""><b><?= $_->l('ОС') ?></b></td>
                <td class=""><b><?= $_->l('Браузер') ?></b></td>
                <td class=""><b><?= $_->l('Дата') ?></b></td>
            </tr>
            <? foreach ($sessions as $session) { ?>
                <tr>
                    <td><?= $session->os ?> (<?= $session->ip ?>)</td>
                    <td><?= $session->browser ?> </td>
                    <td><?= $session->date ?></td>
                </tr>
            <? } ?>

            <tr>
                <td colspan="3" style="text-align:center"><input type="submit" name="save"
                                                                 value="<?= $_->l('Завершить все сессии (кроме активной)') ?>">
                </td>
            </tr>



            </tbody>
        </table>
    </form>
<? }
elseif ($page == 'notifications') { ?>
    <?= $_->JS('bootstrap-switch.min.js') ?>
    <?= $_->CSS('bootstrap-switch.css') ?>
    <script>
        $(function () {
            $('input.checkbox').bootstrapSwitch({size: 'mini'});

        })
    </script>
    <form action="" method="post" class="form">
        <table class="settings_table">
            <tbody>
            <tr>
                <td class="styled_td"><b><?= $_->l('Уведомления') ?></b></td>
                <td class="styled_td text-center"><b><?= $_->l('Email') ?></b></td>
                <td class="styled_td text-center"><b><?= $_->l('SMS') ?></b></td>
            </tr>
            <tr>
                <td><?= $_->l('Уведомлять о выставлении нового счета:') ?></td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['new_bill']) ? 'checked="checked"' : '') ?>
                                name="notifications[new_bill]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
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
                            <input <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['end_hosting_order']) ? 'checked="checked"' : '') ?>
                                name="notifications[end_hosting_order]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
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
                            <input <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['suspend_hosting_order']) ? 'checked="checked"' : '') ?>
                                name="notifications[suspend_hosting_order]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
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
                            <input <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['unsuspend_hosting_order']) ? 'checked="checked"' : '') ?>
                                name="notifications[unsuspend_hosting_order]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
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
                            <input  <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['new_ticket']) ? 'checked="checked"' : '') ?>
                                name="notifications[new_ticket]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
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
                            <input  <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['ticket_answer']) ? 'checked="checked"' : '') ?>
                                name="notifications[ticket_answer]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['sms_ticket_answer']) ? 'checked="checked"' : '') ?>
                                name="notifications[sms_ticket_answer]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?= $_->l('Уведомлять об добавлении и изменении информации о заказе:') ?></td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_email_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['info_service_order']) ? 'checked="checked"' : '') ?>
                                name="notifications[info_service_order]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <label>
                            <input  <?=($config->enable_client_sms_notification_control ? '' :'disabled="disabled"')?>
                                type="checkbox" <?= (isset($notifications['sms_info_service_order']) ? 'checked="checked"' : '') ?>
                                name="notifications[sms_info_service_order]" value="1" class="checkbox">
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:center">
                    <button type="submit" class="submit" name="save"><span
                            class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

    </form>

<? }
elseif ($page == 'main') { ?>
    <form action="" method="post" class="form" accept-charset="UTF-8" enctype="multipart/form-data">
        <table class="settings_table">
            <tbody>
            <tr>
                <td colspan="2" class="styled_td"><b><?= $_->l('Персональные данные') ?></b></td>
            </tr>
            <tr>
                <td><?= $_->l('Имя:') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="name" value="<?= $client->name ?>" class="form-control"
                               data-validate="fio">
                    </div>
                </td>
            </tr>
            <tr>
                <td><?= $_->l('Номер телефона:') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="phone" value="<?= $client->phone ?>" class="form-control"
                               data-validate="phone">
                    </div>
                </td>
            </tr>
            <tr>
                <td><?= $_->l('Email:') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="email" value="<?= $client->email ?>" class="form-control"
                               data-validate="email">
                    </div>
                </td>
            </tr>
            <?if($config->enable_lang_switcher_for_client){?>

                <tr>
                    <td><?= $_->l('Язык:') ?></td>
                    <td>
                        <div class="validate-block">
                            <select name="default_lang" class="form-control">
                                <?foreach($languages as $lang){?>
                                    <option <?=($client->default_lang == $lang->id? 'selected="selected"' : '')?> data-ico="<?=$_->link('storage/i18n/flags/'.$lang->iso_code.'.png')?>" value="<?=$lang->id?>"><?=$lang->name?></option>
                                <?}?>
                            </select>



                        </div>
                        <?=$_->l('Этот язык будет использоватся по умолчанию для уведомлений.')?>
                    </td>
                </tr>
                <?if($config->enable_social_auth){?>
                    <tr>
                        <td><?=$_->l('Синхронизация аккаунтов')?>:</td>
                        <td>
                            <div>
                                <? $networks_providers = implode(',', array_slice($socialAuthInfo->networks, 0, 4));?>
                                <? $networks_hidden = implode(',', array_slice($socialAuthInfo->networks, 4));?>
                                <script src="//ulogin.ru/js/ulogin.js"></script>
                                <div id="uLogin" data-ulogin="display=panel;theme=classic;fields=first_name,last_name;lang=<?=$lang->iso_code?>;providers=<?=$networks_providers?>;hidden=<?=$networks_hidden?>;redirect_uri=<?=$_->link('/social/auth')?>;mobilebuttons=0;"></div>
                            </div>
                            <div>
                                <?foreach ($social_accounts as $clientSocialAccount){?>
                                    <div class="label label-info network-auth-item">
                                        <img src="" alt="" class="network-auth-item-icon">
                                        <span class="network-auth-item-name"><?=$clientSocialAccount->network?></span>
                                        <a href="<?=$_->link('setting/remove-social-account?id=' . $clientSocialAccount->id)?>" class="network-auth-item-close">×</a>
                                    </div>
                                <?}?>
                            </div>
                        </td>
                    </tr>
                <?}?>
            <?} ?>



            <tr>
                <td><?= $_->l('Тип:') ?></td>
                <td>
                    <div class="validate-block">

                        <select name="type" class="form-control">
                            <option value="0" <?=($client->type == 0 ? 'selected="selected"' : '')?> ><?=$_->l('Частное лицо')?></option>
                            <option value="1" <?=($client->type == 1 ? 'selected="selected"' : '')?>><?=$_->l('Юридическое лицо')?></option>
                        </select>
                    </div>
                </td>
            </tr>



            <tr class="form-organization">
                <td><?= $_->l('Страна:') ?></td>
                <td>
                    <div class="validate-block">

                        <?$countryList = array(
                            "AF" => "Afghanistan",
                            "AL" => "Albania",
                            "DZ" => "Algeria",
                            "AS" => "American Samoa",
                            "AD" => "Andorra",
                            "AO" => "Angola",
                            "AI" => "Anguilla",
                            "AQ" => "Antarctica",
                            "AG" => "Antigua and Barbuda",
                            "AR" => "Argentina",
                            "AM" => "Armenia",
                            "AW" => "Aruba",
                            "AU" => "Australia",
                            "AT" => "Austria",
                            "AZ" => "Azerbaijan",
                            "BS" => "Bahamas",
                            "BH" => "Bahrain",
                            "BD" => "Bangladesh",
                            "BB" => "Barbados",
                            "BY" => "Belarus",
                            "BE" => "Belgium",
                            "BZ" => "Belize",
                            "BJ" => "Benin",
                            "BM" => "Bermuda",
                            "BT" => "Bhutan",
                            "BO" => "Bolivia",
                            "BA" => "Bosnia and Herzegovina",
                            "BW" => "Botswana",
                            "BV" => "Bouvet Island",
                            "BR" => "Brazil",
                            "BQ" => "British Antarctic Territory",
                            "IO" => "British Indian Ocean Territory",
                            "VG" => "British Virgin Islands",
                            "BN" => "Brunei",
                            "BG" => "Bulgaria",
                            "BF" => "Burkina Faso",
                            "BI" => "Burundi",
                            "KH" => "Cambodia",
                            "CM" => "Cameroon",
                            "CA" => "Canada",
                            "CT" => "Canton and Enderbury Islands",
                            "CV" => "Cape Verde",
                            "KY" => "Cayman Islands",
                            "CF" => "Central African Republic",
                            "TD" => "Chad",
                            "CL" => "Chile",
                            "CN" => "China",
                            "CX" => "Christmas Island",
                            "CC" => "Cocos [Keeling] Islands",
                            "CO" => "Colombia",
                            "KM" => "Comoros",
                            "CG" => "Congo - Brazzaville",
                            "CD" => "Congo - Kinshasa",
                            "CK" => "Cook Islands",
                            "CR" => "Costa Rica",
                            "HR" => "Croatia",
                            "CU" => "Cuba",
                            "CY" => "Cyprus",
                            "CZ" => "Czech Republic",
                            "CI" => "Côte d’Ivoire",
                            "DK" => "Denmark",
                            "DJ" => "Djibouti",
                            "DM" => "Dominica",
                            "DO" => "Dominican Republic",
                            "NQ" => "Dronning Maud Land",
                            "DD" => "East Germany",
                            "EC" => "Ecuador",
                            "EG" => "Egypt",
                            "SV" => "El Salvador",
                            "GQ" => "Equatorial Guinea",
                            "ER" => "Eritrea",
                            "EE" => "Estonia",
                            "ET" => "Ethiopia",
                            "FK" => "Falkland Islands",
                            "FO" => "Faroe Islands",
                            "FJ" => "Fiji",
                            "FI" => "Finland",
                            "FR" => "France",
                            "GF" => "French Guiana",
                            "PF" => "French Polynesia",
                            "TF" => "French Southern Territories",
                            "FQ" => "French Southern and Antarctic Territories",
                            "GA" => "Gabon",
                            "GM" => "Gambia",
                            "GE" => "Georgia",
                            "DE" => "Germany",
                            "GH" => "Ghana",
                            "GI" => "Gibraltar",
                            "GR" => "Greece",
                            "GL" => "Greenland",
                            "GD" => "Grenada",
                            "GP" => "Guadeloupe",
                            "GU" => "Guam",
                            "GT" => "Guatemala",
                            "GG" => "Guernsey",
                            "GN" => "Guinea",
                            "GW" => "Guinea-Bissau",
                            "GY" => "Guyana",
                            "HT" => "Haiti",
                            "HM" => "Heard Island and McDonald Islands",
                            "HN" => "Honduras",
                            "HK" => "Hong Kong SAR China",
                            "HU" => "Hungary",
                            "IS" => "Iceland",
                            "IN" => "India",
                            "ID" => "Indonesia",
                            "IR" => "Iran",
                            "IQ" => "Iraq",
                            "IE" => "Ireland",
                            "IM" => "Isle of Man",
                            "IL" => "Israel",
                            "IT" => "Italy",
                            "JM" => "Jamaica",
                            "JP" => "Japan",
                            "JE" => "Jersey",
                            "JT" => "Johnston Island",
                            "JO" => "Jordan",
                            "KZ" => "Kazakhstan",
                            "KE" => "Kenya",
                            "KI" => "Kiribati",
                            "KW" => "Kuwait",
                            "KG" => "Kyrgyzstan",
                            "LA" => "Laos",
                            "LV" => "Latvia",
                            "LB" => "Lebanon",
                            "LS" => "Lesotho",
                            "LR" => "Liberia",
                            "LY" => "Libya",
                            "LI" => "Liechtenstein",
                            "LT" => "Lithuania",
                            "LU" => "Luxembourg",
                            "MO" => "Macau SAR China",
                            "MK" => "Macedonia",
                            "MG" => "Madagascar",
                            "MW" => "Malawi",
                            "MY" => "Malaysia",
                            "MV" => "Maldives",
                            "ML" => "Mali",
                            "MT" => "Malta",
                            "MH" => "Marshall Islands",
                            "MQ" => "Martinique",
                            "MR" => "Mauritania",
                            "MU" => "Mauritius",
                            "YT" => "Mayotte",
                            "FX" => "Metropolitan France",
                            "MX" => "Mexico",
                            "FM" => "Micronesia",
                            "MI" => "Midway Islands",
                            "MD" => "Moldova",
                            "MC" => "Monaco",
                            "MN" => "Mongolia",
                            "ME" => "Montenegro",
                            "MS" => "Montserrat",
                            "MA" => "Morocco",
                            "MZ" => "Mozambique",
                            "MM" => "Myanmar [Burma]",
                            "NA" => "Namibia",
                            "NR" => "Nauru",
                            "NP" => "Nepal",
                            "NL" => "Netherlands",
                            "AN" => "Netherlands Antilles",
                            "NT" => "Neutral Zone",
                            "NC" => "New Caledonia",
                            "NZ" => "New Zealand",
                            "NI" => "Nicaragua",
                            "NE" => "Niger",
                            "NG" => "Nigeria",
                            "NU" => "Niue",
                            "NF" => "Norfolk Island",
                            "KP" => "North Korea",
                            "VD" => "North Vietnam",
                            "MP" => "Northern Mariana Islands",
                            "NO" => "Norway",
                            "OM" => "Oman",
                            "PC" => "Pacific Islands Trust Territory",
                            "PK" => "Pakistan",
                            "PW" => "Palau",
                            "PS" => "Palestinian Territories",
                            "PA" => "Panama",
                            "PZ" => "Panama Canal Zone",
                            "PG" => "Papua New Guinea",
                            "PY" => "Paraguay",
                            "YD" => "People's Democratic Republic of Yemen",
                            "PE" => "Peru",
                            "PH" => "Philippines",
                            "PN" => "Pitcairn Islands",
                            "PL" => "Poland",
                            "PT" => "Portugal",
                            "PR" => "Puerto Rico",
                            "QA" => "Qatar",
                            "RO" => "Romania",
                            "RU" => "Russia",
                            "RW" => "Rwanda",
                            "RE" => "Réunion",
                            "BL" => "Saint Barthélemy",
                            "SH" => "Saint Helena",
                            "KN" => "Saint Kitts and Nevis",
                            "LC" => "Saint Lucia",
                            "MF" => "Saint Martin",
                            "PM" => "Saint Pierre and Miquelon",
                            "VC" => "Saint Vincent and the Grenadines",
                            "WS" => "Samoa",
                            "SM" => "San Marino",
                            "SA" => "Saudi Arabia",
                            "SN" => "Senegal",
                            "RS" => "Serbia",
                            "CS" => "Serbia and Montenegro",
                            "SC" => "Seychelles",
                            "SL" => "Sierra Leone",
                            "SG" => "Singapore",
                            "SK" => "Slovakia",
                            "SI" => "Slovenia",
                            "SB" => "Solomon Islands",
                            "SO" => "Somalia",
                            "ZA" => "South Africa",
                            "GS" => "South Georgia and the South Sandwich Islands",
                            "KR" => "South Korea",
                            "ES" => "Spain",
                            "LK" => "Sri Lanka",
                            "SD" => "Sudan",
                            "SR" => "Suriname",
                            "SJ" => "Svalbard and Jan Mayen",
                            "SZ" => "Swaziland",
                            "SE" => "Sweden",
                            "CH" => "Switzerland",
                            "SY" => "Syria",
                            "ST" => "São Tomé and Príncipe",
                            "TW" => "Taiwan",
                            "TJ" => "Tajikistan",
                            "TZ" => "Tanzania",
                            "TH" => "Thailand",
                            "TL" => "Timor-Leste",
                            "TG" => "Togo",
                            "TK" => "Tokelau",
                            "TO" => "Tonga",
                            "TT" => "Trinidad and Tobago",
                            "TN" => "Tunisia",
                            "TR" => "Turkey",
                            "TM" => "Turkmenistan",
                            "TC" => "Turks and Caicos Islands",
                            "TV" => "Tuvalu",
                            "UM" => "U.S. Minor Outlying Islands",
                            "PU" => "U.S. Miscellaneous Pacific Islands",
                            "VI" => "U.S. Virgin Islands",
                            "UG" => "Uganda",
                            "UA" => "Ukraine",
                            "SU" => "Union of Soviet Socialist Republics",
                            "AE" => "United Arab Emirates",
                            "GB" => "United Kingdom",
                            "US" => "United States",
                            "ZZ" => "Unknown or Invalid Region",
                            "UY" => "Uruguay",
                            "UZ" => "Uzbekistan",
                            "VU" => "Vanuatu",
                            "VA" => "Vatican City",
                            "VE" => "Venezuela",
                            "VN" => "Vietnam",
                            "WK" => "Wake Island",
                            "WF" => "Wallis and Futuna",
                            "EH" => "Western Sahara",
                            "YE" => "Yemen",
                            "ZM" => "Zambia",
                            "ZW" => "Zimbabwe",
                            "AX" => "Åland Islands",
                        );?>

                        <select class="form-control" name="country" data-validate="required">
                            <option value=""> --- </option>
                            <?foreach($countryList as $code=>$name){?>
                                <option <?=($client->country == $code ? 'selected="selected"' : '')?> data-ico="<?=(file_exists($_->path('img/flags/'.strtolower($code).'.png')) ? $_->path('img/flags/'.strtolower($code).'.png') : $_->path('img/flags/ua.png'))?>" value="<?=$code?>" <?=(1 == $code ? 'selected="selected"' : '')?>><?=$name?></option>
                            <?}?>
                        </select>


                    </div>
                </td>
            </tr>

            <tr class="form-organization">
                <td><?=$_->l('Название организации')?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_name" value="<?= $client->organization_name ?>" class="form-control"
                               data-validate="required">
                    </div>
                </td>
            </tr>

            <tr class="form-organization">
                <td><?=$_->l('Юридический адрес организации')?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_address" value="<?= $client->organization_address ?>" class="form-control"
                               data-validate="required">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input <?= !$client->organization_located_address ? 'checked="checked"' : ''?> name="use_organization_location_address" type="checkbox"> <?=$_->l('Фактический адрес совпадает с юридическим')?>
                        </label>
                    </div>
                </td>
            </tr>

            <tr class="form-organization form-organization_location_address" >
                <td><?=$_->l('Фактический адрес организации')?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_located_address" value="<?= $client->organization_located_address ?>" class="form-control"
                               data-validate="required">
                    </div>
                </td>
            </tr>

            <tr class="form-organization">
                <td><?= $_->l('Руководитель организации') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_chief"
                               value="<?= $client->organization_chief ? $client->organization_chief : $client->name ?>"
                               class="form-control"
                               data-validate="required">
                    </div>
                </td>
            </tr>

            <tr class="form-organization form-hidden form-ua">
                <td><?=$_->l('ЕГРПОУ')?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_number" value="<?= $client->organization_number ?>" class="form-control"
                               data-validate="required">
                    </div>
                </td>
            </tr>

            <tr class="form-organization">
                <td><?= $_->l('ИНН') ?></td>
                <td>
                    <div class="validate-block">
                        <input type="text" name="organization_ipn" value="<?= $client->organization_ipn ?>" class="form-control"
                               data-validate="required">
                    </div>
                </td>
            </tr>


            <? $i = 1;
            foreach ($docs as $doc) { ?>
                <td><?= $_->l('Документ') . ' ' . $i++ ?></td>
                <td>
                    <div class="validate-block">
                        <a target="_blank"
                           href="<?= $_->link('/storage/docs/' . $client->id . '/' . $doc) ?>"><?= $doc ?></a>

                        <a class="close" href="<?= $_->link('setting/docs/remove?doc=' . $doc) ?>"><span
                                aria-hidden="true">×</span></a>

                    </div>
                </td>
                </tr>
            <? } ?>
            <tr>
                <td colspan="2" style="text-align:center">
                    <button class="btn btn-info btn-xs add-doc">
                        <span class="glyphicon glyphicon-plus"></span>
                        <?= $_->l('Добавить документ') ?></button>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:center"><input type="submit" name="save"
                                                                 value="<?= $_->l('Изменить данные') ?>"></td>
            </tr>


            </tbody>
        </table>
        <script>
            var i = <?=$i?>;
            $('.add-doc').on('click', function (e) {
                e.preventDefault();
                $(this).parents('tr').before('<tr >' +
                    '<td><?=$_->l('Документ ')?>' + i + '</td>' +
                    '<td>' +
                    '<div class="validate-block">' +
                    '<input type="file" name="docs[]" class="form-control">' +
                    '</div>' +
                    '</td>' +
                    '</tr>'
                );
                i++;
                return false;
            });
        </script>
        <?if($_->rGet('error_code') == 1){?>
            <script>
                createNoty('<?=$_->l('Ошибка привязки аккаунта')?>', 'danger');
            </script>
        <?}?>
    </form>
    <script>
        function formatState (state) {
            if (!state.id) { return state.text; }
            console.log(state);
            var $state = $(
                '<span><img src="'+$(state.element).data('ico')+'" width="20px" class="img-flag" /> ' + state.text + '</span>'
            );
            return $state;
        }
        $("select[name=default_lang]").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
    </script>

    <script>
        function formatCountry (state) {
            if (!state.id) { return state.text; }
            console.log(state);
            var $state = $(
                '<span> ' + state.text + '</span>'
            );
            return $state;
        }
    </script>

    <script>
        $(function () {

            $('select[name=type]').on('change', function () {

                if($(this).val() == 1){
                    $('.form-organization').show();
                    $('input[name=use_organization_location_address]').trigger('change');
                    $('.form-hidden').hide();
                    $('.form-person').hide();
                } else {
                    $('.form-organization').hide();
                    $('.form-person').show();
                }
                $('.form').validate({messages: validate_messages});

            }).trigger('change');

            $('input[name=use_organization_location_address]').on('change', function(){
                if(!$(this).is(':checked')){
                    $('.form-organization_location_address').show();
                }    else{
                    $('.form-organization_location_address').hide();

                    $('input[name=organization_located_address]').val('');
                }
            }).trigger('change');

            $("select[name=country]").select2({
                width: '100%',
                templateResult: formatCountry,
                templateSelection: formatCountry
            }).on('change', function(){
                $('.form-hidden').hide();
                var form = '.form-'+($(this).val().toLowerCase());
                var form_hidden = '.form-hidden-' + ($(this).val().toLowerCase());
                $(form).show();
                $(form_hidden).hide();
                $('.form').validate({messages: validate_messages});

            }).trigger('change');

        })
    </script>

<? } else if ($page == 'domain-owners') { ?>

<? } ?>
