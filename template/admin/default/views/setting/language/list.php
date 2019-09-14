<div class="top-menu">
    <a href="<?= $_->link('admin/settings/language/add') ?>" class="btn btn-default"><span
            class="glyphicon glyphicon-ok" aria-hidden="true"></span><?=$_->l('Добавить')?></a>



    <div class="pull-right">
        <a href="<?= $_->link('admin/settings/languages/settings') ?>" class="btn btn-default"><span
                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Настройки')?></a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th><?=$_->l('Язык')?></th>
        <th><?=$_->l('Действие')?></th>
    </tr>
    </thead>
    <tbody>
    <?foreach($languages as $lang){?>
    <tr>
        <td><?=$lang->id?></td>
        <td><?=$lang->name?></td>
        <td>
            <?if($lang->id >1){?>
                <a href="<?=$_->link('admin/settings/language/add?id_lang='.$lang->id)?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-edit"></span> <?=$_->l('Редактировать')?></a>
                <a href="<?=$_->link('admin/settings/languages/translate-manager?id_lang='.$lang->id)?>" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-random"></span> <?=$_->l('Менеджер перевода')?></a>
                <a href="<?=$_->link('admin/settings/language/remove?id_lang='.$lang->id)?>" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> <?=$_->l('Удалить')?></a>
            <?}?>
        </td>
    </tr>
    <?}?>
    </tbody>
</table>