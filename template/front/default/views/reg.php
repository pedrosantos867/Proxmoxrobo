<?= $_->css('reg.css') ?>
<?= $_->JS('validator.js') ?>
<script>
    $(function () {
        $('.form').validate({messages: validate_messages});
        $('form.sms-confirmation').validate({messages: validate_messages});
        $('form.code-confirmation').validate({messages: validate_messages});
    })
</script>
<? if ($config->enabled_sms_confirm) { ?>
<script>
    $(function () {
        $('.form').on('submit', function (e) {

            if ($('.form input[name=id_confirmation]').val() && $('.form input[name=code]').val()) {

            }
            else {
                e.preventDefault();
                $('.sms-confirmation input[name=phone]').val($('.form input[name=phone]').val()).trigger('change');
                $('.sms-confirmation input[name=phone]').attr('disabled', 'disabled');

                $('#check-mobile').modal('show');
            }

        });

        $('.sms-confirmation').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                method: 'post',
                dataType: 'json',
                data: {action: 'sendSmsCode', phone: $('.sms-confirmation input[name=phone]').val(), ajax: 1},
                success: function (data) {
                    $('.form input[name=id_confirmation]').val(data.code);
                    $('.code-confirmation input[name=code]').data('validate-send-ajax', data.code);

                    $('.sms-confirmation').hide();
                    $('.code-confirmation').show();
                }
            });
            //ajax

        });

        $('.code-confirmation').on('submit', function (e) {
            e.preventDefault();

            $('.form input[name=code]').val($('.code-confirmation input[name=code]').val());
            $('.form').submit();
        })
    })
</script>
<? } ?>
<div id="check-mobile" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="mySmallModalLabel"><?= $_->l('Подтверждение телефона') ?></h4>
            </div>
            <div class="modal-body">
                <form class="sms-confirmation">
                    <div class="form-group phone-group">
                        <label for="phone"><?= $_->l('Номер телефона') ?></label>
                        <input type="text" id="phone" name="phone" placeholder=""
                               class="input-xlarge form-control" data-validate="required|ajax">
                    </div>

                    <div class="form-group phone-group">
                        <button id="sms-send" type="submit"
                                class="btn btn-warning form-control"><?= $_->l('Отправить СМС') ?></button>
                    </div>


                </form>

                <form class="code-confirmation" style="display: none;">

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

            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row top30">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
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
                    <h3 class="panel-title text-center"><?= $_->l('Регистрация') ?></h3>
                </div>
                <div class="panel-body">
                    <? if ($error == 'no_pass') { ?>
                        <div class="alert alert-danger"
                             role="alert"><?= $_->l('Пользователь не существует или пароль неверный!') ?>
                        </div>
                    <? } else if ($error == 'no_valid') { ?>
                        <div class="alert alert-danger" role="alert"><?= $_->l('Заполните все поля!') ?></div>
                    <? } ?>

                    <form id="reg-form" class="form" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="id_confirmation" value="">
                                <input type="hidden" name="code" value="">

                                <div class="form-group">

                                    <label class="control-label" for="username"><?= $_->l('Имя пользователя') ?></label>
                                    <input type="text" id="username" name="username" placeholder=""
                                           class="input-lg form-control" data-validate="username|ajax">
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="name"><?= $_->l('E-mail') ?></label>
                                    <input type="text" id="email" name="email" placeholder=""
                                           class="input-lg form-control" data-validate="email|ajax">
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="password_confirm"><?= $_->l('ФИО') ?></label>
                                    <input type="text" id="name" name="name" placeholder=""
                                           class="input-lg form-control" data-validate="fio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Password -->
                                    <label class="control-label" for="password_confirm"><?= $_->l('Телефон') ?></label>


                                    <input type="text" id="phone" name="phone" placeholder=""
                                           class="input-lg form-control" data-validate="phone|ajax">
                                </div>
                                <div class="form-group">
                                    <!-- Password-->
                                    <label class="control-label" for="password"><?= $_->l('Пароль') ?></label>

                                    <input type="password" id="pass" name="pass" placeholder=""
                                           class="input-lg form-control" data-validate="pass">


                                </div>

                                <div class="form-group">
                                    <!-- Password -->
                                    <label class="control-label"
                                           for="password_confirm"><?= $_->l('Пароль (повторно)') ?></label>


                                    <input type="password" id="pass1" name="pass1" placeholder=""
                                           class="input-lg form-control" data-validate="required|pass2"
                                           data-validate-field-pass="input[name=pass]">


                                </div>
                            </div>

                        </div>

                        <? if ($config->enabled_captcha) { ?>
                            <div class="form-group">
                                <script src='https://www.google.com/recaptcha/api.js'></script>
                                <div class="g-recaptcha" data-sitekey="<?= $config->recaptcha_sitekey ?>"></div>
                            </div>
                        <? } ?>


                        <div class="form-group">
                        <!-- Button -->

                                <div class="controls">
                                    <button class="btn btn-primary btn-lg btn-block"><span
                                            class="glyphicon glyphicon-floppy-save"></span> <?= $_->l('Регистрация') ?>
                                    </button>
                                </div>

                            </div>

                        <?if($config->enable_social_auth){?>
                            <div class="social-reg">
                                <?=$_->l('Регистрация через соц.сети')?>

                                <? if(is_array($socialAuthInfo->networks)) { ?>
                                   <?
                                    if(count($socialAuthInfo->networks)>4){
                                    $networks_providers = implode(',', array_slice($socialAuthInfo->networks, 0, 4));
                                    $networks_hidden = implode(',', array_slice($socialAuthInfo->networks, 4));
                                    } else {
                                        $networks_providers = implode(',', $socialAuthInfo->networks);
                                        $networks_hidden = '';
                                    }
                                    ?>
                                <script src="//ulogin.ru/js/ulogin.js"></script>
                                <div id="uLogin" data-ulogin="display=panel;theme=classic;fields=first_name,last_name,phone,email,nickname;verified_email=1;lang=<?=$lang->iso_code?>;providers=<?=$networks_providers?>;hidden=<?=$networks_hidden?>;redirect_uri=<?=$_->link('/social/auth' . $_->rGet('back'))?>;mobilebuttons=0;"></div>


                                <?}?>

                              <br>
                            </div>
                        <?}?>
                        
                        <div style="margin-top: 8px" class="text-center">
                            <a href="<?= $_->link('login' . ($_->rGet('back')?'?back=' . $_->rGet('back') :'')) ?>"> ← <?=$_->l('Страница входа')?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




