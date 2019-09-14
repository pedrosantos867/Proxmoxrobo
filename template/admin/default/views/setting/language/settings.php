
<form method="post">
    <fieldset>
        <div class="form-group">
            <label for="disabledTextInput"><?=$_->l('Язык по умолчанию для панели администратора')?></label>
            <select class="form-control" name="admin_default_lang">

                <?foreach($languages as $lang){?>
                    <option <?=($config->admin_default_lang == $lang->id ? 'selected="selected"' : '')?> value="<?=$lang->id?>"><?=$lang->name?></option>
                <?}?>
            </select>
        </div>
        <div class="form-group">
            <label for="disabledTextInput"><?=$_->l('Язык по умолчанию для панели клиента')?></label>

            <select class="form-control" name="front_default_lang">

                <?foreach($languages as $lang){?>
                    <option <?=($config->front_default_lang == $lang->id ? 'selected="selected"' : '')?> value="<?=$lang->id?>"><?=$lang->name?></option>
                <?}?>
            </select>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" <?=($config->enable_lang_switcher_for_client ? 'checked="checked"' : '')?> name="enable_lang_switcher_for_client" value="1"> <?=$_->l('Разрешить смену языка клиенту')?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" <?=($config->enable_lang_switcher_for_admin ? 'checked="checked"' : '')?> name="enable_lang_switcher_for_admin" value="1"> <?=$_->l('Разрешить смену языка администратору')?>
            </label>
        </div>
        <button type="submit" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
    </fieldset>
</form>