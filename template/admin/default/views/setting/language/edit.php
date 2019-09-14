<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="exampleInputEmail1"><?=$_->l('Название')?></label>
        <input type="text" class="form-control" name="name" value="<?= $language->name ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">ISO</label>
        <input type="text" class="form-control" name="iso_code" <?=($language->id ? 'readonly="readonly"' : '')?>  value="<?= $language->iso_code ?>">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1"><?=$_->l('Иконка')?> (24x24 px)</label>
        <input type="file" class="form-control" name="ico">
    </div>
    <button type="submit" class="btn btn-default"><?=$_->l('Сохранить')?></button>
</form>