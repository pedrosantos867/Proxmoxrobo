<?= $_->css('reminder.css') ?>
<div class="container">
    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <section class="login-form">
                <form method="post" action="#" role="login">
                    <?if($config->enable_lang_switcher_for_client && count($languages) > 1){?>
                        <ul class="lang">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">
                                    <img src="<?=$_->link('storage/i18n/flags/'.$lang->iso_code.'.png')?>" height="23px">
                                    <span class="caret"></span></a>

                                <ul class="dropdown-menu" role="menu">

                                    <? foreach ($languages as $l) { ?>
                                        <li>
                                            <a href="<?= $_->link($request, 'lang='.$l->id) ?>">
                                                <img src="<?=$_->link('storage/i18n/flags/'.$l->iso_code.'.png')?>" height="23px"> <?=$l->name?>
                                            </a>
                                        </li>
                                    <? } ?>
                                </ul>
                            </li>
                        </ul>
                    <?}?>
                    <img src="<?= $_->path('img/logo.png') ?>" class="img-responsive" alt=""/>
                    <input type="text" name="username" placeholder="<?=$_->l('Логин')?>" required
                           class="form-control input-lg" value=""/>


                    <button type="submit" name="go" class="btn btn-lg btn-primary btn-block"><?=$_->l('Восстановить пароль')?>
                    </button>
                    <div>
                        <a href="<?= $_->link('login') ?>"><?=$_->l('Страница входа')?></a>
                    </div>

                </form>

                <div class="form-links">
                    <span class="login-text">Powered by</span> <br> <a href="http://hopebilling.com/">www.hopebilling.com</a>
                </div>

            </section>
        </div>

    </div>
</div>