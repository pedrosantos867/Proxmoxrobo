<? if ($system == 'turbosms') { ?>
    <form method="post">
        <fieldset>
            <div class="form-group">
                <label for="disabledTextInput">Имя отправителя</label>
                <input type="text" id="disabledTextInput" name="sender"
                       value="<?= (isset($smsconfig->turbosms->sender) ? $smsconfig->turbosms->sender : '') ?>"
                       class="form-control" placeholder="Имя отправителя">
            </div>
            <div class="form-group">
                <label for="disabledTextInput">Логин</label>
                <input type="text" id="disabledTextInput" name="login"
                       value="<?= (isset($smsconfig->turbosms->login) ? $smsconfig->turbosms->login : '') ?>"
                       class="form-control" placeholder="Логин">
            </div>
            <div class="form-group">
                <label for="disabledSelect">Пароль</label>
                <input type="text" id="disabledTextInput" name="password"
                       value="<?= isset($smsconfig->turbosms->password) ? $smsconfig->turbosms->password : '' ?>"
                       class="form-control"
                       placeholder="Пароль">
            </div>

            <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
        </fieldset>
    </form>
<? } elseif ($system == 'smsc') { ?>
    <form method="post">
        <fieldset>
            <div class="form-group">
                <label for="disabledTextInput">Имя отправителя</label>
                <input type="text" id="disabledTextInput" name="sender"
                       value="<?= (isset($smsconfig->smsc->sender) ? $smsconfig->smsc->sender : '') ?>"
                       class="form-control" placeholder="Имя отправителя">
            </div>
            <div class="form-group">
                <label for="disabledTextInput">Логин</label>
                <input type="text" id="disabledTextInput" name="login"
                       value="<?= (isset($smsconfig->smsc->login) ? $smsconfig->smsc->login : '') ?>"
                       class="form-control" placeholder="Логин">
            </div>
            <div class="form-group">
                <label for="disabledSelect">Пароль</label>
                <input type="text" id="disabledTextInput" name="password"
                       value="<?= isset($smsconfig->smsc->password) ? $smsconfig->smsc->password : '' ?>"
                       class="form-control"
                       placeholder="Пароль">
            </div>

            <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
        </fieldset>
    </form>
<? } ?>
