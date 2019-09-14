<div class="ajax-block">
    <div class="top-menu">
        <a href="<?= $_->link('admin/ticket/new') ?>" class="btn btn-primary"><span
                class="glyphicon glyphicon-plus-sign"></span> <?=$_->l('Создать тикет')?></a>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>№</th>
            <th><?=$_->l('Клиент')?></th>
            <th><?=$_->l('Тема')?></th>
            <th><?=$_->l('Состояние')?></th>
            <th><?=$_->l('Приоритет')?></th>
            <th><?=$_->l('Дата')?></th>

            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($tickets) == 0) { ?>
            <tr>
                <td colspan="11"><?=$_->l('Результаты не найдены')?>.</td>
            </tr>
        <? } ?>
        <? foreach ($tickets as $ticket) { ?>
            <tr>
                <th scope="row"><?= $ticket->id ?></th>
                <td><?= $ticket->user ?></td>
                <td><?= $ticket->subject ?></td>

                <td><?= $ticket->status == -1 ? '<span class="label label-success">Новый</span>' : ($ticket->status == 0 ? '<span class="label label-warning">В обработке</span>' : ($ticket->status == 1 ? '<span class="label label-danger">Закрыт</span>' : '')) ?>
                    &nbsp;&nbsp;&nbsp;<?= $ticket->count_new ? '<span class="label label-info">' . $ticket->count_new . '</span>' : '' ?></td>
                <td><?= $ticket->priority == 0 ? '<span class="label label-danger">Низкий</span>' : ($ticket->priority == 1 ? '<span class="label label-warning">Средний</span>' : ($ticket->priority == 2 ? '<span class="label label-success">Высокий</span>' : '')) ?></td>
                <td><?= $ticket->date ?></td>
                <td class="text-center">
                    <a href="<?= $_->link('admin/ticket/' . $ticket->id) ?>" class="btn btn-info btn-xs"><span
                            class="glyphicon glyphicon-eye-open"></span> <?=$_->l('Просмотр')?> </a>
                    <? if ($ticket->status != 1) { ?>
                        <a href="<?= $_->link('admin/ticket/close/' . $ticket->id) ?>"
                           class="btn btn-danger btn-xs ajax-action"><span
                                class="glyphicon glyphicon-remove-sign"></span> <?=$_->l('Закрыть')?> </a>
                    <? } ?>
                    <a href="<?= $_->link('admin/ticket/remove/' . $ticket->id) ?>"
                       class="btn btn-danger btn-xs ajax-action"><span
                            class="glyphicon glyphicon-remove-sign"></span> <?=$_->l('Удалить')?> </a>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?= $pagination ?>
</div>
