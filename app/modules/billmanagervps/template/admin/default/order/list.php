<div class="ajax-block">
    <table class="table table-bordered">
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

            <th width="40%"><?=$_->l('Клиент')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="client" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="client" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>

                    <input type="text" data-type="like" name="client" class=" filter" data-field="client"
                           value="<?= isset($filter['client']) ? $filter['client'] : '' ?>">

                </div>
            </th>

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
                            <option <?= (isset($filter['plan_id']) && $filter['plan_id'] == $plan->id ? 'selected="selected"' : '') ?>
                                value="<?= $plan->id ?>"><?= $plan->name ?></option>
                        <? } ?>
                    </select>

                </div>
            </th>


            <th> ORDER ID </th>
            <th> <?= $_->l('Конец') ?></th>
            <th> <?= $_->l('Осталось') ?></th>

            <th> <?= $_->l('Статус') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($orders) == 0) { ?>
            <tr class="text-center">
                <td colspan="11"><?= $_->l('Результаты не найдены.') ?></td>
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

                <td>
                    <a href="<?= $_->link('admin/client/info/' . $order->client_id) ?>"
                       class="ajax-modal"><?= $order->client ?></a>
                </td>
                <td><?= $order->name ?></td>
                <td><?= $order->order_id ?></td>


                <td><?= $order->paid_to ?></td>
                <td><?= $interval->format('%a дней') ?></td>


                <td>
                    <? if ($order->active) { ?>
                        <span class="label label-success"><?= $_->l('Активный') ?></span>
                    <? } else { ?>
                        <span class="label label-danger"><?= $_->l('Отключен') ?></span>
                    <? } ?>
                </td>


                <td class="text-center"><!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown"
                                aria-expanded="false">
                            <span class="glyphicon glyphicon-cog"></span>&nbsp; <?= $_->l('Управление') ?> <span
                                class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">

                            <?if($order->active){?>
                            <li><a href="<?= $_->link('admin/modules/billmanagervps/order/info?id_order=' . $order->id . '') ?>" class="ajax-modal"><span
                                        class="glyphicon glyphicon-eye-open"></span>&nbsp;<?= $_->l('Информация') ?>
                                </a></li>
                            <?}?>

                            <li><a href="<?= $_->link('admin/modules/billmanagervps/order/remove?id_order=' . $order->id . '') ?>" class="ajax-action"><span
                                        class="glyphicon glyphicon-trash"></span>&nbsp;<?= $_->l('Удалить') ?>
                                </a></li>



                        </ul>
                    </div>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
