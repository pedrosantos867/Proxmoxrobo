<? if(isset($info->next)){ ?>
    <div>
        <div>
            <?=$_->l('Текущая версия:')?> <?= $config->app_version ?> <br>
            <?=$_->l('Доступная версия:')?> <?= $info->next ?>
        </div>
    </div>
    <?if(!$canUpdate){?>
        <div class="alert alert-danger" role="alert">
            <?=$_->l('Web-сервер не имеет прав для записи в директорию биллинга, для автоматического обновления вы должны установите рекурсивные права на запись для Web-сервера в директорию биллинга!')?>
        </div>
    <?}?>

    <?if($info->description){?>
        <br>
        <div class="panel" style="padding: 15px">
            <?=$info->description?>
        </div>
    <?}?>

    <? if ($config->app_version != $info->next) { ?>
        <br>
        <div>
            <form method="post">
                <input type="hidden" name="update" value="1">
                <button <?=!$canUpdate ? 'disabled="disabled"' : ''?> class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span> <?=$_->l('Обновить')?></button>
            </form>
        </div>
    <? } ?>
<? } else { ?>
    <div class="alert alert-warning">
        <span class="glyphicon glyphicon-warning-sign"></span>
        <?=$_->l('Не удалось загрузить информацию. Попробуйте повторить запрос немного позже.')?>
    </div>
<? } ?>



