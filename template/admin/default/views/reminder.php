<?= $_->css('reminder.css') ?>
<div class="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <section class="login-form">
                <form method="post" action="#" role="login">
                    <img src="<?= $_->path('img/logo.png') ?>" class="img-responsive" alt=""/>
                    <input type="text" name="username" placeholder="<?=$_->l('Логин')?>" required
                           class="form-control input-lg" value=""/>


                    <button type="submit" name="go" class="btn btn-lg btn-primary btn-block"><?=$_->l('Восстановить пароль')?>
                    </button>
                    <div>
                        <a href="<?= $_->link('admin/login') ?>"><?=$_->l('Страница входа')?></a>
                    </div>

                </form>

                <div class="form-links">
                    <span class="login-text">Powered by</span> <a href="http://hopebilling.com/">www.hopebilling.com</a>
                </div>

            </section>
        </div>

    </div>
</div>


