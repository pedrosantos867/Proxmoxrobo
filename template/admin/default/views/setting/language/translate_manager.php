<form method="get" onchange="$(this).submit()">
    <input type="hidden" name="id_lang" value="<?=$_->rget('id_lang')?>">
    <div class="form-group">
    <label><?=$_->l('Выберите тип панели:')?></label>
    <select name="type" class="form-control">
        <option value=""><?=$_->l('Выберите тип панели')?></option>
        <optgroup label="System">
        <option <?=($_->rget('type') == 1 ? 'selected="selected"' : '')?> value="1"><?=$_->l('Панель клиента')?></option>
        <option <?=($_->rget('type') == 2 ? 'selected="selected"' : '')?> value="2"><?=$_->l('Панель администратора')?></option>
        <option <?=($_->rget('type') == 4 ? 'selected="selected"' : '')?> value="4"><?=$_->l('Email сообщения клиенту')?></option>
        <option <?=($_->rget('type') == 5 ? 'selected="selected"' : '')?> value="5"><?=$_->l('Email сообщения администратору')?></option>
        <option <?=($_->rget('type') == 3 ? 'selected="selected"' : '')?> value="3"><?=$_->l('Установщик')?></option>
        </optgroup>
        <optgroup label="Modules">
            <? foreach ($modules as $module){ ?>
                <option <?=($_->rget('type') == 'm'.$module->id.'f' ? 'selected="selected"' : '')?> value="m<?=$module->id?>f"><?=$module->name?> (<?=$_->l('Панель клиента')?>)</option>
                <option <?=($_->rget('type') == 'm'.$module->id.'a' ? 'selected="selected"' : '')?> value="m<?=$module->id?>a"><?=$module->name?> (<?=$_->l('Панель администратора')?>)</option>
            <? } ?>
        </optgroup>
    </select>
</div>
    <div class="form-group">
    <label><?=$_->l('Выберите язык с которого хотите осуществить перевод')?></label>
    <select name="from" class="form-control">

        <?foreach($languages as $l){?>
            <option <?=($_->rget('from') == $l->id ? 'selected="selected"' : '')?> value="<?=$l->id?>">
                <?=$l->name?>
            </option>
        <?}?>
    </select>
        </div>
</form>

<?if($_->rget('type') ){?>



    <form method="post" onchange="return confirm(' <?=$_->l('Внимание! Это действие удалит текущий перевод выбранного языка. Продолжить ?')?>') ? $(this).submit() :  false;">
        <div class="form-group">
        <label for="copy_from"><?=$_->l('Копировать фразы:')?> </label>
        <select class="form-control" name="copy_from">
            <option value="">Выбрать</option>
            <?foreach($languages as $l){?>
                <option  value="<?=$l->id?>">
                    <?=$l->name?>
                </option>
            <?}?>
        </select>
        </div>
    </form>


<form method="post">
<?  if(is_array($translates) && count($translates)>0){$i=0; foreach ($translates as $file => $translate) { ?>

    <div><?= $file ?></div>

    <?  foreach ($translate as $from => $to) {
        ?>
        <div class="row"  style="margin-top: 5px">
            <div class="col-md-5">
                <input class="form-control input-original-<?=$i?>" value="<?= htmlspecialchars( $_->tr($from,  $dir, $file,  $_->rget('from')) ) ?>" disabled="disabled">
            </div>
            <div class="col-md-6">
                <input class="form-control input-translate-<?=$i?>" name="translate[<?=$file?>][<?= htmlspecialchars($from) ?>]" value="<?= $to ?>"></div>
            <div class="col-md-1">
                <a class="btn btn-warning btn-xs translate"  data-id="<?=$i?>" ><span class="glyphicon glyphicon-globe"></span></a>
                <a class="btn btn-danger btn-xs "  onclick="$(this).parent().parent().remove()" ><span class="glyphicon glyphicon-remove-circle"></span></a>
            </div>
        </div>
        <? $i++;
    }
    ?>



    <?
}}
?>
    <script>
        $('a.translate').on('click',function(){
            var word = $('.input-original-'+$(this).data('id')).val();
            var id = $(this).data('id');
            $.ajax({
                url: 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20151015T125926Z.8bb30846db519a97.7886b6c890269c50a4bfabc244473123827fbeaf&text='+word+'&lang=<?=$language->iso_code?>',
                dataType: 'json',
                success:function(data){
                //   alert(data.text[0]);
                    $('.input-translate-'+id).val(data.text[0]);
                }
            })


        })
    </script>

<button class="btn btn-default" type="submit"><?=$_->l('Сохранить')?></button>
</form>
<?}?>