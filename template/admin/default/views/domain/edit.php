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
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Информация о домене')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link($request) ?>" <?= (isset($ajax) ? 'class="ajax-form"' : '') ?>
                          method="post">
                        <? if (isset($ajax)) { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                        <div class="form-group">
                            <label for="name"><?=$_->l('Зона')?></label>
                            <input type="text" class="form-control" name="name" data-validate="custom|ajax" data-validate-send-ajax="<?=$domain->id?>"
                                   data-validate-match="^([a-zа-я]{2,})([.a-z]{0,})$" data-validate-message-fail-custom="<?=$_->l('Например: ru или com')?>"

                                   value="<?= $domain->name ?>">
                        </div>
                        <div class="form-group">
                            <label for="registrant_id"><?=$_->l('Регистратор')?></label>
                            <select name="registrant_id" data-validate="required" class="form-control">
                                <? foreach ($registrars as $registrar) { ?>
                                    <option <?= $domain->registrant_id == $registrar->id ? 'selected="selected"' : '' ?>
                                        value="<?= $registrar->id ?>"><?= $registrar->name ?></option>
                                <? } ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="min_period"><?=$_->l('Минимальный период регистрации')?></label>
                            <input type="number" class="form-control" name="min_period" data-validate="required"
                                   value="<?= $domain->min_period ?>">
                        </div>

                        <div class="form-group">
                            <label for="min_period"><?=$_->l('Максимальный период регистрации')?></label>
                            <input type="number" class="form-control" name="max_period" data-validate="required"
                                   value="<?= $domain->max_period ?>">
                        </div>

                        <div class="form-group">
                            <label for="min_period"><?=$_->l('Минимальный срок продления')?></label>
                            <input type="number" class="form-control" name="min_extension_period" data-validate="required"
                                   value="<?= $domain->min_extension_period ?>">
                        </div>
                        <div class="form-group">
                            <label for="min_period"><?=$_->l('Максимальный срок продления')?></label>
                            <input type="number" class="form-control" name="max_extension_period" data-validate="required"
                                   value="<?= $domain->max_extension_period ?>">
                        </div>


                        <div class="form-group">
                            <label for="min_period"><?= $_->l('Стоимость домена') ?>: </label>
                            <input type="number" class="form-control" name="price" data-validate="required"
                                   value="<?= $domain->price ?>" min="0">
                        </div>


                        <div class="form-group">
                            <label for="min_period"><?= $_->l('Стоимость продления домена') ?>:</label>
                            <input type="number" class="form-control" name="extension_price" data-validate="required"
                                   value="<?= $domain->extension_price ?>" min="0">
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