

<div class="ajax-block">
    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('form').validate({messages: validate_messages});
        });
    </script>

<style type="text/css">
    legend {
        font-size: 32px;
        text-shadow: 0 1px 0 #fff, 1px 2px 2px #333;
    }
    label {
        margin-bottom: 5px !important;
    }
    .plan_details {
        padding: 10px 0 10px;
    }

</style>

    <form method="POST">
        <div class="top-tabs">
            <ul class="nav nav-tabs">
                <li role="presentation" id="tab_home" class="tb active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=$_->l('Основные')?></a></li>
               <?/* <li role="presentation" id="tab_prices" class="tb "><a href="#prices" aria-controls="options" role="tab" data-toggle="tab">Цены</a></li>*/?>
                <li role="presentation" id="tab_options" class="tb "><a href="#options" aria-controls="options" role="tab" data-toggle="tab"><?=$_->l('Опции')?></a></li>
                <li role="presentation" id="tab_options" class="tb "><a href="#prices" aria-controls="prices" role="tab" data-toggle="tab"><?=$_->l('Цены')?></a></li>
                <?if($plan->id){?>
                <li role="presentation" id="tab_options" class="tb "><a href="#links" aria-controls="links" role="tab" data-toggle="tab"><?=$_->l('Ссылки')?></a></li>
                <?}?>

            </ul>
        </div>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">

                <div class="form-group">
                    <!-- Username -->
                    <label class="control-label" for="username"><?=$_->l('Название')?></label>

                    <div class="controls">
                        <input type="text" id="name" name="name" value="<?= $plan->name ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>

                <div class="form-group">
                    <!-- E-mail -->
                    <label class="control-label" for="name"><?=$_->l('Цена')?>, <?= $dcurrency->displayName() ?></label>

                    <div class="controls">
                        <input type="text" id="price" name="price" value="<?= $plan->price ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">

                    </div>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" <?= $plan->test_days != 0 ? 'checked="checked"':'' ?> name="test_enabled" value="1" onchange="$('#test_period').toggle()"> <?=$_->l("Включить возможность заказа тестового периода")?>
                    </label>
                </div>
                <div id="test_period" class="form-group" <?= $plan->test_days == 0 ? 'style="display: none"': '' ?>>
                    <label for="test_days"><?=$_->l('Кол-во дней для тестирования')?> </label>
                    <input type="number" value="<?= $plan->test_days ?>" name="test_days" class="form-control" id="test_days" placeholder="7">
                </div>

                <div class="form-group">
                    <label class="control-label" for="name"><?=$_->l('Сервер')?></label>
            <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title=""
                  data-original-title="<?=$_->l('Сервер из которого будет получен список пакетов хостинг панели.')?>"></span>
                    <select name="server_id" class="input-xlarge form-control"  data-validate="required">
                        <option value=""> ---</option>
                        <? foreach ($servers as $server) { ?>
                            <option <?= ($plan->server_id == $server->id ? 'selected' : '') ?>
                                value="<?= $server->id ?>"><?= $server->name ?></option>
                        <? } ?>
                    </select>
                </div>

                <script>
                    function loadPlans() {
                        var id_server = ($('select[name=server_id]').val());
                        $('select[name=panel_name]').html('');
                        if (id_server) {
                            $('select[name=server_id]').attr('disabled', 'disabled');
                            $('select[name=panel_name]').prepend($('<option><?=$_->l('Подождите, идет загрузка...')?></option>')).attr('disabled', 'disabled');
                            $.ajax({
                                type: 'post',
                                dataType: 'json',

                                data: {server_id: id_server, ajax: 1, action: 'getPlans'},
                                success: function (data) {
                                    $('select[name=panel_name]').html('');
                                    if (data.result == 1) {
                                        var def = '<?=(\System\Tools::rPOST('panel_name'))?>';

                                        if(data.data==''){
                                            $('select[name=panel_name]').prepend($('<option><?=$_->l('Список шаблонов хостинг панели пуст.')?></option>')).attr('disabled', 'disabled');
                                        }
                                        $.each(data.data, function (key, val) {
                                            $('select[name=panel_name]').prepend($('<option ' + (def == key ? 'selected="selected"' : '') + ' value="' + val + '">' + val + '</option>')).removeAttr('disabled');
                                        });


                                    } else {
                                        if(data.error == -2){
                                            $('select[name=panel_name]').prepend($('<option><?=$_->l('Соединение с сервером отсутствует...')?></option>')).attr('disabled', 'disabled');
                                        }
                                    }
                                    $('select[name=server_id]').removeAttr('disabled');
                                    $('select[name=panel_name]').trigger('change');
                                }

                            })
                        }
                    }
                    $(function () {
                        <?if(!$plan->id && \System\Tools::rPOST('panel_name')){?>
                        loadPlans();
                        <?}?>
                        $('select[name=server_id]').on('change', function () {
                            loadPlans();
                        })
                    })

                </script>

                <div class="form-group">
                    <label class="control-label" for="name"><?=$_->l('План панели')?></label>
            <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title=""
                  data-original-title="<?=$_->l('Название плана в хостинг панеле.')?>"></span>
                    <select name="panel_name" class="input-xlarge form-control"  data-validate="required">
                        <? foreach ($panel_plans as $name) { ?>
                            <option
                                value="<?= $name ?>" <?= ($plan->panel_name == $name ? 'selected' : '') ?>><?= $name ?></option>
                        <? } ?>
                    </select>
                </div>
                <?/*
                <button class="btn btn-xs" id="set_manual_plan" >Ручной ввод</button>
                <script>
                    $('#set_manual_plan').on('click', function(){
                        $('select[name=panel_name]').replaceWith('<input type="text" class="form-control">');
                        $('form').validate({messages: validate_messages});
                        return false;
                    })
                </script>
*/?>
                <? if (isset($error['message']) && $error['message'] == 'no_plan') { ?>
                    <div class="alert alert-danger" role="alert" style="margin-top: 15px;">
                        <?=$_->l('Сервер %server не содержит плана %plan. Создайте данный план на всех серверах, которые вы выбираете!', array('server' => $error['server']->name, 'plan' => $error['plan'] ))?>
                    </div>
                <? } ?>
                <div class="form-group">
                    <label class="control-label" for="name"><?=$_->l('Доступные для выбора сервера')?></label>
            <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="right" title=""
                  data-original-title="<?=$_->l('На всех выбраных серверах должен быть создан тариф с именем выбранным в поле &quot;План панели&quot; ')?>"></span>
                    <select name="aviable_servers[]" multiple class="input-xlarge form-control"  data-validate="required">
                        <? foreach ($servers as $server) { ?>
                            <option <?= (in_array($server->id, explode('|', $plan->aviable_servers)) ? 'selected' : '') ?>
                                value="<?= $server->id ?>"><?= $server->name ?></option>
                        <? } ?>
                    </select>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="options">


                    <?= $_->js('jquery-ui.min.js') ?>
                    <?= $_->js('dragtable.js') ?>
                    <a class="btn btn-default open-option"
                       href="<?= $_->link('admin/plan-property/add/' . $plan->id) ?>"><span class="glyphicon glyphicon-ok"
                                                                                            aria-hidden="true"></span><?=$_->l('Добавить')?></a>

                    <div class="plan_details">

                        <table class="table table-bordered dragable">
                            <thead>
                            <tr>
                                <th width="7px"></th>
                                <th><?=$_->l('Параметр')?></th>
                                <th><?=$_->l('Значение')?></th>
                                <th><?=$_->l('Действие')?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($details as $detail) { ?>
                                <tr data-id="<?= $detail->id ?>">
                                    <th style="cursor: move"><span class="glyphicon glyphicon-move"></span></th>
                                    <th scope="row"><?= $detail->name ?></th>
                                    <td><input type="hidden" name="params_ids[]" value="<?=$detail->id?>"><input name="param_values[]" class="form-control" value="<?= $detail->value ?>"></td>
                                    <td><span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span></td>
                                </tr>
                            <? } ?>


                            </tbody>
                        </table>



                        <script>
                            var tab = '';
                            $('.open-option').on('click', function(e){

                                    $('.plan_details table tbody').append('<tr data-id="1"><td style="cursor: move"><span class="glyphicon glyphicon-move"></span></td><td>' +
                                        '<select name="params_ids[]">' +
                                        <?foreach($all_details as $p){?>
                                        '<option value="<?=$p->id?>"><?=$p->name?></option>' +
                                        <?}?>
                                        '</select>' +
                                        '</td><td>' +
                                        '<input type="text" name="param_values[]" value="" class="form-control"/>' +
                                        '</td><td><span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span></td></tr>');
                                    return false;

                                e.preventDefault();
                            });
                            $(document).on('click', 'span.rm', function () {
                                $(this).parents('tr').remove();
                            })

                        </script>

                    </div>


            </div>
            <div role="tabpanel" class="tab-pane" id="prices">

                <div role="tabpanel" class="tab-pane" id="prices">

                    <a class="btn btn-default add-price"
                       href="#"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?></a>


                    <table style="margin-top: 10px" class="table prices">
                          <thead>
                              <tr>
                                  <th><?=$_->l('Название периода')?></th>
                                  <th><?=$_->l('Период (в месяцах)')?></th>
                                  <th><?=$_->l('Цена за период')?></th>
                                  <th class="text-center"><?=$_->l('Включен')?></th>
                              </tr>
                          </thead>
                            <?foreach ($prices as $price){?>
                                <tr>
                                    <td><input class="form-control" name="prices_name[]" value="<?=$price->name?>" type="text"></td>
                                    <td><input class="form-control" name="prices_period[]" value="<?=$price->period?>" type="text"></td>
                                    <td><input class="form-control" name="prices_price[]" value="<?=$price->price?>" type="text"></td>
                                    <td  class="text-center"><input type="hidden" class="checkbox_value" value="<?=($price->enabled)?>" name="prices_enabled[]">
                                        <input class="checkforinput" <?=($price->enabled ? 'checked="checked"' : '')?> type="checkbox" value="1">
                                        <span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span>
                                    </td>
                                </tr>
                            <?}?>
                    </table>

                    <script>
                        $('a.add-price').on('click', function () {
                            var html = '<tr>' +
                                '<td><input class="form-control" name="prices_name[]" type="text"></td>' +
                                '<td><input class="form-control" name="prices_period[]" type="number"></td>' +
                                '<td><input class="form-control" name="prices_price[]" type="text"></td>' +
                                '<td  class="text-center">  <input type="hidden" value="1" class="checkbox_value" name="prices_enabled[]">' +
                                '<input class="checkforinput"  type="checkbox" checked="checked" value="1">' +
                                '<span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span>' +
                                '</td>' +
                                '</tr>';

                            $('table.prices').append(html);
                        });

                        $('body').on('change', '.checkforinput', function () {
                            if($(this).prop("checked") == true){
                                $(this).parent().find('input.checkbox_value').val("1");

                            }else{
                                $(this).parent().find('input.checkbox_value').val("0");
                            }
                        });
                        $(document).on('ready',function () {
                            $(".tb a").on("click", function () {
                                window.location.hash = $(this).attr("href");
                            });
                            url = window.location.hash;
                            if(url) $('a[href=' + url + ']').trigger("click");
                        })
                    </script>
            </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="links">
                <div class="form-group">
                    <!-- Username -->
                    <label class="control-label" for="username"><?=$_->l('Ссылка для заказа тарифного плана')?></label>

                    <div class="controls">
                        <input type="text" id="name" name="name" value="<?= $_->link('order/plan/'.$plan->id) ?>" disabled="disabled" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?></button>
            </div>

        </div>

    </form>
</div>