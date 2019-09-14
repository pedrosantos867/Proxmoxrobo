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
            if (!$('input[name="date"]').val()) {
                //  alert($('input[name="date"]').val());
                reset = 1;
            }
            $('input[name="date"]').daterangepicker({
                'locale': {
                    format: 'MM/DD/YYYY',
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

            $('a.ajax-pay').on('click', function () {
                var url = $(this).attr('href');

                loader.display();
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: {ajax: 1},
                    success: function (data) {
                        if (data.result) {
                            loader.hide();
                            getTableWithFilter();
                        }
                    }
                });
                return false;
            })

        })
    </script>
    <? if ($order) { ?>
        <h3> <?= $_->l('Счета по заказу №%bill', array('bill' => $order->id)) ?></h3>
    <? } ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 3%"><input type="checkbox" class="table-checkbox-all"></th>
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
            <th style="width: 10%"><?=$_->l('Дата')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="id" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="id" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>
                    <input type="text" name="date" class=" filter" data-field="id"
                           value="<?= isset($filter['date']) ? $filter['date'] : '' ?>">
                </div>
            </th>
            <th style="width: 10%"><?=$_->l('Пользователь')?></th>
            <th style="width: 15%"><?=$_->l('Услуга')?>

                <div>
                    <select name="type" class="filter" data-type="equal">
                        <option value=""> ---</option>

                        <? if ($config->enable_component_hosting) { ?>
                            <option <?= (isset($filter['type']) && $filter['type'] != '' && $filter['type'] == \model\Bill::TYPE_ORDER) ? 'selected="selected"' : '' ?>
                                value="<?= \model\Bill::TYPE_ORDER ?>"><?= $_->l('Хостинг') ?>
                            </option>
                        <? } ?>

                        <option <?= (isset($filter['type']) && $filter['type'] == \model\Bill::TYPE_CHANGE_PLAN) ? 'selected="selected"' : '' ?>
                            value="<?= \model\Bill::TYPE_CHANGE_PLAN ?>"><?=$_->l('Смена тарифа')?>
                        </option>
                        <option <?= (isset($filter['type']) && $filter['type'] == \model\Bill::TYPE_BALANCE) ? 'selected="selected"' : '' ?>
                            value="<?= \model\Bill::TYPE_BALANCE ?>"><?=$_->l('Пополнение баланса')?>
                        </option>

                        <? if ($config->enable_component_domain) { ?>
                            <option <?= (isset($filter['type']) && $filter['type'] == \model\Bill::TYPE_DOMAIN_ORDER) ? 'selected="selected"' : '' ?>
                                value="<?= \model\Bill::TYPE_DOMAIN_ORDER ?>"><?= $_->l('Домен') ?>
                            </option>
                            <option <?= (isset($filter['type']) && $filter['type'] == \model\Bill::TYPE_DOMAIN_PROLONG) ? 'selected="selected"' : '' ?>
                                value="<?= \model\Bill::TYPE_DOMAIN_PROLONG ?>"><?= $_->l('Продление домена') ?>
                            </option>
                        <? } ?>

                        <? if ($config->enable_component_vps) { ?>
                            <option <?= (isset($filter['type']) && $filter['type'] == \model\Bill::TYPE_VPS) ? 'selected="selected"' : '' ?>
                                value="<?= \model\Bill::TYPE_VPS ?>"><?= $_->l('VPS') ?>
                            </option>
                        <? } ?>


                        <? foreach ($service_categories as $category) {?>
                            <option <?= (isset($filter['type']) && $filter['type'] == 's'.$category->id.'') ? 'selected="selected"' : '' ?> value="s<?=$category->id?>"><?=$category->name?> </option>
                        <?}?>

                        <? foreach ($services as $key => $service) { ?>
                            <option <?= (isset($filter['type']) && $filter['type'] == $key) ? 'selected="selected"' : '' ?>
                                value="<?= $key ?>"><?= $service ?> </option>
                        <? } ?>

                    </select>
                </div>
            </th>

            <th style="width: 10%"><?=$_->l('Тариф')?></th>
            <th style="width: 5%"><?=$_->l('Логин')?>

            </th>
            <th style="width: 10%"><?=$_->l('Стоимость')?></th>
            <th style="width: 5%"><?=$_->l('Сумма')?></th>
            <th style="width: 10%"><?=$_->l('Истекает')?></th>
            <th style="width: 5%"><?=$_->l('Статус')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="is_paid" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="is_paid" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>
                    <select name="is_paid" class="filter" data-type="equal">
                        <option value=""> ---</option>
                        <option <?= (isset($filter['is_paid']) && $filter['is_paid'] != '' && $filter['is_paid'] == 0) ? 'selected="selected"' : '' ?>
                            value="0"><?=$_->l('Ожидает оплату')?>
                        </option>
                        <option <?= (isset($filter['is_paid']) && $filter['is_paid'] == -1) ? 'selected="selected"' : '' ?>
                            value="-1"><?=$_->l('Отменен')?>
                        </option>
                        <option <?= (isset($filter['is_paid']) && $filter['is_paid'] == 1) ? 'selected="selected"' : '' ?>
                            value="1"><?=$_->l('Оплачен')?>
                        </option>
                    </select>
                </div>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($bills) == 0) { ?>
            <tr>
                <td colspan="12"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <? foreach ($bills as $bill) { ?>
            <tr>
                <td>
                    <? if ($bill->is_paid == 0) { ?>
                        <input type="checkbox" class="table-checkbox" data-id="<?= $bill->id ?>">
                    <? } else { ?>
                        <input type="checkbox" disabled="disabled">
                    <? } ?>
                </td>

                <td scope="row"><?= $bill->id ?></td>
                <td><?= date('d-m-Y', strtotime($bill->date)) ?></td>

                <td><a href="<?= $_->link('admin/client/info/' . $bill->client_id) ?>"
                       class="ajax-modal"><?= $bill->client ?></a></td>
                <td>
                    <? if ($bill->type == \model\Bill::TYPE_BALANCE) { ?>
                        <?=$_->l('Пополнение баланса')?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_CHANGE_PLAN) { ?>
                        <?=$_->l('Смена хостинг тарифа')?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_ORDER) { ?>
                        <?=$_->l('Хостинг')?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_DOMAIN_ORDER) { ?>
                        <?=$_->l('Домен')?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_DOMAIN_PROLONG) { ?>
                        <?=$_->l('Продление домена')?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_SERVICE_ORDER) { ?>
                        <?=$bill->category?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_VPS) { ?>
                        <?= $_->l('VPS') ?>
                    <? } else { ?>
                        <?= isset($services['module_' . $bill->type]) ? $services['module_' . $bill->type] : '---' ?>
                    <? } ?>
                </td>


                <td>
                    <? if ($bill->type == \model\Bill::TYPE_CHANGE_PLAN) { ?>
                        (<?= $bill->old_plan . ' ==>> ' . $bill->new_plan ?>)
                    <? } else if ($bill->type == \model\Bill::TYPE_ORDER) { ?>
                        <?= $bill->hosting_plan ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_DOMAIN_ORDER) { ?>
                        <?= $bill->domain_zone ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_DOMAIN_PROLONG) { ?>
                        <?= $bill->domain_zone ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_SERVICE_ORDER) { ?>
                        <?= $bill->service ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_VPS) { ?>
                        <?= $bill->vps_plan ?>
                    <? } else if (isset($bill->plan)) { ?>
                        <?= $bill->plan ?>
                    <? } else { ?>
                        ---
                    <? } ?>
                </td>
                <td>
                    <? if ($bill->type == \model\Bill::TYPE_CHANGE_PLAN) { ?>
                        <?= $bill->login ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_ORDER) { ?>
                        <?= $bill->login ?>
                    <? } else if ($bill->type == \model\Bill::TYPE_VPS) { ?>
                        <?= $bill->vps_username ?>

                    <? } elseif ($bill->type == \model\Bill::TYPE_DOMAIN_ORDER) { ?>
                        <?= $bill->domain ?>
                    <? } elseif ($bill->type == \model\Bill::TYPE_DOMAIN_PROLONG) { ?>
                        <?= $bill->domain ?>
                    <? }
                    else { ?>
                    ---
                </td>
                <? } ?>
                </td>



                <td><?= $currency->displayPrice($bill->price) ?> </td>
                <td><?= $currency->displayPrice($bill->total) ?> </td>
                <td><?= date('d-m-Y', strtotime($bill->date) + (86400 * 2)) ?></td>
                <td>
                    <? if($bill->is_paid == 1){ ?>
                        <span class="label label-success"><?=$_->l('Оплачен')?></span>
                    <? } elseif($bill->is_paid == -1){?>
                        <span class="label label-danger"><?=$_->l('Отменен')?></span>
                    <? } elseif ($bill->is_paid == -2) { ?>
                        <span class="label label-info"><?= $_->l('Возврат') ?></span>
                    <? } else { ?>
                        <span class="label label-warning"><?=$_->l('Ожидает оплату')?></span>
                    <? } ?>
                </td>

                <td class="text-center">
                    <? if (!$bill->is_paid) { ?>
                        <a href="<?= $_->link('admin/bill/pay/' . $bill->id) ?>"
                           class="btn btn-primary btn-xs ajax-action"><span
                                class="glyphicon glyphicon-shopping-cart"></span> <?=$_->l('Ручная оплата')?></a>
                        <a href="<?= $_->link('admin/bill/off/' . $bill->id) ?>"
                           class="btn btn-danger btn-xs ajax-action"><span
                                class="glyphicon glyphicon-remove"></span> <?=$_->l('Отменить')?></a>

                    <? } ?>
                    <? if ($bill->total > 0 && $bill->is_paid == 1 && $bill->type != \model\Bill::TYPE_BALANCE) { ?>
                        <a href="<?= $_->link('admin/bill/refund/' . $bill->id) ?>"
                           class="btn btn-info btn-xs ajax-action"><span
                                class="glyphicon glyphicon glyphicon-repeat"></span> <?= $_->l('Возврат') ?></a>
                    <? } ?>
                    <a href="<?= $_->link('admin/bill/remove/' . $bill->id) ?>"
                       class="btn btn-danger btn-xs ajax-action"><span
                            class="glyphicon glyphicon-remove"></span> <?=$_->l('Удалить')?></a>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
