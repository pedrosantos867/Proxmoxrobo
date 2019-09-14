<?= $_->JS('validator.js') ?>



<script>

    $(function () {



        $('form').on('submit', function (e) {
            e.preventDefault();

            $('input[name=login]').parents('.form-group').removeClass('has-error');
            $('input[name=pass]').parents('.form-group').removeClass('has-error');

            var postData = $(this).serializeArray();
            $('.loader').show();
            $.ajax(
                {
                    type: "POST",
                    dataType: 'json',
                    data: postData,
                    success: function (data, textStatus, jqXHR) {
                        $('.loader').hide();
                        if (data.result == '1'){
                            if(data.id_bill) {
                                location.href = '<?=$_->link('bill/')?>' + data.id_bill;
                            } else {
                                location.href = '<?=$_->link('vps-orders')?>' ;
                            }
                        } else {
                            if(data.error == 'user_exist'){
                                createNoty('<?=$_->l('Извините, пользователь с таким именем уже существует. Выберите другое имя.')?>', 'danger')
                            }
                            else if(data.error == 'password'){
                                createNoty('<?=$_->l('Пароль слишком простой, выберите пожалуйста другой пароль')?>', 'danger')
                            } else if(data.error == 'user_length'){
                                createNoty('<?=$_->l('Имя пользователя слишком длинное, попробуйте выбрать другое имя')?>', 'danger')
                            }
                        }
                        //data: return data from server
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //if fails
                    }
                });

        })
    })

</script>

<form method="post">
    <input type="hidden" name="ajax" value="1">

    <div class="form-group">
        <label for="exampleInputEmail1"><?= $_->l('Выбранный хостинг тариф') ?> (<a
                href="<?= $_->link('vps-orders/new') ?>"><?= $_->l('Изменить') ?></a>)</label>
        <input type="hidden" name="id_plan" value="<?= $plan->id ?>">
        <input class="form-control" value="<?= $plan->name ?>" disabled>
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1"><?= $_->l('Выберите сервер') ?></label>
        <select name="server" class="form-control">
            <? foreach ($servers as $server) { ?>
                <?if ($server->hidden) continue;?>
                <option value="<?= $server->id ?>" data-panel="<?=$server->type?>"><?= $server->name ?></option>
            <? } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="image"><?= $_->l('Выберите образ') ?></label>
        <select name="image" class="form-control">
            <? foreach ($plan->getImages() as $image) { ?>
                <option value="<?= $image ?>"><?= $image ?></option>
            <? } ?>
        </select>
    </div>
    <div class="form-group">
        <label><?= $_->l('Период оплаты') ?></label>
        <select name="pay_period" class="form-control">
            <? if($plan->test_days > 0){ ?>
                <option value="test"><?=$plan->test_days?> <?= $_->l('{'.$plan->test_days.'|день|дня|дней}') ?> - <?= $currency->displayPrice(0) ?> <?=$_->l('(тестовый период)')?></option>
            <? } ?>
            <option value="1">1 <?= $_->l('месяц') ?>
                - <?= $currency->displayPrice($plan->price) ?>      </option>
            <option value="2">2 <?= $_->l('месяца') ?>
                - <?= $currency->displayPrice($plan->price * 2)  ?>   </option>
            <option value="6">6 <?= $_->l('месяцев') ?>
                - <?= $currency->displayPrice($plan->price * 6)  ?>   </option>
            <option value="12">12 <?= $_->l('месяцев') ?>
                - <?= $currency->displayPrice($plan->price * 12)  ?>  </option>
        </select>
    </div>

    <div class="form-group ">
        <label><?= $_->l('Ваш логин для входа в панель управления хостингом') ?></label>
        <input name="login" data-validate="username" class="form-control" value="<?= $_->p('login') ?>">
    </div>
    <div class="form-group">
        <label><?= $_->l('Ваш пароль для входа в панель управления хостингом') ?></label>
        <input type="password" name="pass" data-validate="pass" class="form-control" value="<?= $_->p('password') ?>">
    </div>

    <script>
        $(function () {
            useDomain($('select[name=server] option:first-child'));
        });


        $('select[name=server]').on('change', function () {
            useDomain($(this).find('option:selected'));
        });

        function useDomain(element){
            var panel = (element).data('panel');
            if (panel == <?=\model\VpsServer::PANEL_VMMANAGER;?>){
                $('input[name=domain]').parents('div.form-group').show();
            } else {
                $('input[name=domain]').parents('div.form-group').hide();
            }
            $('form').validate(validate_messages);
        }
    </script>

    <div class="form-group">
        <label><?= $_->l('Домен') ?></label>
        <input type="text" name="domain" data-validate="domain" class="form-control" value="<?= $_->p('domain') ?>">
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="promocode_on">
        <?= $_->l('Использовать промокод') ?></label>
    </div>

    <div class="form-group promocode-inp-inner">
        <label><?= $_->l('Промокод') ?></label>
        <input type="text" name="promocode" class="form-control" placeholder="<?=$_->l('Ввести в случае наявности')?>" data-validate="ajax">
    </div>

    <?if($rules_page){?>
        <div class="form-group">
            <div class="checkbox ">
                <label>
                    <input type="checkbox" name="agree" data-validate="required" value="1"> <?= $_->l('Я согласен с условиями') ?> <a href="#" data-toggle="modal" data-target="#rulesModal" > <?= $_->l('договора') ?> </a>
                </label>
            </div>
        </div>
    <?}?>
    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-play-circle"></span> <?= $_->l('Сформировать заказ') ?>
    </button>
</form>

<!-- Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel"><?=$rules_page->name?></h4>
            </div>
            <div class="modal-body">
                <?=$rules_page->desc?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= $_->l('Закрыть') ?></button>
            </div>
        </div>
    </div>
</div>