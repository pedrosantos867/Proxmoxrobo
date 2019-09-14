<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="terminal_name"><?= $_->l('Terminal name') ?></label>
            <input type="text" id="terminal_name" name="terminal_name"
                   value="<?= $pconfig->tinkoff->terminal_name != '' ? $pconfig->tinkoff->terminal_name : '' ?>"
                   class="form-control" placeholder="<?= $_->l('Terminal name') ?>">
        </div>
        <div class="form-group">
            <label for="secret_key"><?= $_->l('Secret key') ?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->tinkoff->secret_key) ? $pconfig->tinkoff->secret_key : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('Secret key') ?>">
        </div>
        <div class="form-group">
            <label for="url"><?= $_->l('URL') ?></label>
            <input type="text" id="url" name="url"
                   value="<?= isset($pconfig->tinkoff->url) ? $pconfig->tinkoff->url : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('URL') ?>">
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>