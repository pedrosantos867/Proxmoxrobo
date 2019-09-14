<div class="loaded-block">
    <? if (isset($ajax)) { ?>
<?= $_->JS('validator.js') ?>

    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $_->l('Параметр') ?></h4>
                </div>
                <div class="modal-body">
                    <? } ?>
                    <form <?= (isset($ajax) ? 'class="ajax-form"' : '') ?>
                        action='<?= $_->link('admin/plan/param/' . ($param->id ? $param->id : 'add')) ?>'
                        method="POST">
                        <div class="form-group">
                            <label for="username"><?= $_->l('Название') ?></label>
                            <input type="text" id="name" data-validate="required" name="name" value="<?= $param->name ?>" placeholder=""
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="username"><?= $_->l('Описание') ?></label>
                            <input type="text" id="desc" name="desc" value="<?= $param->desc ?>" placeholder=""
                                   class="form-control">
                        </div>

                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><?= $_->l('Сохранить') ?></button>
                        <? } else { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>

                    </form>
                    <script>
                        $(function () {
                            $('form').validate({messages: validate_messages});
                        });
                    </script>
                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $_->l('Закрыть') ?></button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-success"><span
                            class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>
