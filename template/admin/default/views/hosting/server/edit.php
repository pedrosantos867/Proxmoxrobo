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
                    <form action="<?= $_->link('admin/server/' . ($server->id ? $server->id : 'add')) ?>"
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
                            <label for="ip"><?=$_->l('Ссылка доступа к панели клиента')?></label>
                            <input type="text" name="ip" value="<?= $server->ip ?>" placeholder="<?=$_->l('Чтобы использовать адрес панели по умолчанию, оставьте это поле пустым.')?>"
                                   class="form-control" data-validate="custom"
                                   data-validate-match="((http|https):\/\/([a-z0-9.-]{0,})([:][0-9]{0,4}|^$)|^$)"
                                   data-validate-message-fail-custom="<?=$_->l('URL адрес хостинг панели (ссылка будет доступная клиентам в подробностях о заказе). Например: https://mypanel.com:8080')?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="login"><?=$_->l('Хостинг панель')?></label>
                            <select name="panel" class="form-control" data-validate="required">
                                <option <?= $server->panel == \model\HostingServer::PANEL_VESTA ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_VESTA ?>">VestaCP
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_ISP4 ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_ISP4 ?>">ISP Manager 4
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_ISP ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_ISP ?>">ISP Manager 5
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_CPANEl ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_CPANEl ?>">cPanel
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_PLESK ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_PLESK ?>">Plesk
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_DIRECTADMIN ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_DIRECTADMIN?>">Direct Admin
                                </option>
                                <option <?= $server->panel == \model\HostingServer::PANEL_ISPCONFIG ? 'selected="selected"' : '' ?>
                                    value="<?= \model\HostingServer::PANEL_ISPCONFIG?>">ispConfig
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="login"><?=$_->l('Логин')?></label>
                            <input type="text" name="login" value="<?= $server->login ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div class="form-group">
                            <label for="pass"><?=$_->l('Пароль')?></label>

                            <input type="text" name="pass" data-validate="required" value="<?= $server->pass ?>"
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
