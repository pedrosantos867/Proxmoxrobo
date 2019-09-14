<div class="ajax-block">
    <div class="top-menu">
        <a href="<?= $_->link('admin/vps-params/edit') ?>" class="btn btn-default ajax-modal">
            <span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            <?=$_->l('Добавить')?></a>
    </div>
    <div>
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
                <th><?=$_->l('Название')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>

                        <input type="text" name="name" class=" filter" data-field="name"
                               value="<?= isset($filter['name']) ? $filter['name'] : '' ?>">

                    </div>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($properties) == 0) { ?>
                <tr>
                    <td colspan="5"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($properties as $property) { ?>
                <tr>
                    <th scope="row"><?= $property->id ?></th>

                    <td><?= $property->name ?></td>

                    <td>
                        <a href="<?= $_->link('admin/vps-params/edit?id_param=' . $property->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"><?=$_->l('Изменить')?></a>
                        <a href="<?= $_->link('admin/vps-params/remove?id_param=' . $property->id) ?>"
                           class="btn btn-xs btn-danger ajax-action"><?=$_->l('Удалить')?></a>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <?= $pagination ?>
</div>
