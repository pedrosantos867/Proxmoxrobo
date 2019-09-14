<div class="ajax-block">


    <div>
        <a href="<?= $_->link('admin/domain-registrars/add') ?>" class="btn btn-default ajax-modal"><span
                class="glyphicon glyphicon-ok"
                aria-hidden="true"></span><?=$_->l('Добавить')?></a>


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
                        <input type="text" class="form-control filter" name="id" data-field="id"
                               value="<?= isset($filter['id']) ? $filter['id'] : '' ?>">
                    </div>
                </th>


                <th width="10%"><?=$_->l('Название')?></th>
                <th width="10%"><?=$_->l('Тип')?></th>

                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($registrars) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($registrars as $registrar) { ?>
                <tr>
                    <th scope="row"><?= $registrar->id ?></th>
                    <td><?= $registrar->name ?></td>
                    <td>

                        <?foreach ($types as $key => $name){?>
                            <?= $registrar->type == $key ? $name : '' ?>
                        <?}?>
                    </td>
                    <td>
                        <a href="<?= $_->link('admin/domain-registrars/' . $registrar->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"><span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Изменить')?></a>
                        <a href="<?= $_->link('admin/domain-registrars/remove/' . $registrar->id) ?>"
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