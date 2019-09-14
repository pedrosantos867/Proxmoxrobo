<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="recaptcha_sitekey">SiteKey</label>
            <input type="text" id="recaptcha_sitekey" name="recaptcha_sitekey"
                   value="<?= $cfg->recaptcha_sitekey ?>"
                   class="form-control" placeholder="SiteKey">
        </div>
        <div class="form-group">
            <label for="recaptcha_sitekey">Secret</label>
            <input type="text" id="recaptcha_secret" name="recaptcha_secret"
                   value="<?= $cfg->recaptcha_secret ?>"
                   class="form-control" placeholder="Secret">
        </div>

        <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
    </fieldset>
</form>