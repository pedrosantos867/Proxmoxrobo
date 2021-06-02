<div class="ajax-block">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th rowspan="2"><?=$_->l('ID')?></th>
                <th rowspan="2"><?=$_->l('Backup Server')?></th>
                <th rowspan="2"><?=$_->l('VPS ID')?></th>
                <th rowspan="2"><?=$_->l('Client name')?></th>
                <th colspan="7"><?=$_->l('Day of the week')?></th>
                <th rowspan="2"><?=$_->l('Time of the day')?></th>
            </tr>
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wen</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tbody>  
            <? if (count($backupOrders) == 0) { ?>
            <tr>
                <td colspan="100%"><?=$_->l('No results found.')?></td>
            </tr>
            <? } ?>

            <? foreach ($backupOrders as $backupOrder) { ?>
            <tr>
                <th scope="row"><?= $backupOrder->id ?></th>
                <td><?= $backupOrder->storage ?></td>
                <td><?= $backupOrder->vmid ?></td>
                <td><a href="<?= $_->link('admin/client/info/' . $backupOrder->client_id) ?>"
                        class="ajax-modal"><?= $backupOrder->name ?></a></td>

                <? if($backupOrder->sunday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->monday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->tuesday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->wednesday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->thursday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->friday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>>
                <? }else{?>
                <td></td>
                <?}?>
                <? if($backupOrder->saturday){ ?>
                    <td>
                    <p class="glyphicon glyphicon-ok dow" style="color:green"></p>
                </td>
                <? }else{?>
                <td></td>
                <?}?>

                <td><?= date ('H:i', strtotime($backupOrder->time)) ?>h</td>
            </tr>
            <? } ?>


        </tbody>
    </table>
    <style>
    dow,
    th,
    td {
        text-align: center;
    }
    </style>
</div>