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

    .settings_table input[type="submit"], button.submit {
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
</style>
<?= $_->JS('validator.js') ?>

<script>
    $(function () {
        $('.form').validate({messages: validate_messages});
    })
</script>

<ul class="nav nav-tabs nav-justified">
    <li role="presentation" <?= ($page == 'main' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting') ?>"><?= $_->l('Основные') ?></a>
    </li>
    <li role="presentation" <?= ($page == 'safety' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting/safety') ?>"><?= $_->l('Безопасность') ?></a></li>
    <li role="presentation" <?= ($page == 'notifications' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting/notifications') ?>"><?= $_->l('Уведомления') ?></a></li>
    <li role="presentation" <?= ($page == 'domain-owners' ? 'class="active"' : '') ?>><a
            href="<?= $_->link('setting/domain-owners') ?>"><?= $_->l('Владельцы доменов') ?></a></li>
</ul>

<?=$content?>