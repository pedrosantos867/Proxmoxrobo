<div class="loaded-block">
    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form method="post" action="<?= $_->link('admin/service-orders/info?id_order='.$order->id) ?>" class="modal-content ajax-form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Добавление информации')?></h4>
                </div>
                <?=$_->js('summernote/summernote.min.js')?>
                <?=$_->css('summernote/summernote.css')?>
                <div class="modal-body">
                    <input type="hidden" name="ajax" value="1">
                    <textarea class="form-control" rows="8" placeholder="<?=$_->l('Добавьте информацию о заказе для клиента')?>" name="admin_info"><?= $order->admin_info ?></textarea>
                    <script>
                        $('textarea[name=admin_info]').summernote();
                    </script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="submit" class="btn btn-success"><?=$_->l('Сохранить')?></button>
                </div>
            </form>
        </div>
    </div>

</div>
