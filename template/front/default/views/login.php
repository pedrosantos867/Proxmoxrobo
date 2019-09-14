<?= $_->css('login.css') ?>
<?= $_->JS('validator.js') ?>
<div class="container">
    <? if($config->enabled_sms_login){ ?>
    <div id="check-mobile" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title" id="mySmallModalLabel"><?= $_->l('Подтверждение входа') ?></h4>
                </div>
                <div class="modal-body">


                    <form class="code-confirmation">

                        <div class="form-group code-group">
                            <label for="code"><?= $_->l('Код из СМС') ?></label>
                            <input type="text" id="code" name="code" placeholder=""
                                   class="input-xlarge form-control" data-validate="required|ajax"
                                   data-validate-send-ajax="" data-validate-message-fail-ajax="<?= $_->l('Код введен неверно') ?>">
                        </div>
                        <div class="form-group code-group">
                            <button id="code-send" type="submit"
                                    class="btn btn-warning form-control"><?= $_->l('Подтвердить') ?></button>

                        </div>
                    </form>

                    <script>
                        $('form.code-confirmation').validate({messages: validate_messages});
                        $('form.code-confirmation').on('submit', function (e) {
                           // e.preventDefault();
                            $('#login-form input[name=code]').val($('form.code-confirmation input[name=code]').val());
                            $('#login-form').off('submit').submit();
                            return false;
                        })
                    </script>

                </div>
            </div>
        </div>
    </div>
    <?}?>

    <div class="row" id="pwd-container">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <section class="login-form">
                <? if (\System\Tools::rGET('reg')) { ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-ok"></span>
                        <?=$_->l('Благодарим за регистрацию в системе, на вашу почту отправлены данные для входа')?>
                    </div>
                <? } ?>
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
                <? } else if($error == 'phone_error'){ ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <span class="glyphicon glyphicon-remove-sign"></span>
                        <?=$_->l('Код из СМС введен не верно!')?>
                    </div>
                <? } ?>
                <form id="login-form" method="post" action="<?=$_->link('login')?><?=isset($back) ? '?back=' . $back : ''?>" role="login">

                    <?if($config->enable_lang_switcher_for_client  && count($languages) > 1){?>
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

                    <input type="password" name="pass" class="form-control input-lg"
                           id="pass" placeholder="<?=$_->l('Пароль')?>" required=""/>

                    <button type="submit" name="go" class="btn btn-lg btn-primary btn-block"><?=$_->l('Войти')?></button>
                    <?if($config->enable_social_auth){?>
                        <div class="social-reg">        
                            <?=$_->l('Регистрация через соц.сети')?>
                            <?if(is_array($socialAuthInfo->networks)){?>
                            <? $networks_providers = implode(',', array_slice($socialAuthInfo->networks, 0, 4));?>
                            <? $networks_hidden = implode(',', array_slice($socialAuthInfo->networks, 4));?>
                            <? } ?>
                            <script src="//ulogin.ru/js/ulogin.js"></script>
                            <div id="uLogin" data-ulogin="display=panel;theme=classic;fields=first_name,last_name,phone,email,nickname;verified_email=1;lang=<?=$lang->iso_code?>;providers=<?=$networks_providers?>;hidden=<?=$networks_hidden?>;redirect_uri=<?=$_->link('/social/auth' . $_->rGet('back'))?>;mobilebuttons=0;"></div>
                            <br>
                        </div>
                    <?}?>
                    <? if($config->enabled_sms_login){ ?>
                        <input type="hidden" name="code" value="">
                        <input type="hidden" name="id_conf" value="">

                        <script>

                            $('#login-form').on('submit', function (e) {
                                e.preventDefault();
                                $.ajax({
                                    data: {ajax:1, action: 'login', username: $('input[name="username"]').val(), pass: $('input[name="pass"]').val()},
                                    method: 'post',
                                    dataType: 'json',
                                    success : function (data) {
                                     if(data.ok){
                                         $('form#login-form input[name=id_conf]').val(data.id);
                                         $('.code-confirmation input[name=code]').data('validate-send-ajax', data.id);
                                         $('#check-mobile').modal({
                                             backdrop: 'static',
                                             keyboard: false
                                         });
                                     }
                                    }
                                });
                                return false;
                            })
                        </script>
                    <? } ?>

                    <div>
                        <a href="<?= $_->link('reminder') ?><?=isset($back) ? '?back=' . $back : ''?>"><?=$_->l('Забыли пароль ?')?></a>
                    </div>
                    <div>
                        <a href="<?= $_->link('reg')?><?= isset($back) ? '?back=' . $back : ''?>"><?=$_->l('Регистрация')?></a>
                    </div>
                </form>
                <div class="form-links">
                    <span class="login-text">Powered by</span> <br> <a href="http://hopebilling.com/">www.hopebilling.com</a>
                </div>
            </section>
        </div>
    </div>
</div>
<?if($_->rGet('error_code') == 1){?>

    <script>
        
        createNoty('<?=$_->l('Ошибка привязки аккаунта')?>', 'danger');
    </script>
<?}?>