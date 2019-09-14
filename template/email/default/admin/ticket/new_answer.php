{s}<?=$_->l('К тикету получен новй ответ')?>{/s}
<?=$_->l('Получен новый ответ к тикету.')?> <br>
<?=$_->l('Посмотреть ответ можно по ссылке:')?>
<br>
<a href="<?= \System\Tools::link('admin/ticket/' . $ticket->id); ?>"><?= \System\Tools::link('admin/ticket/' . $ticket->id); ?></a>