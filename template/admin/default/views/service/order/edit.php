<div class="loaded-block">

    <? if (isset($ajax)) { ?>
    <!-- Modal -->
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $_->l('Редактирование услуги') ?></h4>
                </div>
                <div class="modal-body">
                    <? } ?>
                    <form action="<?= $_->link('admin/service-orders/edit?id_service_order=' . $order->id) ?>"
                          class="ajax-form"
                          method="post">
                        <div class="form-group">
                            <label for="status"><?= $_->l('Статус') ?></label>
                            <select name="status" class="form-control">
                                <option <?= $order->status == 1 && $order->type == 0 ? 'selected="selected"' : '' ?>
                                        value="10"><?= $_->l('Активный') ?></option>
                                <option <?= $order->status == 0 && $order->type == 0 ? 'selected="selected"' : '' ?>
                                        value="00"><?= $_->l('Отключен') ?></option>
                                <option <?= $order->status == 1 && $order->type == 1 ? 'selected="selected"' : '' ?>
                                        value="11"><?= $_->l('Оплачено') ?></option>
                                <option <?= $order->status == 0 && $order->type == 1 ? 'selected="selected"' : '' ?>
                                        value="01"><?= $_->l('Не оплачено') ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paid_to"><?= $_->l('Конец') ?></label>
                            <input type="text" class="form-control" name="paid_to"
                                   value="<?= $order->paid_to ?>">
                        </div>
                        <? if (!isset($ajax)) { ?>
                            <button type="submit" class="btn btn-success"><?= $_->l('Сохранить') ?></button>
                        <? } else { ?>
                            <input type="hidden" name="ajax" value="1">
                        <? } ?>
                    </form>

                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $_->l('Закрыть') ?></button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-success"><span
                                class="glyphicon glyphicon-floppy-disk"></span> <?= $_->l('Сохранить') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>