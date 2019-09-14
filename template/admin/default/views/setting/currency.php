<form method="post">
    <div class="form-group">
        <label for="name"><?=$_->l('Название')?></label>
        <input type="text" class="form-control" name="name" value="<?= $cur->name ?>">
    </div>
    <div class="form-group">
        <label for="iso">ISO</label>
        <input type="text" class="form-control" name="iso" value="<?= $cur->iso ?>" maxlength="3">
    </div>
    <div class="form-group">
        <label for="short_name"><?=$_->l('Формат отражения')?></label>
        <input type="text" class="form-control" name="short_name" value="<?= $cur->short_name ?>">
        <span class="help-inline">Например: $ {0} или {0} руб.</span>
    </div>
    <div class="form-group">
        <label for="coefficient"><?=$_->l('Коэффициент по отношению к валюте по-умолчанию')?></label>
        <input type="text" class="form-control" placeholder="0.5" name="coefficient" value="<?= $cur->coefficient ?>">
    </div>


    <button type="submit" class="btn btn-default"><?=$_->l('Сохранить')?></button>
</form>