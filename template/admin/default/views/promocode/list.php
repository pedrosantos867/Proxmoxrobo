<div class="ajax-block">

    <div class="top-menu">
        <a href="<?= $_->link('admin/promocodes/edit') ?>" class="btn btn-default ajax-modal">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?>
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?=$_->l('Имя')?></th>
                <th><?=$_->l('Код')?></th>
                <th><?=$_->l('Скидка')?></th>
                <th><?=$_->l('Тип скидки')?></th>
                <th><?=$_->l('Количество')?></th>
                <th><?=$_->l('Использовано')?></th>
                <th><?=$_->l('Дата окончания')?></th>
                <th><?=$_->l('Действие')?></th>
            </tr>
        </thead>
        <tbody>
        <? if (count($promocodes) == 0) { ?>
            <tr>
                <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
            </tr>
        <? } ?>
        <?foreach ($promocodes as $promocode):?>
            <tr>
                <td><?=$promocode->name?></td>
                <td><?=$promocode->code?></td>
                <td><?=$promocode->sale?></td>
                <td><?= $promocode->sale_type ? $_->l('Процент') : $_->l('Сумма') ?></td>
                <td><?=$promocode->total_count?></td>
                <td><?=$promocode->used_count?></td>
                <td><?=$promocode->end_date?></td>
                <td>
                    <a class="btn btn-default btn-xs ajax-modal" href="<?= $_->link('admin/promocodes/edit?promocode_id=' . $promocode->id) ?>"> <span
                            class="glyphicon glyphicon-cog" aria-hidden="true"></span> <?=$_->l('Редактировать')?></a>

                    <a class="btn btn-danger btn-xs ajax-action"
                       data-confirm="<?=$_->l('Продолжить удаление?')?>"
                       href="<?= $_->link('admin/promocodes/remove?promocode_id=' . $promocode->id) ?>"><span
                            class="glyphicon glyphicon-trash" aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                </td>
            </tr>
        <?endforeach;?>
        </tbody>
    </table>

    <?= $pagination ?>
</div>