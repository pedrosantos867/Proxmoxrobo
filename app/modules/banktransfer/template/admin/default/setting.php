<?=$_->js('summernote/summernote.min.js')?>
<?=$_->css('summernote/summernote.css')?>

<form method="post">
    <fieldset>


        <div class="form-group">
            <label for="desc"><?=$_->l('Описание')?></label>
            <textarea name="desc" placeholder=""
                      class="form-control"><?=$pconfig->desc?></textarea>
        </div>

        <div class="form-group">
            <label for="desc"><?=$_->l('Шаблон счета')?></label>
            <textarea name="invoice" placeholder=""
                      class="form-control"><?=$pconfig->invoice?></textarea>
        </div>
        <script>
            $(function () {
                $('textarea[name=desc]').summernote();
                $('textarea[name=invoice]').summernote();
            })

        </script>

        <div class="form-group">
            <label><?=$_->l('Использовать валюту для рассчетов')?></label>
            <select class="form-control" name="currency">
                <?foreach ($currencies as $currency){?>
                    <option <?=$pconfig->currency == $currency->id ? 'selected="selected"' : ''?> value="<?=$currency->id?>"><?=$currency->name?></option>
                <?}?>
            </select>
        </div>

        <div class="checkbox">
            <label>
                <input name="on-act" value="1" <?=($pconfig->act_on ? 'checked="checked"' : '')?> type="checkbox"> <?=$_->l('Разрешить генерацию акта выполненых работ')?>
            </label>
        </div>
        <div class="form-group act-form" style="display: none">
            <label for="act" ><?=$_->l('Шаблон акта')?></label>
            <textarea name="act" placeholder=""
                      class="form-control"><?=$pconfig->act?></textarea>
        </div>
        <script>

                $('input[name=on-act]').on('change', function () {
                   if($(this).is(':checked')){
                        $('.act-form').show();
                   }else{
                       $('.act-form').hide();
                   }
                });
                $(function () {
                    $('textarea[name=act]').summernote();

                });
                if($('input[name=on-act]').is(':checked')){
                    $('.act-form').show();
                }else{
                    $('.act-form').hide();
                }

        </script>



        <button type="submit" class="btn btn-primary"><?= $_->l('Сохранить') ?></button>
    </fieldset>
</form>