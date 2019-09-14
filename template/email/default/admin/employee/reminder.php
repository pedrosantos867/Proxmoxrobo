{s}<?=$_->l('Восстановление пароля')?>{/s}
<?=$_->l('Для смены пароля перейдите по ссылке:')?>
<br>
<a href="<?= $_->link('admin/reminder/code/' . $code) ?>">
    <?= $_->link('admin/reminder/code/' . $code) ?>
</a>

