

<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="url"><?=$_->l('API URL')?></label>
            <input type="text" id="url" name="url"
                   value="<?= $bconfig->url  ?>"
                   class="form-control"
                   placeholder="<?=$_->l('API URL')?>">
        </div>

        <div class="form-group">
            <label for="username"><?=$_->l('Логин')?></label>
            <input type="text" id="username" name="username"
                   value="<?= $bconfig->username  ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Логин')?>">
        </div>

        <div class="form-group">
            <label for="password"><?=$_->l('Пароль')?></label>
            <input type="text" id="password" name="password"
                   value="<?= $bconfig->password  ?>"
                   class="form-control"
                   placeholder="<?=$_->l('Пароль')?>">
        </div>



        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>