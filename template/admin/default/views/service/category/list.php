<div class="ajax-block">
    <?= $_->js('jquery-ui.js') ?>
    <?= $_->js('dragtable.js') ?>
    <div class="top-menu">
        <a href="<?= $_->link('admin/service-categories/edit') ?>" class="btn btn-default ajax-modal">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?>
          </a>
    </div>

    <div>
        <table class="table table-bordered dragable" data-id="categories">
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

                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($categories) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($categories as $category) { ?>
                <tr data-id="<?= $category->id ?>">
                    <th style="cursor: move"><span class="glyphicon glyphicon-move"></span></th>
                    <th scope="row"><?= $category->id ?></th>
                    <td><?= $category->name ?></td>


                    <td>
                        <a class="btn btn-default btn-xs ajax-modal" href="<?= $_->link('admin/service-categories/edit?id_category=' . $category->id) ?>"> <span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>

                        <a class="btn btn-danger btn-xs ajax-action"
                           data-confirm="<?=$_->l('Продолжить удаление?')?>"
                           href="<?= $_->link('admin/service-categories/remove?id_category=' . $category->id) ?>"><span
                                class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    </td>

                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>
