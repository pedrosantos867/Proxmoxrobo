{s}New VPS order{/s}
Доступ к серверу для администратора:
<br>
<?= ($order->ip); ?>
<br>
root
<br>
<?= ($order->password); ?>
<br>
<br>
VMmanager — панель управления виртуальным контейнером:
<br>
<a href="<?= $plan->link ?>" target="_blank"><?= $plan->link ?></a>
<br>
Вход в панель: <?= ($order->username); ?>
<br>
Пароль: <?= ($order->userpassword); ?>
