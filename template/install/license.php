<div class="row">
    <div class="col-md-12" style="padding: 15px">
        <?php if (isset($error) && $error == 'license_invalid') { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <?=$_->l('Лицензионный ключ недействительный!')?>
            </div>
        <?php } ?>

        <h4 class="alert alert-danger alert-dismissible" id="licence-agreement-lable"><?=$_->l('Для продолжения вы должны согласиться с условиями лицензии')?></h4>

    </div>
</div>


<form method="post">
    <div class="row">
        <div class="col-md-12">

            <div class="form-group">

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading text-center">
                                <?=$_->l('Условия лицензии')?>
                            </div>

                            <?if($_->cookie('lang_install') == 1 || $_->cookie('lang_install') == 4){?>
                                <div class="panel-body" style="overflow-y: scroll; max-height: 300px">
                                <h3>Лицензирование и информация</h3>
                                Устанавливая, копируя или используя программное обеспечение HopeBilling (далее - биллинг система), вы соглашаетесь с условиями этого лицензионного соглашения. Мы оставляем за собой право заблокировать или приостановить лицензии в случае нарушения условий этого договора, а в случае повторного нарушения – внести домены в “черный” список.

                                <h3>Условия получения</h3>

                                Все лицензии выдаются автоматически сервером лицензирования в процессе установки или через службу поддержки. При смене типа лицензии после оплаты нужно написать запрос в службу поддержки обязательно указав Ваш лицензионный ключ выданный сервером лицензирования.

                                <h3>Условия использования</h3>

                                <h4>Бесплатная лицензия</h4>

                                Бесплатная лицензия выдается бесплатно в процессе установки биллинг системы путем отправки ключа на указанный электронный адрес на срок 10 лет. В бесплатной лицензии запрещено убирать или менять знак охраны авторского права и проводить смену логотипов.

                                <h4>Платные лицензии</h4>

                                Платная лицензия выдается, после оплаты лицензии установленной стоимости на момент покупки. В платной версии разрешено проводить любую модификацию программного кода биллинг системы. Также может быть получено разрешение на скрытие знака охраны авторского права по написанию запроса в службу поддержки, при этом все авторские права сохраняются, а за услугу может взиматься дополнительная плата.

                                <h3>Смена типа лицензии</h3>
                                Смена типа лицензии из:

                                <ul>
                                    <li> лицензии типа «VIP» на бесплатную лицензию составляет стоимость одной лицензии типа «VIP». </li>
                                    <li> лицензии типа «VIP» на лицензию типа «PRO» производиться бесплатно. </li>
                                    <li> лицензии типа «PRO» на лицензию типа «VIP» производиться бесплатно. </li>
                                </ul>

                                <h3>Передача лицензии</h3>

                                Лицензии купленные у нас не могут быть переданы или перепроданы другим людям. Пожалуйста свяжитесь с службой поддержки по этим вопросам.

                                <h3>Условия возврата</h3>

                                Возврат стоимости лицензии платных версий не производится.

                                <h3>Техническая поддержка</h3>

                                Оказании поддержки производится на сайте http://support.hopebilling.com Время ответа на запрос к поддержке не гарантировано и не лимитировано, и может занимать до 72 часов. Также компания оставляет за собой право отказать в поддержке в случае использование бесплатной лицензии или если запрос не имеет отношения к биллинг системе (также мы оставляем за собой право отказаться от дальнейшей поддержки и сопровождения без объяснения причины). Поддержка производится на английском, русском или украинском языке.
                            </div>
                            <?} else {?>
                                <div class="panel-body"  style="overflow-y: scroll; max-height: 300px">
                                <h3>Licensing and information</h3>

                                Establishing, making the copies or using the software of HopeBilling (further - billing system), you agree with conditions of this license agreement. We reserve the right to block or suspend licenses in case of violation of terms of this agreement, and in case of further violation – to enter the domains in the "black" list.

                                <h3> Receiving conditions </h3>

                                All licenses are granted automatically by the licensing server in the course of installation or through the support service. During the changing of type of a license after paying it is necessary to write a request to support service surely having specified your license key issued by the licensing server.
                                Using conditions

                                <h4> Free license </h4>

                                The free license is granted free  in the course of installation billing system by sending a key for the specified e-mail address for the term of 10 years. In a free license it is forbidden to clean or change a sign of protection or an author's right and to change the logos, and also to modify the source code of system.

                                <h4> Paid licenses </h4>

                                The paid license is granted after payment of the license of the  determined value at the time of purchase. In the paid version it is authorized to carry out any modification of a program code of billing system. Also can be got permission to hide a sign of protection of an author's right on writing of a request in support service, at the same time all author's rights are in safe, that`s why, it can be an additional charge for the service.

                                <h3> Changing of a license type </h3>

                                Changing of a license type from:
                                <ul>
                                    <li>"VIP" licenses for the free license are constituted by the cost of one "VIP" license.</li>
                                    <li>"VIP" licenses for the "PRO" license is free.</li>
                                    <li>"PRO" licenses for the "VIP" license is free.</li>
                                </ul>

                                <h3> Transferring of the license </h3>

                                The licenses purchased from us can't be transferred or resold to other people. Please contact support service on these questions.

                                <h3> Terms of returning </h3>

                                Returning a cost of the license of the paid versions can`t be made.

                                <h3> Technical support </h3>

                                Support  can be made on the website http://support.hopebilling.com
                                Time of replying to the request to support isn't guaranteed and not limited, and can borrow till 72 hours. Also the company reserves the right to refuse support in a case use of the free license or if the request has no relation to billing to system (also we reserve the right to refuse further support and maintenance without an explanation  of a reason ). Support can be made in English, Russian or Ukrainian.

                            </div>
                            <?}?>

                            <div class="panel-footer">
                                <label><input id="licence-agreement" type="checkbox" > <?=$_->l('Я согласен с условиями лицензии')?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="licence-agreement-tab">
                        <label for="license_key"><?=$_->l('Ваш лицензионный ключ')?></label>

                        <div style="margin-bottom:8px">



                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control" name="license_key" id="license_key" value="<?=$free_key?>"
                               placeholder="<?=$_->l('Ваш лицензионный ключ')?>">
                        </div>

                            <div class="row">
                            <div class="col-md-6">
                                <a href="https://hopebilling.com/#price" target="_blank" class=" btn btn-xs btn-primary hbls"><?=$_->l('Купить лицензию')?></a>
                                <a href="<?='http://service.hopebilling.com/licenser.php?create_free_key=1&domain='.$_SERVER['SERVER_NAME'].''?>" target="_blank" class=" btn btn-xs btn-default"><?=$_->l('Получить бесплатный ключ')?></a>

                            </div>

                        </div>

                        <?/*
                        <script>
                            var vidget = Object.create(HBLS_modal_form);

                            <?
                            if (isset($_GET['id_lang']) && $_GET['id_lang'] != 4){
                                $lang = 'en';
                            }
                            else $lang = 'ru';
                            ?>



                            $('.hbls').on('click', function (e) {
                                e.preventDefault();
                               // vidget.check('<?=$_->link('install?step=3')?>', '<?=$lang?>', '<?=$_SERVER['HTTP_HOST']?>');
                                vidget.init('<?=$_->link('install?step=3')?>', '<?=$lang?>', 'pro', '<?=$_SERVER['HTTP_HOST']?>');

                            });

                            <?if (isset($hbls_hash)){?>
                            vidget.confirm('<?=$hbls_bill?>', '<?=$hbls_hash?>', '<?=$lang?>');

                            <?}?>

                        </script>
                        */?>

                    </div>
                </div>

                <script>
                    $('#licence-agreement-lable').toggle();
                    $('#licence-agreement').change(function () {
                        $('#licence-agreement-tab a').toggleClass('disabled');
                        $('.need-licence-agreement').toggleClass('disabled');
                        $('#licence-agreement-lable').toggle();

                    }).trigger('change');

                </script>

            </div>


            <script>
                $(function(){
                    //will be deleted
                    $('#send_license').on('click', function () {
                    $.ajax({
                        url: 'http://service.hopebilling.com/licenser.php?create_free_key=1&domain=' + document.domain + '&email=' + $('#email').val() + '&user='+$('#fullname').val(),
                        type: 'get',
                        success: function (data) {

                                $('#getLicenseModal').modal('hide');
                                $('#licenseSendModal').modal('show');
                                $('#license_key').val(data);
                            
                        }
                    })
                });
                    //end delete code

                    $('body').on('click', '.disabled', function () {
                        return false;
                    })
                })
            </script>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right need-licence-agreement disabled"><?=$_->l('Далее')?> <span
                    class="glyphicon glyphicon-chevron-right "></span></button>
        </div>
    </div>
</form>


<!-- Modal -->
<div class="modal fade" id="getLicenseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=$_->l('Активация бесплатной лицензии')?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="fullname"><?=$_->l('ФИО')?></label>
                    <input type="text" class="form-control" id="fullname" placeholder="<?=$_->l('Фамилия Имя Отчество')?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                <button type="button" id="send_license" class="btn btn-primary"><?=$_->l('Отправить')?></button>
            </div>
        </div>

    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="licenseSendModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=$_->l('Ваша лицензия активированна')?></h4>
            </div>
            <div class="modal-body">
                <?=$_->l('Лицензионный ключ был отправлен на Ваш Email, указанный при активации лицензии.')?>
                <?=$_->l('Если у Вас возникли вопросы, напишите нам')?> <a href="mailto:support@hopebilling.com">support@hopebilling.com</a>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>

            </div>
        </div>

    </div>
</div>