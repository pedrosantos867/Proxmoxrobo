<div class="ajax-block">
    <a href="<?= $_->link('admin/order/add') ?>" class="btn btn-default"><span class="glyphicon glyphicon-ok"
                                                                               aria-hidden="true"></span><?=$_->l('Добавить')?></a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 7%">№
                <div class="sorting">
                    <a href="#" class="order" data-field="id" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="id" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>
                    <input type="text" data-type="equal" name="id" class=" filter" data-field="id"
                           value="<?= isset($filter['id']) ? $filter['id'] : '' ?>">
                </div>
            </th>
            <th><?=$_->l('Клиент')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="name" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="name" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>
                    <input type="text" data-type="like" name="name" class=" filter" data-field="name"
                           value="<?= isset($filter['name']) ? $filter['name'] : '' ?>">
                </div>
            </th>
            <th><?=$_->l('Дата')?></th>
            <th><?=$_->l('Тариф')?></th>
            <th><?=$_->l('Логин')?></th>
            <th><?=$_->l('Стоимость')?></th>
            <th><?=$_->l('Конец')?></th>
            <th><?=$_->l('Сервер')?></th>
            <th><?=$_->l('Статус')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <? if (count($orders) == 0) { ?>
            <tr>
                <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <? foreach ($orders as $order) { ?>
            <tr>
                <th scope="row"><?= $order->id ?></th>
                <td><a href="<?= $_->link('admin/client/info/' . $order->client_id) ?>"
                       class="ajax-modal"><?= $order->user_name ?></a></td>
                <td><?= $order->date ?></td>
                <td><?= $order->name ?></td>

                <td><?= $order->login ?></td>
                <td><?= $order->price ?></td>
                <td><?= $order->paid_to ?></td>
                <td><?= $order->server_name ?></td>

                <td>
                    <? if ($order->active) { ?>
                        <span class="label label-success"><?= $_->l('Активный') ?></span>
                    <? } else { ?>
                        <span class="label label-danger"><?= $_->l('Отключен') ?></span>
                    <? } ?>
                </td>
                <td><!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <span class="glyphicon glyphicon-cog"></span>&nbsp; <?=$_->l('Управление')?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= $_->link('admin/bills?id_order=' . $order->id . '') ?>"><span
                                        class="glyphicon glyphicon-file"></span>&nbsp;<?=$_->l('Счета')?></a></li>
                            <li><a href="<?= $_->link('admin/order/' . $order->id) ?>"><span
                                        class="glyphicon glyphicon-edit"></span>&nbsp;<?=$_->l('Изменить заказ')?></a></li>
                            <li><a href="<?= $_->link('admin/orders/remove/' . $order->id) ?>" class="ajax-action"
                                   data-confirm="Вы уверенны что хотите удалить заказ ?"><span
                                        class="glyphicon glyphicon-remove-circle"></span>&nbsp;<?=$_->l('Удалить заказ')?></a></li>

                        </ul>
                    </div>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>

    <?= $pagination ?>
</div>
