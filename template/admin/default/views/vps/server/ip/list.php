<div class="ajax-block">
    <a href="<?= $_->link('admin/vps-servers') ?>" class="btn btn-warning"><span
            class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span><?=$_->l('Список серверов')?></a>

    <a href="<?= $_->link('admin/vps-ips/edit?server_id='.$_->rget('server_id')) ?>" class="btn btn-default ajax-modal"><span
            class="glyphicon glyphicon-plus" aria-hidden="true"></span><?=$_->l('Добавить')?></a>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>№</th>
            <th><?= $_->l('Тип') ?></th>
            <th><?=$_->l('VLAN')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="vlan" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="vlan" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>

                    <input type="text" data-type="like" name="vlan" class=" filter" data-field="vlan"
                           value="<?= isset($filter['vlan']) ? $filter['vlan'] : '' ?>">

                </div>
            </th>
            <th><?=$_->l('IP Адрес')?>
                <div class="sorting">
                    <a href="#" class="order" data-field="ip" data-type="desc"><span
                            class="glyphicon glyphicon-chevron-up"></span></a>
                    <a href="#" class="order" data-field="ip" data-type="asc"><span
                            class="glyphicon glyphicon-chevron-down"></span></a>
                </div>
                <div>

                    <input type="text" data-type="like" name="ip" class=" filter" data-field="ip"
                           value="<?= isset($filter['ip']) ? $filter['ip'] : '' ?>">

                </div>
            </th>

            <th></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($ips) == 0) { ?>
            <tr>
                <td colspan="5"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <? foreach ($ips as $ip) { ?>
            <tr <?=($ip->used == 1) ? 'class="danger"' : ''?>>
                <th scope="row"><?= $ip->id ?></th>
                <td>
                    <? if ($ip->type == 0) { ?>
                        VLAN
                    <? } ?>

                    <? if ($ip->type == 1) { ?>
                        Static IPv4
                    <? } ?>

                    <? if ($ip->type == 2) { ?>
                        NAT Static IPv4
                    <? } ?>
                    <? if ($ip->type == 3) { ?>
                        Private Static IPv4
                    <? } ?>
                    <? if ($ip->type == 4) { ?>
                        Public Static IPv4
                    <? } ?>
                </td>
                <td>
                    <? if ($ip->type == 0) { ?>
                        <?= $ip->vlan ?>
                    <? } else { ?>
                        ---
                    <? } ?>

                </td>
                <td>
                    <? if ($ip->type == 0) { ?>
                        ---
                    <? } else { ?>
                        <?= $ip->ip ?>
                    <? } ?>
                </td>

                <td>
                    <?if($ip->used != 1){?>
                    <a class="btn btn-default btn-xs ajax-modal"
                       href="<?= $_->link('admin/vps-ips/edit?ip_id=' . $ip->id) ?>"><span class="glyphicon glyphicon-cog"
                                                                                   aria-hidden="true"></span><?=$_->l('Редактировать')?></a>

                    <a class="btn btn-danger btn-xs ajax-action"
                       href="<?= $_->link('admin/vps-ips/remove?ip_id=' . $ip->id) ?>"
                       data-confirm="<?=$_->l('Вы уверенны что хотите удалить этот IP? ')?>">
                    <span
                        class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    <?} else {?>
                    <span class="label label-danger"> <?=$_->l('IP-адрес используется')?> </span>
                    <? } ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>