<div class="ajax-block">


    <div class="top-menu">
        <a href="<?=$_->link('admin/modules/billmanagervps/plans/edit')?>" class="btn btn-default ajax-modal"><span class="glyphicon glyphicon-ok"
                                                                                  aria-hidden="true"></span><?=$_->l('Добавить тариф')?>
        </a>
    </div>

    <div>
        <table class="table table-bordered dragable" data-id="plans">
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
                <tr>

                    <th scope="row"><?= $plan->id ?></th>

                    <td><?= $plan->name ?></td>
                    <td><?= $dcurrency->displayPrice($plan->price) ?></td>

                    <td>
                        <a class="btn btn-default btn-xs ajax-modal" href="<?= $_->link('admin/modules/billmanagervps/plans/edit?plan_id=' . $plan->id) ?>"> <span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>


                        <a class="btn btn-danger btn-xs ajax-action"
                           onclick="return confirm('Удаление тарифного плана приведет к удалению всех заказов оформленных на данный тариф! Продолжить удаление?');"
                           href="<?= $_->link('admin/modules/billmanagervps/plans/remove?plan_id=' . $plan->id) ?>"><span
                                class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>


                    </td>

                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>
