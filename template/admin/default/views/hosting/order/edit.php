<?= $_->JS('validator.js') ?>

<script>
    $(function () {
        $('form').validate({messages: validate_messages});

    })
</script>

<? if (!empty($errors)) { ?>

    <?
    foreach ($errors as $error) {
        ?>
        <? if ($error == 'no_connection') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Нет соединения с сервером.')?></div>
        <? } else if ($error == 'system_error') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Системная ошибка!')?></div>
        <? } else if ($error == 'user_isset') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Пользователь с таким логином существует')?></div>
        <? } else if ($error == 'field_user_no_valid') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Пользователь не выбран')?></div>
        <? } else if ($error == 'field_server_no_valid') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Сервер не выбран')?></div>
        <? } else if ($error == 'field_plan_no_valid') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Тарифный план не выбран')?></div>
        <? } else if ($error == 'pass_not_valid') { ?>
            <div class="alert alert-danger" role="alert"> <?=$_->l('Пароль слишком легкий! Придумайте другой!')?></div>
        <? } ?>
    <? } ?>


<? } ?>
<form method="post">
    <div class="form-group" disabled="disabled">
        <label for="login"><?=$_->l('Логин')?></label>

        <input type="text" class="form-control" data-validate="username" data-validate-def="<?= $order->login ?>"
               name="login" <?= ($order->id ? 'disabled' : '') ?>
               value="<?= $order->login ?>">
    </div>
    <? if (!$order->id) { ?>
        <div class="form-group">
            <label for="pass"><?=$_->l('Пароль')?></label>
            <input type="password" class="form-control" data-validate="pass"
                   name="pass"
                   value="">
        </div>
    <? } ?>
    <div class="form-group">
        <label for="exampleInputPassword1"><?=$_->l('Тариф')?></label>
        <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right"
              title="<?=$_->l('При изменении тарифа счет выставлен не будет, а изменении будет произведено сразу после сохранения.')?>"></span>

        <select name="plan_id" class="form-control" data-validate="required">
            <option value=""> ---</option>
            <? foreach ($plans as $plan) { ?>
                <option
                    value="<?= $plan->id ?>" <?= (($order->plan_id == $plan->id) || (\System\Tools::rPOST('plan_id') == $plan->id) ? 'selected' : '') ?> ><?= $plan->name ?></option>
            <? } ?>
        </select>
    </div>

    <? if (!$order->id) { ?>
        <div class="form-group">
            <label for="server_id"><?=$_->l('Сервер')?></label>
            <select name="server_id" data-validate="required" class="form-control">
                <option value=""> ---</option>

                <? foreach ($servers as $server) { ?>
                    <option value="<?= $server->id ?>" data-panel="<?=$server->panel?>"> <?= $server->name ?> </option>
                <? } ?>
            </select>
        </div>
        <script>
            $(function () {
                // console.log($('select[name=server] option:first-child'));
                useDomain($('select[name=server_id] option:first-child'));
            });


            $('select[name=server_id]').on('change', function () {
                useDomain($(this).find('option:selected'));
            });

            function useDomain(element){
                var panel = (element).data('panel');

                if (panel == <?=\model\HostingServer::PANEL_CPANEl;?> ||
                    panel == <?=\model\HostingServer::PANEL_PLESK;?> ||
                    panel == <?=\model\HostingServer::PANEL_DIRECTADMIN;?> ||
                    panel == <?=\model\HostingServer::PANEL_ISP;?>
                ){
                    $('input[name=domain]').parents('div.form-group').show();
                } else {
                    $('input[name=domain]').parents('div.form-group').hide();
                }
                $('form').validate(validate_messages);
            }
        </script>
        <script>
            function getServers() {
                var id_plan = ($('select[name=plan_id]').val());
                if (id_plan) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {type: 'get_servers', plan_id: id_plan, ajax: 1},
                        success: function (data) {
                            $('select[name=server_id]').removeAttr('disabled');
                            $('select[name=server_id]').html('<option value=""> --- </option>');
                            for (var i = 0; i < data.length; i++) {
                                $('select[name=server_id]').append($('<option ' + ((data[i].id == '<?=\System\Tools::rPOST('server_id')?>') ? 'selected="selected"' : '') + ' value="' + data[i].id + '" data-panel="' + data[i].panel + '">' + data[i].name + '</option>'));
                            }
                            $('select[name=server_id]').trigger('change');

                        }
                    })
                } else {
                    $('select[name=server_id]').attr('disabled', 'disabled');
                    $('select[name=server_id]').html('<option value=""> <?=$_->l('Для выбора сервера, выберите тарифный план.')?> </option>');
                }
            }


            getServers();
            $('select[name=plan_id]').on('change', function () {
                getServers();
            })

        </script>
        <div class="form-group">
            <label><?= $_->l('Домен') ?></label>
            <input type="text" name="domain" data-validate="domain" class="form-control" value="<?= $_->p('domain') ?>">
        </div>

    <? } else { ?>
        <div class="form-group">
            <label for="exampleInputPassword1"><?=$_->l('Сервер')?></label>
            <input type="hidden" name="server_id" value="<?= $server->id ?>">
            <select name="server_id" class="form-control" data-validate="required" disabled="disabled">
                <option selected="selected" value="<?= $server->id ?>"><?= $server->name ?></option>
            </select>
        </div>

    <? } ?>
    <?= $_->js('select2/select2.min.js') ?>
    <?= $_->js('select2/i18n/ru.js') ?>
    <?= $_->css('select2/select2.min.css') ?>
    <div class="form-group">
        <label for="exampleInputEmail1"><?=$_->l('Клиент')?></label>
        <select name="user_id" data-validate="required" class="form-control">
            <option value=""> --- </option>
            <? foreach ($clients as $client) { ?>
                <option
                    value="<?= $client->id ?>" <?= (($order->client_id == $client->id) || \System\Tools::rPOST('user_id') == $client->id ? 'selected' : '') ?> ><?= $client->name ?></option>
            <? } ?>
        </select>
        <script type="text/javascript">
            function formatRepo(repo) {
                if (repo.loading) return '<?=$_->l('Загрузка...')?>';

                var markup = '<div class="clearfix">' +
                    '<div class="col-sm-10">' +
                    '<div class="clearfix">' +
                    '<div class="col-sm-2">' + repo.username + '</div>' +
                    '<div class="col-sm-4">' + repo.name + '</div>' +
                    '<div class="col-sm-3"><i class="fa fa-code-fork"></i> ' + repo.email + '</div>' +
                    '<div class="col-sm-2"><i class="fa fa-star"></i> ' + repo.phone +
                    '</div>' +
                    '</div>';

                markup += '</div></div>';
                return markup;
            }

            function formatRepoSelection(repo) {
                console.log(repo);
                return repo.text;
            }
            $("select[name=user_id]").select2({
                ajax: {
                    method: 'POST',
                    dataType: 'json',
                    delay: 250,

                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            action: 'getClients',
                            ajax: 1
                        };
                    },
                    processResults: function (data, page) {
                        // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                language: "ru",
                placeholder: '<?=$_->l('Введите логин, ФИО, телефон или Email')?>',
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 3,
                templateResult: formatRepo, // omitted for brevity, see the source of this page
                templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
            });
        </script>
    </div>
    <? if (!$order->id) { ?>
    <div class="checkbox">
        <label>
            <input name="import_flag" value="1" type="checkbox" > <?=$_->l('Пользователь уже существует в панели управления сервером')?>
        </label>
    </div>
    <div class="add_data" style="display: none;">
        <div class="form-group">
            <label for="paid_to"><?=$_->l('Аккаунт оплачен до')?></label>
            <input type="text" class="form-control" name="paid_to" id="paid_to">

        </div>
    </div>
        <script>
            $('input[name=import_flag]').on('change', function(){
                if(!$('.add_data').is(":visible")){
                    $('input[name=paid_to]').attr('data-validate', 'date');
                    $('input[name=paid_to]').data('validate', 'date');
                    $('.add_data').show();
                } else{
                    $('input[name=paid_to]').removeAttr('data-validate');
                    $('input[name=paid_to]').removeData('validate');
                    $('.add_data').hide();
                }
                //alert($('input[name=date_end]').data('validate'));
               // $('form').validate_destroy();
                $('form').validate({messages: validate_messages});
            })
        </script>
<?} else {?>
        <div class="add_data">
            <div class="form-group">
                <label for="paid_to"><?=$_->l('Аккаунт оплачен до')?></label>
                <input type="text" class="form-control"   data-inputmask="'mask': 'd-m-y'" name="paid_to" id="paid_to" value="<?=date('d-m-Y', strtotime($order->paid_to))?>">

            </div>
        </div>
    <?}?>
    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?>
    </button>
</form>