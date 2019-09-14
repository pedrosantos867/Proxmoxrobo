<div class="loaded-block">
    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('form').validate({messages: validate_messages});

        })
    </script>
    <? if (isset($ajax)) { ?>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Пользователь</h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link($request) ?>" <?= (isset($ajax) ? 'class="ajax-form"' : '') ?>
                          method="post">
                        <? if (isset($ajax)) { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Имя пользователя</label>
                            <input type="text" class="form-control" name="username" data-validate="username|ajax"
                                   data-validate-def="<?= $client->username ?>"
                                   value="<?= $client->username ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" name="pass" data-validate="pass"
                                   value="<?= ($client->password ? '________' : '') ?>" placeholder="*****">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">ФИО</label>
                            <input type="text" class="form-control" name="name" data-validate="fio"
                                   value="<?= $client->name ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">EMAIL</label>
                            <input type="text" class="form-control" name="email" data-validate="email|ajax"
                                   data-validate-def="<?= $client->email ?>" value="<?= $client->email ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Телефон</label>
                            <input type="text" class="form-control" name="phone" value="<?= $client->phone ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Комментарий</label>
                            <textarea class="form-control" name="phone"><?= $client->comment ?></textarea>
                        </div>
                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-default">Сохранить</button><? } ?>
                    </form>


                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>