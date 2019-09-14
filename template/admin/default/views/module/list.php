<table class="table">
    <thead>
        <tr>
            <th><?=$_->l('Название')?></th>
            <th><?=$_->l('Автор')?></th>
            <th><?=$_->l('Действие')?></th>
        </tr>
    </thead>
    <tbody>

        <?foreach ($modules as $module){ ?>
            <tr  <?=$module->status == 1 ? 'class="success"' : ''?>>
                <td><?=$module->name?></td>
                <td><?=$module->author?></td>
                <td>
                    <?if($module->module->paid){?>
                        <a href="" class="btn btn-xs btn-inf"><span class="glyphicon glyphicon-shopping-cart"></span> <?=$_->l('Купить')?></a>
                    <?}else {?>
                        <?if($module->status == 0){?>
                            <a href="<?=$_->link('admin/modules/install?module_id='.$module->id)?>" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-play-circle"></span> <?=$_->l('Установить')?></a>
                        <?} else {?>
                            <a href="<?=$_->link('admin/modules/uninstall?module_id='.$module->id)?>" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span> <?=$_->l('Деинсталировать')?></a>
                        <? } ?>
                        <?if($module->status ==1 && $module->has_setting_page){?>
                            <a href="<?=$_->link('admin/modules/setting/'.$module->system_name)?>" class="btn btn-xs btn-warning"><span class="glyphicon glyphicon-cog"></span> <?=$_->l('Настройки')?></a>
                        <?} ?>
                    <?}?>
                </td>
            </tr>
        <?}?>
    </tbody>
</table>