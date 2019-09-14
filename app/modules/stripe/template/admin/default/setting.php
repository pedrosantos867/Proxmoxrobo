<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="key"><?= $_->l('Secret key') ?></label>
            <input type="text" id="secret_key" name="secret_key"
                   value="<?= isset($pconfig->stripe->secret_key) ? $pconfig->stripe->secret_key : '' ?>"
                   class="form-control" placeholder="<?= $_->l('Secret key') ?>">
        </div>

        <div class="form-group">
            <label for="password"><?= $_->l('Publishable key') ?></label>
            <input type="text" id="publishable_key" name="publishable_key"
                   value="<?= isset($pconfig->stripe->publishable_key) ? $pconfig->stripe->publishable_key : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('Publishable key') ?>">
        </div>
        <div class="form-group">
            <label><?= $_->l('Использовать валюту для рассчетов') ?> <span style="color: red;" data-toggle="tooltip"
                                                                           data-placement="right" title=""
                                                                           data-original-title="<?= $_->l('Для корректной работы платежной системы Stripe в обязательном порядке у вас должна быть создана валюта USD.') ?>">см. примечание</span></label>
            <select class="form-control" name="currency">
                <? foreach ($currencies as $currency) {
                    ?>
                    <option <?= $pconfig->stripe->currency == $currency->id ? 'selected="selected"' : '' ?>
                        value="<?= $currency->id ?>"><?= $currency->name ?></option>
                    <?
                } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>