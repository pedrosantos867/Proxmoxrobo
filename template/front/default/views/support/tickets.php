<div class="ajax-block">
    <div class="top-menu">
        <a href="<?= $_->link('support/ticket/new') ?>" class="btn btn-primary"><span
                class="glyphicon glyphicon-plus-sign"></span> <?= $_->l('Создать обращение в службу поддержки') ?></a>
    </div>
<div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th><?= $_->l('№') ?></th>
            <th><?= $_->l('Тема') ?></th>
            <th><?= $_->l('Состояние') ?></th>
            <th><?= $_->l('Приоритет') ?></th>
            <th><?= $_->l('Дата') ?></th>

            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <? if (count($tickets) == 0) { ?>
            <tr>
                <td colspan="11"><?= $_->l('Результаты не найдены.') ?></td>
            </tr>
        <? } ?>
        <? foreach ($tickets as $ticket) { ?>
            <tr>
                <th scope="row"><?= $ticket->id ?></th>
                <td><?= $ticket->subject ?></td>
                <td>
                    <? if ($ticket->status == -1) { ?>
                        <span class="label label-success"><?= $_->l('Новый') ?></span>
                    <? } elseif ($ticket->status == 0) { ?>
                        <span class="label label-warning"><?= $_->l('В обработке') ?></span>
                    <? } elseif ($ticket->status == 1) { ?>
                        <span class="label label-danger"><?= $_->l('Закрыт') ?></span>
                    <? } ?>
                    &nbsp;&nbsp;&nbsp;
                    <?= $ticket->count_new ? '<span class="label label-info">' . $ticket->count_new . '</span>' : '' ?>
                </td>
                <td>
                    <? if ($ticket->priority == 0) { ?>
                        <span class="label label-danger"><?= $_->l('Низкий') ?></span>
                    <? } elseif ($ticket->priority == 1) { ?>
                        <span class="label label-warning"><?= $_->l('Средний') ?></span>
                    <? } elseif ($ticket->priority == 2) { ?>
                        <span class="label label-success"><?= $_->l('Высокий') ?></span>
                    <? } ?>
                </td>
                <td><?= $ticket->date ?></td>

                <td class="text-center">
                    <a href="<?= $_->link('support/ticket/show?ticket_id=' . $ticket->id) ?>" class="btn btn-info btn-xs"><span
                            class="glyphicon glyphicon-eye-open"></span> <?= $_->l('Просмотр тикета') ?></a>
                    <? if ($ticket->status != 1) { ?>
                        <a href="<?= $_->link('support/ticket/close?ticket_id=' . $ticket->id) ?>"
                           class="btn btn-danger btn-xs ajax-action"><span
                                class="glyphicon glyphicon-remove-sign "></span> <?= $_->l('Закрыть тикет') ?></a>
                    <? } ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>

<?= $pagination ?>
</div>
