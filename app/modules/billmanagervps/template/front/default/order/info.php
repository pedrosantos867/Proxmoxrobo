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

                    Доступ к серверу для администратора:
                    <br>
                    <?=($order->ip);?>
                    <br>
                    root
                    <br>
                    <?=($order->password);?>
                    <br>
                    <br>
                    VMmanager — панель управления виртуальным контейнером:
                    <br>
                    <a href="<?=$plan->link?>" target="_blank"><?=$plan->link?></a>
                    <br>
                    Вход в панель: <?=($order->username);?>
                    <br>
                    Пароль: <?=($order->userpassword);?>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                </div>
            </div>
        </div>
    </div>

</div>
