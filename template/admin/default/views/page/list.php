<div class="ajax-block">

    <div class="top-menu">
        <a href="<?= $_->link('admin/pages/edit') ?>" class="btn btn-default ajax-modal">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?>
        </a>
    </div>

    <div>
        <table class="table table-bordered dragable" data-id="categories">
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

                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($pages) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($pages as $page) { ?>
                <tr data-id="<?= $page->id ?>">

                    <th scope="row"><?= $page->id ?></th>
                    <td><?= $page->name ?></td>


                    <td>
                        <a class="btn btn-default btn-xs ajax-modal" href="<?= $_->link('admin/pages/edit?id_page=' . $page->id) ?>"> <span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>

                        <a class="btn btn-danger btn-xs ajax-action"
                           data-confirm="Продолжить удаление?"
                           href="<?= $_->link('admin/pages/remove?id_page=' . $page->id) ?>"><span
                                class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    </td>

                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>

