<div class="ajax-block">

    <div class="top-menu">
        <a href="<?= $_->link('admin/services/edit') ?>" class="btn btn-default ajax-modal">
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
                <th width="20%"><?=$_->l('Категория')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="category_id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="category_id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <select name="category_id" class="filter" data-field="category">
                                <option value=""> --- </option>
                            <?foreach($categories as $category){?>
                                <option <?= isset($filter['category_id']) && $filter['category_id'] == $category->id ? 'selected="selected"' : '' ?> value="<?=$category->id?>"><?=$category->name?></option>
                            <?}?>
                        </select>


                    </div>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($services) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($services as $service) { ?>
                <tr data-id="<?= $service->id ?>">

                    <th scope="row"><?= $service->id ?></th>
                    <td><?= $service->name ?></td>

                    <td><?= $service->category ?></td>
                    <td>
                        <a class="btn btn-default btn-xs ajax-modal" href="<?= $_->link('admin/services/edit?id_service=' . $service->id) ?>"> <span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>

                        <a class="btn btn-danger btn-xs ajax-action"
                           data-confirm="<?=$_->l('Продолжить удаление?')?>"
                           href="<?= $_->link('admin/services/remove?id_service=' . $service->id) ?>"><span
                                class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    </td>

                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>
