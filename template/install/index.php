<?php $ok = true; ?>
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
            <tr>
                <th>
                    <?=$_->l('Параметр')?>
                </th>
                <th>
                    <?=$_->l('Рекомендуемое значение')?>
                </th>
                <th>
                    <?=$_->l('Текущее значение')?>
                </th>
            </tr>
            </thead>
            <tbody>

            <tr <?php echo((phpversion() >= 5.4) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?=$_->l('Версия PHP')?>
                </td>
                <td>
                    >= 5.4
                </td>
                <td>
                    <?php echo phpversion() ?>
                </td>
            </tr>
            <?php $extensions = get_loaded_extensions(); ?>

            <?php $ok = $ok&& (in_array('zip', $extensions) ? $ok : false) ?>
            <tr <?php echo(in_array('zip', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    ZIP Extension
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('zip', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok&& (in_array('pdo_mysql', $extensions) ? $ok : false) ?>
            <tr <?php echo(in_array('pdo_mysql', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    PDO Support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('pdo_mysql', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>
            <?php $ok = $ok&& (in_array('openssl', $extensions) ? $ok : false) ?>
            <tr <?php echo(in_array('openssl', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    Openssl Support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('openssl', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok&& (extension_loaded('gd') && function_exists('gd_info') ? $ok : false) ?>
            <tr <?php echo(extension_loaded('gd') && function_exists('gd_info') ? 'class="success"' : 'class="danger"') ?> >

                <td>
                   PHP GD extension
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if(extension_loaded('gd') && function_exists('gd_info')) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok && (function_exists('curl_version') ? $ok : false) ?>
            <tr <?php echo(function_exists('curl_version') ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    Curl Support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( function_exists('curl_version') ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok && (in_array('soap', $extensions) ? $ok : false) ?>
            <tr <?php echo(in_array('soap', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    Soap Support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('soap', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok && (ini_get('allow_url_fopen') ? $ok : false) ?>
            <tr <?php echo(ini_get('allow_url_fopen') ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    allow_url_fopen
                </td>
                <td>
                    On
                </td>
                <td>

                    <?php echo ini_get('allow_url_fopen') ? 'On' : 'Off'?>
                </td>
            </tr>

            <?php $ok = $ok&& (in_array('fileinfo', $extensions) ? $ok : false) ?>

            <tr <?php echo(in_array('fileinfo', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    fileinfo support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('fileinfo', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok&& (in_array('SimpleXML', $extensions) ? $ok : false) ?>

            <tr <?php echo(in_array('SimpleXML', $extensions) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    SimpleXML Support
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>

                    <?php if( in_array('SimpleXML', $extensions) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>
            <?php $ok = $ok&& (ini_get('short_open_tag') ? $ok : false) ?>
            <tr <?php echo(ini_get('short_open_tag') ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    PHP short tags
                </td>
                <td>
                    <?=$_->l('Включено')?>
                </td>
                <td>

                    <?php if( ini_get('short_open_tag') ) {?> <?=$_->l('Включено')?> <?} else {?> <?=$_->l('Выключено')?> <?}?>
                </td>
            </tr>


            <?php
            $cf = @file_put_contents(\System\Path::getRoot('app/config/chmodtest.tmp'), '');
            $cd = false;

            if($cf !== false){

                if(is_writable(\System\Path::getRoot('app/config/chmodtest.tmp'))){
                    $cd = true;
                } else{
                    $cd = false;
                }

            }
            @unlink(\System\Path::getRoot('app/config/chmodtest.tmp'));
            $ok = $ok && $cd;


            ?>
            <tr <?php echo($cd ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?=$_->l('Директория %dir доступна на запись', array('dir' => 'app/config/'))?>
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>
                    <?php if( $cd ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>

            <?php $ok = $ok&& (is_writable(\System\Path::getRoot('app/config/global.config')) ? $ok : false) ?>
            <tr <?php echo(is_writable(\System\Path::getRoot('app/config/global.config')) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?=$_->l('Файл %file доступен на запись', array('file' => "app/config/global.config"))?>
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>
                    <?php echo is_writable(\System\Path::getRoot('app/config/global.config')) ? $_->l('Да') : $_->l('Нет') ?>
                </td>
            </tr>
            <?php $ok = $ok&& (is_writable(\System\Path::getRoot('app/config/payments.config')) ? $ok : false) ?>
            <tr <?php echo(is_writable(\System\Path::getRoot('app/config/payments.config')) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?=$_->l('Файл %file доступен на запись', array('file' => "app/config/payments.config"))?>
                </td>
                <td>
                    <?=$_->l('Да')?>
                </td>
                <td>
                    <?php echo is_writable(\System\Path::getRoot('app/config/payments.config')) ? $_->l('Да') : $_->l('Нет') ?>
                </td>
            </tr>
            <?php $ok = $ok&& (is_writable(\System\Path::getRoot('app/config/sms-gateway.config')) ? $ok : false) ?>
            <tr <?php echo(is_writable(\System\Path::getRoot('app/config/sms-gateway.config')) ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?= $_->l('Файл %file доступен на запись', array('file' => 'app/config/sms-gateway.config')) ?>
                </td>
                <td>
                    <?= $_->l('Да') ?>
                </td>
                <td>
                     <?php if( is_writable(\System\Path::getRoot('app/config/sms-gateway.config')) ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>
             

            <?php
            $cf = @file_put_contents(\System\Path::getRoot('storage/chmodtest.tmp'), '');
            $cd = false;

            if($cf !== false){

                if(is_writable(\System\Path::getRoot('storage/chmodtest.tmp'))){
                    $cd = true;
                } else{
                    $cd = false;
                }

            }
            @unlink(\System\Path::getRoot('storage/chmodtest.tmp'));
            $ok = $ok && $cd;


            ?>
            <tr <?php echo($cd ? 'class="success"' : 'class="danger"') ?> >

                <td>
                    <?= $_->l('Директория %dir доступна на запись', array('dir' => 'storage')) ?>
                </td>
                <td>
                    <?= $_->l('Да') ?>
                </td>
                <td>
                    <?php if( $cd ) {?> <?=$_->l('Да')?> <?} else {?> <?=$_->l('Нет')?> <?}?>
                </td>
            </tr>




            </tbody>
        </table>
    </div>
</div>
<?php if ($ok) { ?>
    <a href="<?php echo $_->link('install?step=2') ?>" class="btn btn-primary pull-right"><?=$_->l('Далее')?> <span
            class="glyphicon glyphicon-chevron-right"></span></a>
<?php } ?>