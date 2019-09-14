<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="key"><?= $_->l('Key') ?></label>
            <input type="text" id="key" name="key"
                   value="<?= isset($pconfig->payture->key) ? $pconfig->payture->key : '' ?>"
                   class="form-control" placeholder="<?= $_->l('Key') ?>">
        </div>

        <div class="form-group">
            <label for="password"><?= $_->l('Password') ?></label>
            <input type="text" id="password" name="password"
                   value="<?= isset($pconfig->payture->password) ? $pconfig->payture->password : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('Password') ?>">
        </div>
        <div class="form-group">
            <label for="backend_secure_password"><?= $_->l('BackendSecurePassword') ?></label>
            <input type="text" id="backend_secure_password" name="backend_secure_password"
                   value="<?= isset($pconfig->payture->backend_secure_password) ? $pconfig->payture->backend_secure_password : '' ?>"
                   class="form-control"
                   placeholder="<?= $_->l('BackendSecurePassword') ?>">
        </div>
        <div class="form-group">
            <label><?= $_->l('Использовать валюту для рассчетов') ?></label>
            <select class="form-control" name="currency">
                <? foreach ($currencies as $currency) {
                    if ($currency->name == "RUB") {
                        ?>
                        <option <?= $pconfig->payture->currency == $currency->id ? 'selected="selected"' : '' ?>
                            value="<?= $currency->id ?>"><?= $currency->name ?></option>
                    <? }
                } ?>
            </select>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="test" <?= (isset($pconfig->payture->test) && $pconfig->payture->test ? 'checked="checked"' : '') ?>
                       value="1">
                <?= $_->l('Режим тестирования') ?> <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip"
                                                         data-placement="right" title=""
                                                         data-original-title="<?= $_->l('При проведении платежа транзакция не совершается! Меняется только состояние выставленного Вами счета, без зачисления суммы платежа на Ваш баланс.') ?>"></span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>