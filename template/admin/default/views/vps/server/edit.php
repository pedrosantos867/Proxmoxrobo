<div class="loaded-block">
    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('.validate-form').validate({messages: validate_messages});
        })
    </script>
    <? if (isset($ajax)) { ?>

    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Сервер')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link('admin/vps-servers/edit?server_id=' . ($server->id ? $server->id : '')) ?>"
                          method="POST" class="<?= (isset($ajax) ? 'ajax-form' : '') ?> validate-form">


                        <div class="form-group">
                            <label for="username"><?=$_->l('Название')?></label>
                            <input type="text" name="name" value="<?= $server->name ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div class="form-group">
                            <label for="host"><?=$_->l('Адрес')?></label>
                            <input type="text" name="host" value="<?= $server->host ?>" placeholder=""
                                   class="form-control" data-validate="custom"
                                   data-validate-match="(http|https):\/\/(.*):[0-9]{0,4}"
                                   data-validate-message-fail-custom="<?=$_->l('URL адрес хостинг панели. Например: https://mypanel.com:8080')?>"

                                >
                        </div>

                        <div class="form-group">
                            <label for="type"><?=$_->l('VPS панель')?></label>
                            <select name="type" class="form-control" data-validate="required">
                                <option <?= $server->type == \model\VpsServer::PANEL_PROXMOX ? 'selected="selected"' : '' ?>
                                    value="<?= \model\VpsServer::PANEL_PROXMOX ?>">ProxMox
                                </option>
                                <option <?= $server->type == \model\VpsServer::PANEL_VMMANAGER ? 'selected="selected"' : '' ?>
                                    value="<?= \model\VpsServer::PANEL_VMMANAGER ?>">VMmanager
                                </option>
                                <option disabled="disabled" <?= $server->type == \model\VpsServer::PANEL_HYPERVM ? 'selected="selected"' : '' ?>
                                    value="<?= \model\VpsServer::PANEL_HYPERVM ?>">HyperVM
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="username"><?=$_->l('Логин')?></label>
                            <input type="text" name="username" value="<?= $server->username ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div class="form-group">
                            <label for="password"><?=$_->l('Пароль')?></label>

                            <input type="text" name="password" data-validate="required" value="<?= $server->password ?>"
                                   placeholder=""
                                   class="form-control">

                        </div>
                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><?=$_->l('Сохранить')?></button>
                        <? } else { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                    </form>
                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>
