<div class="ajax-block">
    <a href="<?= $_->link('admin/vps-servers/edit') ?>" class="btn btn-default ajax-modal"><span
            class="glyphicon glyphicon-plus" aria-hidden="true"></span><?=$_->l('Добавить')?></a>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>№</th>
            <th><?=$_->l('Название')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="id" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="id" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>

                    <input type="text" data-type="like" name="name" class=" filter" data-field="name"
                           value="<?= isset($filter['name']) ? $filter['name'] : '' ?>">

                </div>
            </th>
            <th><?=$_->l('Адрес')?></th>
            <th><?=$_->l('Соединение')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($servers) == 0) { ?>
            <tr>
                <td colspan="5"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <? foreach ($servers as $server) { ?>
            <tr <?=$server->hidden ? 'class="row_hidden"' : ''?> data-id="<?= $server->id ?>">
                <th scope="row"><?= $server->id ?></th>
                <td><?= $server->name ?></td>
                <td><?= $server->host ?></td>
                <td><a class="btn btn-warning btn-xs ajax-action" href="<?= $_->link('admin/vps-servers/check?server_id=' . $server->id) ?>">
                        <span class="glyphicon glyphicon-transfer"></span> &nbsp;
                        <?=$_->l('Проверить соединение')?></a></td>
                <td>
                    <a class="btn btn-default btn-xs ajax-modal"
                       href="<?= $_->link('admin/vps-servers/edit?server_id=' . $server->id) ?>"><span class="glyphicon glyphicon-cog"
                                                                                   aria-hidden="true"></span><?=$_->l('Редактировать')?></a>
                    <a class="btn btn-info btn-xs"
                       href="<?= $_->link('admin/vps-ips?server_id=' . $server->id) ?>"><span class="glyphicon glyphicon-menu-hamburger"
                                                                                                       aria-hidden="true"></span><?=$_->l('IP адреса')?></a>

                    <a class="btn btn-danger btn-xs ajax-action"
                       href="<?= $_->link('admin/vps-servers/remove?server_id=' . $server->id) ?>"
                       data-confirm="<?=$_->l('Вы уверенны что хотите удалить этот сервер? Удаление сервера приведет к удалению всех заказов связанных с данным сервером!')?>">
                    <span
                        class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    <?if($server->hidden){?>
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