<div class="ajax-block">


    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

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
            <th><?= $_->l('Дата') ?></th>

            <th><?= $_->l('Домен') ?>

                <div class="sorting">
                    <a href="#" class="order" data-field="domain" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="domain" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>
                    <input type="text" name="domain" class=" filter" data-field="domain"
                           value="<?= isset($filter['domain']) ? $filter['domain'] : '' ?>">
                </div>
            </th>

            <th><?= $_->l('DNS сервера') ?></th>
            <th><?= $_->l('Конец') ?></th>
            <th><?= $_->l('Срок действия') ?></th>
            <th class="text-center"><?= $_->l('Статус') ?></th>
            <th><?= $_->l('Действие') ?></th>

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
            $datetime2 = new DateTime($order->date_end);
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


                <td><?= $order->domain ?></td>

                <td>
                    <?= $order->dns1 ?> <br>
                    <?= $order->dns2 ?> <br>
                    <?= $order->dns3 ?> <br>
                    <?= $order->dns4 ?>
                </td>

                <td><?= $interval->format('%a дней') ?></td>
                <td><?= $order->date_end ?></td>

                <td class="text-center">
                    <? if ($order->status == 1) { ?>
                        <span class="label label-success"><?= $_->l('Активный') ?></span>
                    <? } else if ($order->status == 2) { ?>
                        <span class="label label-danger" data-toggle="tooltip" data-placement="top"
                            data-original-title="Оплата получена, но в процессе обработки произошли ошибки"
                            ><?= $_->l('Ошибка обработки') ?></span>
                    <? } else if ($order->status == -1) { ?>
                        <span class="label label-danger"><?= $_->l('Отменен') ?></span>
                    <? } else if ($order->status == 0) { ?>
                        <span class="label label-warning"><?= $_->l('Ожидает оплату') ?></span>
                    <? } else if ($order->status == 3) { ?>
                        <span class="label label-warning"><?= $_->l('В обработке') ?></span>
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
                            <li><a href="<?= $_->link('bills?id_domain_order=' . $order->id) ?>"><span
                                        class="glyphicon glyphicon-list-alt"></span>&nbsp;<?= $_->l('Счета') ?></a></li>

                            <? if ($order->status == 0) { ?>
                                <?if(!$order->owner_id){?>
                                <li><a href="<?= $_->link('domain-orders/order/' . $order->id) ?>"
                                       class=""><span
                                            class="glyphicon glyphicon-play"></span>&nbsp;<?= $_->l('Продолжить оформление') ?>
                                    </a></li>
                                <?}?>

                                <li><a href="<?= $_->link('domain-orders/remove?id_order=' . $order->id) ?>"
                                       class="ajax-action"><span
                                            class="glyphicon glyphicon-remove-circle"></span>&nbsp;<?= $_->l('Отменить заказ') ?>
                                    </a></li>
                            <? } ?>
                            <? if ($order->status == 1 || $order->status == 2) { ?>
                                <li><a href="<?= $_->link('domain-orders/ns-change?id_order=' . $order->id) ?>"
                                       class="ajax-modal"><span
                                            class="glyphicon glyphicon-retweet"></span>&nbsp;<?= $_->l('Изменить NS сервера') ?>
                                    </a></li>
                            <? } ?>
                            <? if ($order->status == 1) { ?>
                                <li><a href="<?= $_->link('domain-orders/prolong?id_order=' . $order->id) ?>"
                                        ><span
                                            class="glyphicon glyphicon-play"></span>&nbsp;<?= $_->l('Продлить') ?></a>
                                </li>
                            <? } ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
