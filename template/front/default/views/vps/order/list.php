<script>
$(document).ready(function() {
    $(".noVNC_btn").click(function() {
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                action: 'accessWithNoVNC',
                ajax: 1
            },
            complete: function(data) {                
                splited = data["responseText"].split(" ")
                $("#vncModalHeader").append("<b>Access VPS with VNC client</b>");
                $("#vncModalBody").append("Now you can access your VPS using <a href='https://www.realvnc.com/en/connect/download/viewer/' target='_blank'>RealVNC</a> or other VNC clients. <p><p> <b>VNC Server:</b> " + splited[0] + ":" + splited[1] + "<p><b>Password: </b>" + splited[2]);
                $("#vncModal").modal('show');
            }
        })
    });

    $(".btn-start").click(function() {
        var order = $(this).attr("value").split(',')
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                command: "start",
                action: 'manageVM',
                ajax: 1
            },
            complete: function(data) {
                location.reload();
            }
        })
    });

    $(".btn-stop").click(function() {
        var order = $(this).attr("value").split(',')
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                command: "stop",
                action: 'manageVM',
                ajax: 1
            },
            complete: function(data) {
                location.reload();
            }
        })
    });

    $(".btn-reset").click(function() {
        var order = $(this).attr("value").split(',')
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                command: "reset",
                action: 'manageVM',
                ajax: 1
            },
            complete: function(data) {
                location.reload();
            }
        })
    });

    $(".btn-shutdown").click(function() {
        var order = $(this).attr("value").split(',')
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                command: "shutdown",
                action: 'manageVM',
                ajax: 1
            },
            complete: function(data) {
                location.reload();
            }
        })
    });

    $(".btn-reboot").click(function() {
        var order = $(this).attr("value").split(',')
        $.ajax({
            method: 'post',
            data: {
                order: $(this).attr("value").split(';'),
                command: "reboot",
                action: 'manageVM',
                ajax: 1
            },
            complete: function(data) {
                location.reload();
            }
        })
    });
});
</script>
<div class="ajax-block">
    <div class="frame">
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width:7%">№
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" name="id" class=" filter" data-field="id"
                            value="<?= isset($filter['id']) ? $filter['id'] : '' ?>">
                    </div>
                </th>
                <th><?= $_->l('Дата') ?></th>
                <th><?= $_->l('Тариф') ?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="hosting_plan_id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="hosting_plan_id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <select class="filter" name="plan_id">
                            <option value=""> --- </option>
                            <? foreach ($plans as $plan) { ?>
                            <option
                                <?= (isset($filter['plan_id']) && $filter['plan_id'] == $plan->id ? 'selected="selected"' : '') ?>
                                value="<?= $plan->id ?>"><?= $plan->name ?></option>
                            <? } ?>
                        </select>

                    </div>
                </th>
                <th> <?= $_->l('Логин') ?>

                    <div class="sorting">
                        <a href="#" class="order" data-field="username" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="username" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" name="username" class=" filter" data-field="username"
                            value="<?= isset($filter['username']) ? $filter['username'] : '' ?>">
                    </div>
                </th>
                <th> <?= $_->l('Стоимость') ?></th>
                <th> <?= $_->l('Конец') ?></th>
                <th> <?= $_->l('Осталось') ?></th>
                <th> <?= $_->l('Статус') ?></th>
                <th> <?= $_->l('VPS Status') ?></th>
                <th> <?="Debug"?> </th>
                <th><?= $_->l('Actions') ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <? if (count($orders) == 0) { ?>
            <tr class="text-center">
                <td colspan="100%"><?= $_->l('No orders to show') ?></td>
            </tr>
            <? } ?>
            <? foreach ($orders as $order) { ?>
            <?
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($order->paid_to);
            // print_r( $datetime2 ).' -- ';
            if ($datetime1 <= $datetime2) {
                //echo 11;
                $interval = $datetime1->diff($datetime2);
            } else {
                $interval = $datetime1->diff(new DateTime());
            }

            ?>

            <tr>
                <th scope="row"><?= $order->id ?></th>
                <td><?= $order->date ?></td>
                <td><?= $order->name ?></td>

                <td><?= $order->username ?></td>
                <td><?= $currency->displayPrice($order->price) ?></td>
                <td><?= $order->paid_to ?></td>
                <td><?= $interval->format('%a days') ?></td>

                <td>
                    <? if ($order->active) { ?>
                    <span class="label label-success"><?= $_->l('Active') ?></span>
                    <? } else { ?>
                    <span class="label label-danger"><?= $_->l('Disabled') ?></span>
                    <? } ?>
                </td>

                <td class="div-center">
                    <? if($order->vm_status == "running"){ ?>
                    <span class="label label-success"><?= $_->l('Running') ?></span>
                    <? } else if($order->vm_status == "stopped"){ ?>
                    <span class="label label-danger"><?= $_->l('Stopped') ?></span>
                    <? }else{ ?>
                    <span class="label label-default"><?= $_->l('N/A') ?></span>
                    <? } ?>
                </td>
                <td class="div-center">
                    <span class="label label-default">VMID: <?= $order->vmid ?></span>
                    <span class="label label-default">Server: <?= $order->server_name ?></span>
                    <? if( $order->has_qga_configured){ ?>
                    <span class="label label-success"><?= $_->l('QGA: Yes') ?></span>
                    <? } else {?>
                    <span class="label label-danger"><?= $_->l('QGA: No') ?></span>
                    <? } ?>
                </td>
                <td>
                    <table class="my_table">
                        <? if($order->vm_status == "running"){ ?>
                        <tr>
                            <td>
                                <button class="btn btn-xs btn-danger btn-stop"
                                    value=<?=http_build_query($order, '', ',')?>>
                                    <span class="glyphicon glyphicon-stop"></span>
                                    <?= $_->l('Stop') ?>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn btn-xs btn-danger btn-reset"
                                    value=<?=http_build_query($order, '', ',')?>>
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <?= $_->l('Reset') ?>
                                </button>
                            </td>
                        </tr>
                        <? if( $order->has_qga_configured){  ?>
                        <tr>
                            <td>
                                <button class="btn btn-xs btn-danger btn-shutdown"
                                    value=<?=http_build_query($order, '', ',')?>>
                                    <span class="glyphicon glyphicon-off"></span>
                                    <?= $_->l('Shutdown') ?>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn btn-xs btn-success btn-reboot"
                                    value=<?=http_build_query($order, '', ',')?>>
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <?= $_->l('Reboot') ?>
                                </button>
                            </td>
                        </tr>
                        <? } ?>
                        <? } else if ($order->vm_status == "stopped"){?>
                        <tr>
                            <td>
                                <button class="btn btn-xs btn-success btn-start"
                                    value=<?=http_build_query($order, '', ',')?>>
                                    <span class="glyphicon glyphicon-play"></span>
                                    <?= $_->l('Start') ?>
                                </button>
                            </td>
                        </tr>
                        <? } ?>
                        <? if( $order->vm_status != null ){ ?>
                        <tr>
                            <td>
                                <a href="<?= $_->link('') ?>"><span
                                        class="glyphicon glyphicon-play-circle"></span>&nbsp;<?= $_->l('Backup now') ?>
                                </a>
                            </td>
                        </tr>

                        <? if( $order->has_backup_configured ){ ?>
                        <tr>
                            <td>
                                <a href="<?= $_->link('backup-orders/manage/'. $order->vmid) ?>"><span
                                        class="glyphicon glyphicon-wrench"></span>&nbsp;<?= $_->l('Manage backups') ?>
                                </a>
                            </td>
                        </tr>
                        <? } ?>
                        <tr>
                            <td>
                                <button value=<?=http_build_query($order, '', ',')?> <?=($order->vm_status != 'running' ? 'disabled="disabled"' : '') ?> class="btn btn-xs btn-primary noVNC_btn">
                                    <span class="glyphicon glyphicon-new-window"> </span>
                                    <?= $_->l('Access with VNC client') ?>
                                </button>
                            </td>
                        </tr>
                        <? } ?>

                    </table>
                </td>
                <!--Dropdown Settings -->
                <td class="text-center">
                    <!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown"
                            aria-expanded="false">
                            <span class="glyphicon glyphicon-cog"></span>&nbsp; <?= $_->l('Управление') ?> <span
                                class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= $order->server_url ? $order->server_url : $order->server_host ?>"
                                    target="_blank"><span
                                        class="glyphicon glyphicon-list-alt"></span>&nbsp;<?= $_->l('Панель управления') ?></a>
                            </li>
                            <li><a href="<?= $_->link('vps-orders/prolong?id_order=' . $order->id . '') ?>"><span
                                        class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<?= $_->l('Продлить') ?>
                                </a></li>
                            <li><a href="<?= $_->link('bills?id_vps_order=' . $order->id) ?>"><span
                                        class="glyphicon glyphicon-list-alt"></span>&nbsp;<?= $_->l('Счета') ?></a></li>
                            <? if (!$order->active) { ?>
                            <li><a href="<?= $_->link('vps-orders/remove?id_order=' . $order->id) ?>"
                                    class="ajax-action"
                                    data-confirm="<?= $_->l('Вы уверены что хотите удалить заказ ?') ?>"><span
                                        class="glyphicon glyphicon-remove-circle"></span>&nbsp;<?= $_->l('Удалить заказ') ?>
                                </a>
                            </li>
                            <? } ?>
                        </ul>
                    </div>
                </td>
            </tr>
            <? } ?>
            <div class="container">
                <div class="modal fade" id="vncModal" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" id="vncModalHeader">
                                <!-- Content added later -->
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body" id="vncModalBody">
                                <!-- Content added later -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
<style>
thead>tr>th {
    text-align: center;
}

.div-center {
    text-align: center;
}

.my_table tr {
    display: flex;
}

.my_table td {
    margin: 2px;
    display: flex;
    flex-grow: 1;
    /* centering the button */
    align-items: center;
    justify-content: center;
}

.inner_button {
    display: flex;
    /* centering the text inside the button */
    align-items: center;
    justify-content: center;
}
</style>