<div class="ajax-block">

    <div class="top-menu">
    <a class="btn btn-success  ajax-modal" href="<?=$_->link('domain-owner/add')?>" >
        <span class="glyphicon glyphicon-plus-sign"></span> <?=$_->l('Добавить')?>
    </a>
    </div>
    <table class="table">
        <tr>
            <th>ID</th>
            <th><?=$_->l('ФИО')?></th>
            <th></th>
        </tr>

        <? foreach ($owners as $owner) { ?>
            <tr>
                <td><?= $owner->id ?></td>
                <td><?= $owner->fio ?></td>

                <td>
                    <a href="<?= $_->link('domain-owner/add?owner_id=' . $owner->id) ?>"
                       class="btn btn-primary btn-xs ajax-modal"><span class="glyphicon glyphicon-cog"></span> <?=$_->l('Редактировать')?></a>
                    <a href="<?= $_->link('setting/domain-owner/copy?owner_id=' . $owner->id) ?>"
                       class="btn btn-primary btn-xs ajax-action"><span class="glyphicon glyphicon-copy"></span> <?=$_->l('Копировать')?></a>

                    <a href="<?= $_->link('setting/domain-owner/remove?owner_id=' . $owner->id) ?>"
                       class="btn btn-danger btn-xs ajax-action"><span class="glyphicon glyphicon-trash"></span> <?=$_->l('Удалить')?></a></td>
            </tr>
        <? } ?>
    </table>
</div>