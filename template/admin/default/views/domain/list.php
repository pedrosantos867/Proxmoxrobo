<div class="ajax-block">
    <?= $_->js('jquery-ui.js') ?>
    <?= $_->js('dragtable.js') ?>


    <div>
        <a href="<?= $_->link('admin/domain/add') ?>" class="btn btn-default ajax-modal"><span
                class="glyphicon glyphicon-ok"
                aria-hidden="true"></span><?=$_->l('Добавить')?></a>


        <table class="table table-bordered dragable">
            <thead>
            <tr>
                <th style="cursor: move"><span class="glyphicon glyphicon-move"></span></th>
                <th width="8%">№
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="id" data-field="id"
                               value="<?= isset($filter['id']) ? $filter['id'] : '' ?>">
                    </div>
                </th>

                <th><?=$_->l('Зона')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="name" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="name" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" data-type="equal" name="name"
                               value="<?= isset($filter['name']) ? $filter['name'] : '' ?>">
                    </div>
                </th>
                <th width="10%"><?=$_->l('Цена регистрации')?></th>
                <th width="10%"><?=$_->l('Цена продления')?></th>
                <th width="20%"><?=$_->l('Минимальный срок | Максимальный срок')?></th>
                <th><?=$_->l('Регистратор')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($domains) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($domains as $domain) { ?>
                <tr data-id="<?= $domain->id ?>">
                    <th style="cursor: move"><span class="glyphicon glyphicon-move"></span></th>
                    <th scope="row"><?= $domain->id ?></th>
                    <td><?= $domain->name ?></td>
                    <td><?= $currency->displayPrice($domain->price) ?> </td>
                    <td><?= $currency->displayPrice($domain->extension_price) ?> </td>
                    <td><?= $domain->min_period ?> | <?= $domain->max_period ?></td>
                    <td>
                        <?= $domain->registrar ?>

                    </td>
                    <td>
                        <a href="<?= $_->link('admin/domain/edit/' . $domain->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"><span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Редактировать')?></a>
                        <a href="<?= $_->link('admin/domain/remove/' . $domain->id) ?>"
                           class="btn btn-xs btn-danger ajax-action"><span class="glyphicon glyphicon-trash"
                                                                           aria-hidden="true"></span><?=$_->l('Удалить')?></a>

                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>


    </div>
    <?= $pagination ?>

</div>