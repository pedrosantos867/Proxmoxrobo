

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
                <li role="presentation" id="tab_home" class="tb active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Основные</a></li>
                <?/* <li role="presentation" id="tab_prices" class="tb "><a href="#prices" aria-controls="options" role="tab" data-toggle="tab">Цены</a></li>*/?>
                <li role="presentation" id="tab_options" class="tb "><a href="#params" aria-controls="options" role="tab" data-toggle="tab">Параметры</a></li>
                <li role="presentation" id="tab_options" class="tb "><a href="#options" aria-controls="options" role="tab" data-toggle="tab">Опции</a></li>


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

                    <label class="control-label"><?=$_->l('Тип')?></label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="0" <?=$plan->type == 0 ? 'checked="checked"' : ''?>  >
                            <?=$_->l('Установка из ISO')?>
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="type" value="1" <?=$plan->type == 1 ? 'checked="checked"' : ''?> >
                            <?=$_->l('Установка из шаблона ОС (LXC)')?>
                        </label>
                    </div>

                </div>

                <?
                $net_types = [
                    \model\VpsServer::PANEL_VMMANAGER => [
                        'kvm' => [
                            1 => 'NAT (Auto IPv4)',
                            3 => 'NAT (IP list of IPv4)',
                            4 => 'PUBLIC (Auto IPv4)',
                            5 => 'PUBLIC (IP list of IPv4)'
                        ],
                        'lxc' => [
                            6 => 'PRIVATE (Auto IPv4)',
                            7 => 'PRIVATE (IP list of IPv4)',
                            1 => 'NAT (Auto IPv4)',
                            3 => 'NAT (IP list of IPv4)',
                            4 => 'PUBLIC (Auto IPv4)',
                            5 => 'PUBLIC (IP list of IPv4)',

                        ]
                    ],
                    \model\VpsServer::PANEL_PROXMOX => [
                        'kvm' => [
                            1 => 'NAT',
                            2 => 'Bridge vmbr0 with VLAN tag (VLAN list)',
                        ],
                        'lxc' => [
                            1 => 'Bridge vmbr0 with VLAN tag (DHCP IPv4)',
                            2 => 'Bridge vmbr0 with VLAN tag (Static IPv4)',
                            3 => 'Bridge vmbr0 (Static IPv4)',
                            4 => 'Bridge vmbr0 (DHCP IPv4)',
                        ]
                    ]
                ];


                ?>
                <script>
                    var net_types = '<?=json_encode($net_types)?>';
                    var panel = 0;
                    var type = 0;
                    var net_type = '<?=$plan->net_type?>';
                </script>

                <script>
                    function getIsoImages(server_id, node) {
                        $.ajax({
                            method: 'post',
                            data: {ajax:1,'action': 'getServerImages', server_id: server_id, node: node},
                            dataType:'json',
                            success: function (data) {
                                var html = '';
                                for(var i in data){
                                    html += '<option value="'+(i ? i : data[i])+'">'+(data[i])+'</option>';
                                }
                                $('select#images').html(html);
                                <?if(isset($images)){foreach($images as $image){?>
                                $('select#images option[value="<?=$image?>"]').attr('selected', 'selected').trigger('change');
                                <?}}?>


                            }
                        })
                    }

                    function getContainers(server_id, node) {
                        $.ajax({
                            method: 'post',
                            data: {ajax:1,'action': 'getServerContainers', server_id: server_id, node: node},
                            dataType:'json',
                            success: function (data) {
                                var html = '';
                                for(var i in data){
                                    html += '<option value="'+(i ? i : data[i])+'">'+(data[i])+'</option>';
                                }
                                $('select#images').html(html);
                                <?if(isset($images)){foreach($images as $image){?>
                                $('select#images option[value="<?=$image?>"]').attr('selected', 'selected').trigger('change');
                                <?}}?>


                            }
                        })
                    }

                    function getRecipes(server_id, node) {
                        $.ajax({
                            method: 'post',
                            data: {ajax:1,'action': 'getServerRecipes', server_id: server_id, node: node},
                            dataType:'json',
                            success: function (data) {
                                var html = '<option value=""><?=$_->l('Не использовать')?></option>';
                                for(var i in data){
                                    html += '<option '+("<?=$plan->recipe?>" == (i ? i : data[i]) ? 'selected="selected"':'')+' value="'+(i ? i : data[i])+'">'+(data[i])+'</option>';
                                }
                                $('select#recipe').html(html);




                            }
                        })
                    }
                    function eventChangeType() {

                        var server_id = ($('#available_servers').val());
                        var node      = $('select[name=node]').val();

                        if($('input[name=type]:checked').val()==1){
                            type = 'lxc';
                            if(typeof server_id !== 'undefined') {
                                //get containers to images select
                                getContainers(server_id[0], node);
                                getRecipes(server_id[0], node);
                            }
                            $('#recipe-group').show();

                        } else {
                            type = 'kvm';
                            //get iso images to images select
                            if(typeof server_id !== 'undefined') {
                                getIsoImages(server_id[0], node);
                            }
                            $('#recipe-group').hide();
                        }

                    }

                    $('input[name=type]').on('change', function () {
                        eventChangeType();
                    });
                    eventChangeType();
                </script>
                
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
                        <input type="checkbox" <?= $plan->test_days != 0 ? 'checked="checked"':'' ?> name="test_enabled" value="1" onchange="$('#test_period').toggle()"> Включить возможность заказа тестового периода
                    </label>
                </div>
                <div id="test_period" class="form-group" <?= $plan->test_days == 0 ? 'style="display: none"': '' ?>>
                    <label for="test_days"><?= $_->l('Кол-во дней для тестирования') ?></label>
                    <input type="number" value="<?= $plan->test_days ?>" name="test_days" class="form-control" id="test_days" placeholder="7">
                </div>




                <div class="form-group">
                    <label class="control-label" for="name"><?=$_->l('Доступные для выбора сервера')?></label>

                    <select id="available_servers" name="available_servers[]" multiple class="input-xlarge form-control"  data-validate="required">
                        <? foreach ($servers as $server) { ?>
                            <option <?= (in_array($server->id, explode('|', $plan->available_servers)) ? 'selected="selected"' : '') ?>
                               data-panel="<?= $server->type ?>" value="<?= $server->id ?>"><?= $server->name ?></option>
                        <? } ?>
                    </select>

                    <script>



                        $('select#available_servers').on('change',function(){
                            var server_id = ($(this).val());
                            panel = $(this).find('option:selected').data('panel');

                            $.ajax({
                                method: 'post',
                                data: {ajax:1,'action': 'getServerNodes', server_id: server_id[0]},
                                dataType:'json',
                                success: function (data) {
                                    if(data) {
                                        var html = '';


                                        for (var i in data) {
                                            html += '<option value="' + (i ? i : data[i]) + '">' + (data[i]) + '</option>';
                                        }

                                        $('select#nodes').html(html);
                                        $('select#nodes option:first').attr('selected', 'selected').trigger('change');

                                        changePanel(panel);

                                    }
                                }
                            })
                        });

                        $('select#available_servers').trigger('change');

                        function changePanel(panel) {
                            if (panel == <?=\model\VpsServer::PANEL_PROXMOX?>) {
                                $('.proxmox').show();
                                $('.vmmanager').hide();
                            } else if (panel == <?=\model\VpsServer::PANEL_VMMANAGER?>) {
                                $('.proxmox').hide();
                                $('.vmmanager').show();
                            }

                            displayNetworkTypes(panel, type);
                        }

                        function displayNetworkTypes(panel, type) {
                            var net_types_object = JSON.parse(net_types);

                            var html = '';
                           for(var i in net_types_object[panel][type]){
                                html += '<option value="'+i+'" '+ (net_type == i ? 'selected="selected"' : '') +' >'+ net_types_object[panel][type][i] +'</option>';
                              // console.log(net_types_object[panel][type][i]);
                           }

                            $('select[name=net_type]').html(html);
                        }
                    </script>
                </div>
                <div class="form-group">
                    <label class="control-label" for="name"><?=$_->l('Доступные ноды сервера')?></label>

                    <select id="nodes" name="node" class="input-xlarge form-control"  data-validate="required">

                    </select>

                    <script>

                        $('select#nodes').on('change',function(){
                            eventChangeType();
                        })

                    </script>

                </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="params">

                <div class="form-group">
                    <label class="control-label" for="memory"><?=$_->l('ОЗУ (Мб)')?></label>
                    <div class="controls">
                        <input type="number" id="memory" name="memory" value="<?= $plan->memory ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="hdd"><?=$_->l('HDD (Гб)')?></label>
                    <div class="controls">
                        <input type="number" id="hdd" name="hdd" value="<?= $plan->hdd ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="cores"><?=$_->l('Количество ядер процессора')?></label>
                    <div class="controls">
                        <input type="number" id="cores" name="cores" value="<?= $plan->cores ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>
                <div class="form-group proxmox">
                    <label class="control-label" for="socket"><?=$_->l('Номер сокета процессора')?></label>
                    <div class="controls">
                        <input type="number" id="socket" name="socket" value="<?= $plan->socket ?>" placeholder=""
                               class="input-xlarge form-control"  data-validate="required">
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label" for="socket"><?=$_->l('Подключение к сети')?></label>
                    <div class="controls">
                       <select name="net_type" class="form-control">
                           <option value="1" <?=($plan->net_type == 1) ? 'selected="selected"' : ''?>>NAT</option>
                           <option value="2" <?=($plan->net_type == 2) ? 'selected="selected"' : ''?>>Bridge VLAN DHCP</option>
                           <option value="3" <?=($plan->net_type == 3) ? 'selected="selected"' : ''?>>STATIC IPv4</option>
                           <option value="3" <?=($plan->net_type == 4) ? 'selected="selected"' : ''?>>PUBLIC IPv4</option>
                       </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label" for="images"><?=$_->l('Доступные образы')?></label>
                    <div class="controls">
                        <select id="images" name="images[]" class="form-control" multiple="multiple">
                            <? foreach ($images as $image) { ?>
                                <option value="<?=$image?>" <?= (in_array($image, explode('|', $plan->images)) ? 'selected' : '') ?>><?=$image?></option>
                            <? }?>
                        </select>
                    </div>
                </div>

                <div id="recipe-group" class="form-group vmmanager">
                    <label class="control-label" for="images"><?=$_->l('Рецепт установки')?></label>
                    <div class="controls">
                        <select id="recipe" name="recipe" class="form-control">
                            <? foreach ($recipes as $recipe) { ?>
                                <option value=""><?=$_->l('Не использовать')?></option>
                                <option value="<?=$recipe?>" <?= ($recipe== $plan->recipe ? 'selected' : '') ?>><?=$recipe?></option>
                            <? }?>
                        </select>
                    </div>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="options">
                <? if (isset($details)) { ?>

                    <?= $_->js('jquery-ui.min.js') ?>
                    <?= $_->js('dragtable.js') ?>
                    <div class="form-group">
                        <button id="add-param" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span> <?=$_->l('Добавить')?>
                        </button>
                        <script>
                            $('#add-param').on('click', function () {
                                $('.plan_details table tbody').append('<tr data-id="1"><td style="cursor: move"><span class="glyphicon glyphicon-move"></span></td><td>' +
                                    '<select name="params_ids[]">' +
                                    <?foreach($all_params as $p){?>
                                    '<option value="<?=$p->id?>"><?=$p->name?></option>' +
                                    <?}?>
                                    '</select>' +
                                    '</td><td>' +
                                    '<input type="text" name="param_values[]" value="" class="form-control"/>' +
                                    '</td><td><span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span></td></tr>');
                                return false;
                            })
                        </script>
                    </div>

                    <div class="plan_details">
                        <table class="table table-bordered dragable">
                            <thead>
                            <tr>
                                <th width="7px"></th>
                                <th><?=$_->l('Параметр')?></th>
                                <th><?=$_->l('Значение')?></th>
                                <th width="5px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($params as $param) { ?>
                                <tr data-id="<?= $param->id ?>">
                                    <td style="cursor: move"><span class="glyphicon glyphicon-move"></span></td>
                                    <td scope="row"><?= $param->name ?></td>
                                    <td><input type="hidden" name="params_ids[]" value="<?=$param->id?>"><input type="text" name="param_values[]" value="<?=$param->value?>" class="form-control"/></td>
                                    <td><span style="cursor:pointer;" class="glyphicon glyphicon-trash rm"></span></td>
                                </tr>
                            <? } ?>


                            </tbody>
                        </table>
                        <script>
                            $(document).on('click', 'span.rm', function () {
                                $(this).parents('tr').remove();
                            })
                        </script>



                    </div>

                <? } ?>
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?></button>
            </div>
            <?/*<div role="tabpanel" class="tab-pane" id="prices">
              <table class="table">
                  <tr>
                      <td>Период</td>
                      <td>Цена</td>

                      <td class="text-center">Включен</td>
                  </tr>
                  <tr>
                      <td>Неделя</td>
                      <td><input class="form-control" type="number"></td>

                      <td  class="text-center"><input type="checkbox"></td>
                  </tr>
                  <tr>
                      <td>Месяц</td>
                      <td><input class="form-control" type="number"></td>

                      <td  class="text-center"><input type="checkbox"></td>
                  </tr>
                  <tr>
                      <td>Пол года</td>
                      <td><input class="form-control" type="number"></td>

                      <td  class="text-center"><input type="checkbox"></td>
                  </tr>
                  <tr>
                      <td>Год</td>
                      <td><input class="form-control" type="number"></td>

                      <td  class="text-center"><input type="checkbox"></td>
                  </tr>
              </table>
            </div>*/?>
        </div>





    </form>
</div>