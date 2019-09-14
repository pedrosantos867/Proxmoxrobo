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
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Регистратор доменных имен')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link($request) ?>" <?= (isset($ajax) ? 'class="ajax-form"' : '') ?>
                          method="post">
                        <? if (isset($ajax)) { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?=$_->l('Название')?></label>
                            <input type="text" class="form-control" name="name" data-validate="required"

                                   value="<?= $registrar->name ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1"><?=$_->l('Регистратор')?></label>
                            <select name="type" class="form-control">
                                <?foreach ($types as $key => $name){?>
                                    <option <?= $registrar->type == $key ? 'selected="selected"' : '' ?>
                                        value="<?= $key ?>"><?=$name?>
                                    </option>
                                <?}?>


                            </select>
                        </div>

                        <div class="form-group">
                            <label for="login"><?=$_->l('Логин')?></label>
                            <input type="text" class="form-control" name="login" data-validate="required"
                                   value="<?= $registrar->login ?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?=$_->l('Пароль или ключ API')?></label>
                            <input type="text" class="form-control" name="password" data-validate="required"
                                   value="<?= $registrar->password ?>">
                        </div>

                        <div class="form-group">
                            <label for="password"><?=$_->l('URL')?></label>
                            <input type="text" class="form-control" name="url" placeholder="<?=$_->l('Оставьте поле пустым, чтобы использовать значение по умолчанию')?>"
                                   value="<?= $registrar->url ?>">
                        </div>

                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><span
                                    class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?>
                            </button><? } ?>
                    </form>


                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-success"><span
                            class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>