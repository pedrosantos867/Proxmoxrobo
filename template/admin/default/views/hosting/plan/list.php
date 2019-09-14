<div class="ajax-block">
    <?= $_->js('jquery-ui.js') ?>
    <?= $_->js('dragtable.js') ?>

    <div class="top-menu">
        <a href="<?= $_->link('admin/plan/add') ?>" class="btn btn-default"><span class="glyphicon glyphicon-ok"
                                                                                  aria-hidden="true"></span><?=$_->l('Добавить тариф')?>
            </a>
        <a href="<?= $_->link('admin/plan/params') ?>" class="btn btn-default"><span class="glyphicon glyphicon-list"
                                                                                     aria-hidden="true"></span><?=$_->l('Опции тарифа')?>
            </a>
        <a href="<?= $_->link('admin/servers') ?>" class="btn btn-default"><span class="glyphicon glyphicon-hdd"></span><?=$_->l('Сервера')?></a>
    </div>

    <div>
        <table class="table table-bordered dragable" data-id="plans">
            <thead>
            <tr>
                <th width="2%">
                    <div class="sorting">
                        <a href="#" class="order" data-field="sort_position" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="sort_position" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                </th>
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
                <th width="40%"><?=$_->l('Название')?>
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
                <th width="10%"><?=$_->l('Цена')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($plans) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($plans as $plan) { ?>
                <tr <?=$plan->hidden ? 'class="row_hidden"' : ''?> data-id="<?= $plan->id ?>">
                    <th style="cursor: move"><span class="glyphicon glyphicon-move"></span></th>
                    <th scope="row"><?= $plan->id ?></th>
                    <td><?= $plan->name ?></td>
                    <td><?= $dcurrency->displayPrice($plan->price) ?></td>

                    <td>
                        <a class="btn btn-default btn-xs" href="<?= $_->link('admin/plan/' . $plan->id) ?>"> <span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>


                        <a class="btn btn-danger btn-xs"
                           onclick="return confirm('Удаление тарифного плана приведет к удалению всех заказов оформленных на данный тариф! Продолжить удаление?');"
                           href="<?= $_->link('admin/plan/remove/' . $plan->id) ?>"><span
                                class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>

                        <?if($plan->hidden){?>
                            <button class="btn btn-default btn-xs show_row">&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>
                        <?} else {?>
                            <button class="btn btn-default btn-xs hide_row">&nbsp;<span class="glyphicon glyphicon-eye-close"></span></button>
                        <?} ?>
                    </td>

                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>
