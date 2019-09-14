{s}<?=$_->l('Получен тикет')?>{/s}


<?=$_->l('Получено новое обращение в службу поддержки № %ticket.', array('ticket' => $ticket->id ))?>
<p><?=$_->l('Тема: ')?> <?=$ticket->subject?></p>
<p><?=$_->l('Сообщение: ')?> <?=$ticket->message?></p>
<br>
<a href="<?= \System\Tools::link('admin/ticket/' . $ticket->id); ?>">Открыть тикет в биллинг системе</a>