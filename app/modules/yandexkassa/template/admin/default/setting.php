<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="public_key"><?=$_->l('Идентификатор магазина')?></label>
            <input type="text" id="shopId" name="shopId"
                   value="<?= isset($pconfig->yandex->shopId) ? $pconfig->yandex->shopId : '' ?>"
                   class="form-control" placeholder="<?=$_->l('Идентификатор магазина')?>">
        </div>
        <div class="form-group">
            <label for="private_key"><?=$_->l('Идентификатор витрины магазина')?></label>
            <input type="text" id="scid" name="scid"
                   value="<?= isset($pconfig->yandex->scid) ? $pconfig->yandex->scid : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Идентификатор витрины магазина')?>">
        </div>
        <div class="form-group">
            <label for="password"><?=$_->l('Пароль магазина')?></label>
            <input type="text" id="password" name="password"
                   value="<?= isset($pconfig->yandex->password) ? $pconfig->yandex->password : '' ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Пароль магазина')?>">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="test_mode" value="1" <?= ($pconfig->yandex->test_mode==1) ? 'checked="checked"' : '' ?>> <?=$_->l('Использовать тестовый сервер')?>
            </label>
        </div>
        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>