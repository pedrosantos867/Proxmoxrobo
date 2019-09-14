<div class="loaded-block">
    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Информация о заказе')?></h4>
                </div>
                <div class="modal-body">

                    <?if(!$order->admin_info){?>
                       <?=$_->l('Нет дополнительной информации')?>
                    <?}else{?>
                        <div><?=$order->admin_info?></div>
                    <?}?>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                </div>
            </div>
        </div>
    </div>

</div>
