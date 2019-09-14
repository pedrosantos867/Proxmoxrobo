<?= $_->JS('validator.js') ?>
<script>
    $(function () {
        $('.main-form').validate({messages: validate_messages});

    })
</script>
<form class="main-form" action='' method="POST" enctype="multipart/form-data">

    <? if (!empty($errors)) { ?>
        <? foreach ($errors as $error) { ?>
            <? if ($error == 'file_greater5') { ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="glyphicon glyphicon-warning-sign"></span>
                    <?=$_->l('Файл вложения не может превишать 5 МБ.')?>
                </div>
            <? }  ?>
        <? } ?>
    <? } ?>

<div class="form-group">
    <label class="control-label" for="subject"><?= $_->l('Тема') ?></label>
        <input type="text" id="subject" name="subject" data-validate="required" placeholder=""
               class="input-xlarge form-control">
    </div>

    <div class="form-group">
        <!-- E-mail -->
        <label class="control-label" for="name"><?= $_->l('Приоритет') ?></label>
        <select type="text" id="priority" name="priority" data-validate="required" class="input-xlarge form-control">
            <option value="0"><?= $_->l('Низкий') ?></option>
            <option value="1"><?= $_->l('Средний') ?></option>
            <option value="2"><?= $_->l('Высокий') ?></option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="message"><?= $_->l('Сообщение') ?></label>
        <textarea id="message" name="message" placeholder="" data-validate="required"
                  class="input-xlarge form-control"></textarea>
    </div>
    <div class="form-group">
        <label class="control-label" for="message"><?= $_->l('Файлы') ?></label>
        <input type="file" multiple="multiple" class="input-xlarge form-control" name="files[]">
    </div>
    <div class="form-group">
        <button class="btn btn-success "><span class="glyphicon glyphicon-send"></span> <?= $_->l('Отправить') ?>
        </button>
        <a href="<?= $_->link('support') ?>" class="btn btn-info pull-right"><span
                class="glyphicon glyphicon-arrow-left"></span> <?= $_->l('К списку тикетов') ?></a>
    </div>
</form>