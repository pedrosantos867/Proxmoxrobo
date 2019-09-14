<div class="loaded-block">
    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Сменить владельца')?></h4>
                </div>
                <div class="modal-body">
                    <?= $_->js('select2/select2.min.js') ?>
                    <?= $_->js('select2/i18n/ru.js') ?>
                    <?= $_->css('select2/select2.min.css') ?>
                   <form class="ajax-form" method="POST" action="<?=$_->link('admin/domain-orders/change-owner')?>">
                    <div class="form-group">
                        <input type="hidden" name="ajax" value="1">
                        <input type="hidden" name="id_order" value="<?=\System\Tools::rGET('id_order')?>">
                        <select name="owner_id" class="form-control">
                            <?foreach($owners as $owner){?>
                                <option
                                    value="<?= $owner->id ?>"><?= $owner->type == 2 ? $owner->organization_name : $owner->fio ?>
                                    (ID: <?= $owner->id ?>)
                                </option>
                            <?}?>
                        </select>
                        <script type="text/javascript">

                            $("select[name=owner_id]").select2({
                                width: '100%',
                                minimumResultsForSearch: Infinity
                            });
                        </script>
                    </div>
                   </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" onclick="$('.ajax-form').submit();" class="btn btn-primary" ><?=$_->l('Сохранить')?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                </div>
            </div>
        </div>
    </div>

</div>
