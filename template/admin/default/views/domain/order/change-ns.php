<div class="loaded-block">
    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('form').validate({messages: validate_messages});

        })
    </script>
    <? if (isset($ajax)) { ?>

    <!-- Modal -->
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center" id="myModalLabel"><?=$_->l('Смена NS серверов')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <form action="<?= $_->link($request) ?>" class="ajax-form"
                          method="post">
                        <? if (isset($ajax)) { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                        <? if (isset($order->id)) { ?>
                            <input type="hidden" name="id" value="<?= $order->id ?>">
                        <? } ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dns1">NS1</label>
                                    <input type="text" class="form-control" name="dns1" data-validate="domain"
                                           value="<?= $order->dns1 ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dns2">NS2</label>
                                    <input type="text" class="form-control" name="dns2" data-validate="domain"
                                           value="<?= $order->dns2 ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dns3">NS3</label>
                                    <input type="text" class="form-control" name="dns3" data-validate="domain"
                                           data-validate-allow-empty="1"
                                           value="<?= $order->dns3 ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dns4">NS4</label>
                                    <input type="text" class="form-control" name="dns4" data-validate="domain"
                                           data-validate-allow-empty="1"
                                           value="<?= $order->dns4 ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ip1">IP</label>
                                    <input type="text" class="form-control" name="ip1"
                                           value="<?= '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="ip2">IP</label>
                                    <input type="text" class="form-control" name="ip2"
                                           value="<?= '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="ip3">IP</label>
                                    <input type="text" class="form-control" name="ip3"
                                           value="<?= '' ?>">
                                </div>
                                <div class="form-group">
                                    <label for="ip4">IP</label>
                                    <input type="text" class="form-control" name="ip4"
                                           value="<?= '' ?>">
                                </div>
                            </div>


                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><span
                                    class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?>
                            </button><? } ?>
                    </form>


                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="button" onclick="$('#ajaxModal form').submit();" class="btn btn-success"><span
                            class="glyphicon glyphicon-floppy-disk"></span> <?=$_->l('Сохранить')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

<? } ?>
</div>