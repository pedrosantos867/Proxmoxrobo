<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="code"><?= $_->l('code') ?></label>
            <textarea id="code" name="code"
                      class="form-control"
                      placeholder="<?= $_->l('code') ?>"><?= (isset($pconfig->options["siteheart"])) ? $pconfig->options["siteheart"] : '' ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>