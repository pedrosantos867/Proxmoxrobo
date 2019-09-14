<div class="row text-center" style="margin-top: 25px">
    <div class="col-md-12">

        <div class="btn-group-vertical" style="width: 240px" role="group" aria-label="Vertical button group">
                <?php foreach($languages as $lang){?>
                    <a href="<?=$_->link('install?step=1&id_lang='.$lang->id)?>" class="btn btn-default">
                        <img src="<?= $_->link('storage/i18n/flags/' . $lang->iso_code . '.png'); ?>" style="    float: left;
    height: 25px;">
                    <?=$lang->name;?> </a>
                <?php } ?>
            </div>

    </div>
</div>