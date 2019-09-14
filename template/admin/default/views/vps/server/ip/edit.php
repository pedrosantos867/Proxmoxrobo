<div class="loaded-block">
    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {

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
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('IP адрес')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link('admin/vps-ips/edit?ip_id=' . ($ip->id ? $ip->id : '')) ?>"
                          method="POST" class="<?= (isset($ajax) ? 'ajax-form' : '') ?> validate-form">

                        <input type="hidden" name="server_id" value="<?= $server->id ?>">


                        <div class="form-group">
                            <label class="control-label"><?=$_->l('Тип')?></label>
                            <? if ($server->type == \model\VpsServer::PANEL_PROXMOX) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type"
                                               value="0" <?= $ip->type == 0 ? 'checked="checked"' : '' ?> >
                                        <?= $_->l('VLAN dhcp') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type"
                                               value="1" <?= $ip->type == 1 ? 'checked="checked"' : '' ?> >
                                        <?= $_->l('Static IPv4') ?>
                                    </label>
                                </div>
                            <? } ?>
                            <? if ($server->type == \model\VpsServer::PANEL_VMMANAGER) { ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type"
                                               value="2" <?= $ip->type == 2 ? 'checked="checked"' : '' ?> >
                                        <?= $_->l('NAT Static IPv4') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type"
                                               value="3" <?= $ip->type == 3 ? 'checked="checked"' : '' ?> >
                                        <?= $_->l('Private Static IPv4') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="type"
                                               value="4" <?= $ip->type == 4 ? 'checked="checked"' : '' ?> >
                                        <?= $_->l('Public Static IPv4') ?>
                                    </label>
                                </div>
                            <? } ?>
                        </div>

                        <script>
                            function onload() {

                                <?if(!$ip->id){?>
                                $('input[name=type]:first').attr('checked', 'checked');
                                <?}?>

                                if ($('input[name=type]:checked').val() == 1 ||
                                    $('input[name=type]:checked').val() == 2 ||
                                    $('input[name=type]:checked').val() == 3 ||
                                    $('input[name=type]:checked').val() == 4
                                ) {
                                    $('div.ipv4-group').show();
                                    $('div.mask-group').show();
                                    $('div.gateway-group').show();
                                    $('div.vlan-group').hide();
                                } else {
                                    $('div.ipv4-group').hide();
                                    $('div.gateway-group').hide();
                                    $('div.mask-group').hide();
                                    $('div.vlan-group').show();
                                }
                                $('.validate-form').validate({messages: validate_messages});
                            }

                            $('input[name=type]').on('change', function () {
                                onload();
                            });

                            $(function () {
                                onload();
                            })
                        </script>

                        <div class="form-group ipv4-group">
                            <label for="host"><?=$_->l('Адрес')?></label>
                            <input type="text" name="ip" value="<?= $ip->ip ?>" placeholder=""
                                   class="form-control" data-validate="custom" data-validate-match="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" data-validate-message-fail-custom="<?=$_->l('Ведите IP адрес (например 192.168.1.1)')?>">
                        </div>
                        <? if ($server->type == \model\VpsServer::PANEL_PROXMOX) { ?>
                        <div class="form-group mask-group">
                            <label for="host"><?=$_->l('Маска')?></label>
                            <input type="text" name="mask" value="<?= $ip->mask ?>" placeholder=""
                                   class="form-control" data-validate="custom" data-validate-match="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" data-validate-message-fail-custom="<?=$_->l('Ведите IP адрес (например 255.255.255.0)')?>">
                        </div>

                        <div class="form-group gateway-group">
                            <label for="gateway"><?=$_->l('Шлюз')?></label>
                            <input type="text" name="gateway" value="<?= $ip->gateway ?>" placeholder=""
                                   class="form-control" data-validate="custom" data-validate-match="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" data-validate-message-fail-custom="<?=$_->l('Ведите IP адрес (например 192.168.1.1)')?>">
                        </div>
                        <? } ?>

                        <div class="form-group vlan-group">
                            <label for="username"><?=$_->l('VLAN Tag')?></label>
                            <input type="number" min="1" name="vlan" value="<?= $ip->vlan ? $ip->vlan : 1 ?>" placeholder=""
                                   class="form-control" data-validate="required"  >
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
