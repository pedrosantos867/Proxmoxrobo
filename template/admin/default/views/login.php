<?= $_->css('login.css') ?>
<div class="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <section class="login-form">
                <? if (\System\Tools::rGET('send_code')) { ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-ok"></span>
                        <?=$_->l('На Ваш email отправлено сообщение с инструкцией по восстановлению пароля.')?>
                    </div>
                <? } else if (\System\Tools::rGET('send')) { ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-ok"></span>
                        <?=$_->l('На Ваш email отправлено сообщение с новым паролем.')?>
                    </div>
                <? } else if($error == 'login_error'){ ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-remove-sign"></span>
                        <?=$_->l('Логин или пароль введен не верно!')?>
                    </div>
                <? } ?>
                <form method="post" action="#" role="login">
                    <img src="<?= $_->path('img/logo.png') ?>" class="img-responsive" alt=""/>
                    <input type="text" name="username" placeholder="<?=$_->l('Логин')?>" required
                           class="form-control input-lg" value=""/>

                    <input type="password" name="pass" class="form-control input-lg"
                           id="pass" placeholder="<?=$_->l('Пароль')?>" required=""/>

                    <button type="submit" name="go" class="btn btn-lg btn-primary btn-block"><?=$_->l('Войти')?></button>
                    <div>
                        <a href="<?= $_->link('admin/reminder') ?>"><?=$_->l('Забыли пароль ?')?></a>
                    </div>

                </form>
                <div class="form-links">
                    <span class="login-text">Powered by</span> <a href="http://hopebilling.com/">www.hopebilling.com</a>
                </div>
            </section>
        </div>
    </div>
</div>
