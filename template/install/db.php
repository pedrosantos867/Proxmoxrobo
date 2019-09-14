<div class="row" style="margin-top: 10px; padding: 10px">
    <div class="col-md-12">
        <?php if (isset($error) && $error == 'no_connection') { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <?=$_->l('Нет соединения с базой даных!')?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-group">
                <label for="server"><?=$_->l('Сервер')?></label>
                <input type="text" class="form-control" name="server" id="server" placeholder="localhost"
                       value="<?php echo $config->db_host ?>">
            </div>
            <div class="form-group">
                <label for="username"><?=$_->l('Имя пользователя')?></label>
                <input type="text" class="form-control" name="username" id="username" placeholder="<?=$_->l('Имя пользователя')?>"
                       value="<?php echo $config->db_username ?>">
            </div>
            <div class="form-group">
                <label for="password"><?=$_->l('Пароль')?></label>
                <input type="text" class="form-control" name="password" id="password" placeholder="<?=$_->l('Пароль')?>"
                       value="<?php echo $config->db_pass ?>">
            </div>
            <div class="form-group">
                <label for="dbname"><?=$_->l('Название базы данных')?></label>
                <input type="text" class="form-control" name="dbname" id="dbname" placeholder="<?=$_->l('Название базы даных')?>"
                       value="<?php echo $config->db_name ?>">
            </div>


            <button type="submit" class="btn btn-primary pull-right"><?=$_->l('Далее')?> <span
                    class="glyphicon glyphicon-chevron-right"></span></button>
        </form>
    </div>
</div>