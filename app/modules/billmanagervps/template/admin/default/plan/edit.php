<div class="loaded-block">

    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('.validate-form').validate({messages: validate_messages});
        })
    </script>



    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Тариф')?></h4>
                </div>
                <div class="modal-body">


                    <form action="<?=$_->link('admin/modules/billmanagervps/plans/edit?plan_id=' . $plan->id)?>"
                          method="POST" class="<?= (isset($ajax) ? 'ajax-form' : '') ?> validate-form">

                        <div class="form-group">
                            <label><?=$_->l('Название')?></label>
                            <input type="text" name="name" value="<?= $plan->name ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div  class="form-group ">
                            <label><?=$_->l('Цена')?> </label>
                            <input type="number" name="price" value="<?= $plan->price ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div  class="form-group ">
                            <label><?=$_->l('Описание')?> </label>
                            <input type="text" name="description" value="<?= $plan->description ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div  class="form-group ">
                            <label><?=$_->l('Ссылка доступа')?> </label>
                            <input type="text" name="link" value="<?= $plan->link ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div  class="form-group ">
                            <label><?=$_->l('Billmanager pricelist')?> </label>
                            <input type="number" name="pricelist" value="<?= $plan->pricelist ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div class="form-group">
                            <label><?=$_->l('Billmanager addons')?> </label>
                            <textarea name="additions" class="form-control"><?= $plan->additions ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><?=$_->l('Billmanager templates')?> </label>
                            <textarea name="templates" class="form-control"><?= $plan->templates ?></textarea>
                        </div>


                            <input type="hidden" name="ajax" value="1">

                    </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="button" onclick="$('.ajax-form').submit();" class="btn btn-primary"><?=$_->l('Сохранить')?></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log($('div.form-group:first-child').find('input, select, textarea').is(':visible'));
    </script>

</div>