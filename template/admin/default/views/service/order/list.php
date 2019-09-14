<div class="ajax-block">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="8%">№
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
            <th width="18%"><?= $_->l('Категория') ?>

                <div>
                    <select name="category_id" class="filter" data-type="equal">
                        <option value=""> ---</option>
                        <? foreach ($categories as $category) { ?>
                            <option
                                value="<?= $category->id ?>" <?= (isset($filter['category_id']) && $filter['category_id'] == $category->id) ? 'selected="selected"' : '' ?>><?= $category->name ?></option>
                        <? } ?>
                    </select>

                </div>
            </th>
            <th width="15%"><?=$_->l('Клиент')?>
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
            <th width="10%"><?=$_->l('Дата')?></th>
            <th><?=$_->l('Тариф')?></th>
            <th><?=$_->l('Стоимость')?></th>

            <th><?=$_->l('Конец')?></th>

            <th style="text-align: center"><?=$_->l('Статус')?></th>
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
                <th scope="row"><?= $order->category ?></th>
                <td><a href="<?= $_->link('admin/client/info/' . $order->client_id) ?>"
                       class="ajax-modal"><?= $order->user_name ?></a></td>
                <td><?= $order->date ?></td>
                <td><?= $order->name ?></td>

                <td><?= $order->price ?></td>
                <td><?= ($order->type == 0 ) ? $order->paid_to : '---' ?></td>


                <td style="text-align: center">
                    <?if($order->status == 1 && $order->type == 0){?>
                        <span class="label label-success"><?=$_->l('Активный')?></span>
                    <?} else if($order->status == 0 && $order->type == 0){?>
                        <span class="label label-danger"><?=$_->l('Отключен')?></span>
                    <?} else if($order->status == 1 && $order->type == 1){?>
                        <span class="label label-success"><?=$_->l('Оплачено')?></span>
                    <?} else if($order->status == 0 && $order->type == 1){?>
                        <span class="label label-danger"><?=$_->l('Не оплачено')?></span>
                    <?}?>

                <td><!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <span class="glyphicon glyphicon-cog"></span>&nbsp; <?=$_->l('Управление')?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= $_->link('admin/service-orders/edit?id_service_order=' . $order->id) ?>"
                                   class="ajax-modal"><span
                                            class="glyphicon glyphicon-file"></span>&nbsp;<?= $_->l('Изменить') ?></a>
                            </li>
                            <li><a href="<?= $_->link('admin/bills?id_service_order=' . $order->id . '') ?>"><span
                                        class="glyphicon glyphicon-file"></span>&nbsp;<?=$_->l('Счета')?></a></li>
                            <li><a href="<?= $_->link('admin/service-orders/show?id_order='.$order->id) ?>" class="ajax-modal"><span
                                        class="glyphicon glyphicon-eye-open"></span>&nbsp;<?=$_->l('Посмотреть детали')?></a></li>
                            <li><a href="<?= $_->link('admin/service-orders/info?id_order='.$order->id) ?>" class="ajax-modal"><span
                                        class="glyphicon glyphicon-info-sign"></span>&nbsp;<?=$_->l('Добавить информацию')?></a></li>

                            <li><a href="<?= $_->link('admin/service-orders/remove?id_order=' . $order->id) ?>" class="ajax-action"
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
