<div class="ajax-block">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>№</th>
                <th><?=$_->l('ID')?>
                </th>
                <th><?=$_->l('Name')?></th>
                <th><?=$_->l('Address')?></th>
                <th><?=$_->l('Retention')?></th>
                <th><?=$_->l('Datastore')?></th>
                <!--
                <th></th> 
                <th></th>
                -->
            </tr>
        </thead>
        <tbody>

            <? if (count($backupServers) == 0) { ?>
            <tr>
                <td colspan="100%"><?=$_->l('No results found.')?></td>
            </tr>
            <? } ?>

            <? foreach ($backupServers as $backupServer) { ?>
            <tr>
                <th scope="row"><?= $backupServer->id ?></th>
                <td><?= $backupServer->id ?></td>
                <td><?= $backupServer->name ?></td>
                <td><?= $backupServer->address ?></td>
                <td><?= $backupServer->retention ?></td>
                <td><?= $backupServer->datastore ?></td>

                <?php /*
                <td><a class="btn btn-warning btn-xs ajax-action"
                        href="<?= $_->link('admin/vps-servers/check?server_id=' . $server->id) ?>">
                        <span class="glyphicon glyphicon-transfer"></span> &nbsp;
                        <?=$_->l('Check connection')?></a></td>
                <td>
                    <a class="btn btn-default btn-xs ajax-modal"
                        href="<?= $_->link('admin/vps-servers/edit?server_id=' . $server->id) ?>"><span
                            class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Edit')?></a>
                    <a class="btn btn-info btn-xs"
                        href="<?= $_->link('admin/vps-ips?server_id=' . $server->id) ?>"><span
                            class="glyphicon glyphicon-menu-hamburger"
                            aria-hidden="true"></span><?=$_->l('IP address')?></a>

                    <a class="btn btn-danger btn-xs ajax-action"
                        href="<?= $_->link('admin/vps-servers/remove?server_id=' . $server->id) ?>"
                        data-confirm="<?=$_->l('Are you sure you want to delete this server? Deleting a server will delete all orders associated with this server!')?>">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Delete')?></a>
                </td>

                */?>
            </tr>
            <? } ?>
        </tbody>
    </table>



</div>