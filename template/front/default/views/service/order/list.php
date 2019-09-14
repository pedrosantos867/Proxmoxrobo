<div class="ajax-block">
    <?= $_->JS('momentjs/moment.min.js') ?>
    <? if ($lang->iso_code != 'en') { ?>
        <?= $_->JS('momentjs/locale/' . $lang->iso_code . '.js') ?>
    <? } ?>
    <?= $_->JS('daterangepicker.js') ?>
    <?= $_->CSS('daterangepicker.css') ?>


    <script>
        $(function () {

            var reset = 0;
            var reset1 = 0;
            if (!$('input[name="date"]').val()) {
                //  alert($('input[name="date"]').val());
                reset = 1;
            }
            $('input[name="date"]').daterangepicker({
                'locale': {
                    format: 'DD.MM.YYYY',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel'
                }
            }, function (start, end, label) {
            });

            if (reset) {
                $('input[name="date"]').val('');
            }

            $('input[name="date"]').on('cancel.daterangepicker', function (ev, picker) {
                //do something, like clearing an input
                $('input[name="date"]').val('');
                parseFilterFields();

                // getTableWithFilter();
            });
            $('input[name="date"]').on('apply.daterangepicker', function (ev, picker) {
                //do something, like clearing an input

                parseFilterFields();

                // getTableWithFilter();
            });

            if (!$('input[name="paid_to"]').val()) {
                //  alert($('input[name="date"]').val());
                reset1 = 1;
            }
            $('input[name="paid_to"]').daterangepicker({
                'locale': {
                    format: 'DD.MM.YYYY',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel'
                }
            }, function (start, end, label) {
            });

            if (reset1) {
                $('input[name="paid_to"]').val('');
            }

            $('input[name="paid_to"]').on('cancel.daterangepicker', function (ev, picker) {
                //do something, like clearing an input
                $('input[name="paid_to"]').val('');
                parseFilterFields();

                // getTableWithFilter();
            });
            $('input[name="paid_to"]').on('apply.daterangepicker', function (ev, picker) {
                //do something, like clearing an input

                parseFilterFields();

                // getTableWithFilter();
            });


        })
    </script>

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


            <th width="18%">
                <div class="text-center">
                    <?= $_->l('Дата') ?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                    class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                    class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                </div>
                <div>
                    <input type="text" name="date" class=" filter" data-field="id"
                           value="<?= isset($filter['date']) ? $filter['date'] : '' ?>">

                </div>
            </th>
            <th><?=$_->l('Тариф')?></th>
            <th><?=$_->l('Стоимость')?></th>

            <th  width="18%">
                <div class="text-center">
                    <?= $_->l('Конец') ?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                    class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                    class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                </div>
                <div>
                    <input type="text" name="paid_to" class=" filter" data-field="id"
                           value="<?= isset($filter['paid_to']) ? $filter['paid_to'] : '' ?>">

                </div>
            </th>

            <th style="text-align: center"><?=$_->l('Статус')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <? if (count($orders) == 0) { ?>
            <tr class="text-center">
                <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <? foreach ($orders as $order) { ?>
            <tr>
                <th scope="row"><?= $order->id ?></th>

                <td><?= $order->date ?></td>
                <td><?= $order->name ?></td>

                <td><?=  $currency->displayPrice($order->price) ?> </td>
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

                <td style="text-align: center"><!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <span class="glyphicon glyphicon-cog"></span>&nbsp; <?=$_->l('Управление')?> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= $_->link('bills?id_service_order=' . $order->id . '') ?>"><span
                                        class="glyphicon glyphicon-file"></span>&nbsp;<?=$_->l('Счета')?></a></li>
                            <?if( ($order->type == 0 )) {?>
                            <li><a href="<?= $_->link('service-orders/prolong?id_order='.$order->id) ?>" ><span
                                        class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<?=$_->l('Продлить')?></a></li>
                            <?}?>
                            <li><a href="<?= $_->link('service-orders/info?id_order='.$order->id) ?>" class="ajax-modal"><span
                                        class="glyphicon glyphicon-eye-open"></span>&nbsp;<?=$_->l('Информация о заказе')?></a></li>
                            
                            <li><a href="<?= $_->link('service-orders/show?id_order='.$order->id) ?>" class="ajax-modal"><span
                                        class="glyphicon glyphicon-info-sign"></span>&nbsp;<?=$_->l('Посмотреть детали')?></a></li>

                            <li><a href="<?= $_->link('service-orders/remove?id_order=' . $order->id) ?>" class="ajax-action"
                                   data-confirm="<?=$_->l('Вы уверенны что хотите удалить заказ ?')?>"><span
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
