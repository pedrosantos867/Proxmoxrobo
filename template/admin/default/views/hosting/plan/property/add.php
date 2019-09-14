<div class="loaded-block ajax-block">
    <? if (isset($ajax)) { ?>

    <!-- Modal -->
    <div class="modal fade " id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Свойство')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>

                    <? foreach ($messages as $message) { ?>
                        <? if ($message['type'] == 'success' && $message['name'] == 'property_save') { ?>
                            <div class="alert alert-success" role="alert">
                                <?=$_->l('Свойство добавленно!')?>
                            </div>
                        <? } else if ($message['name'] == 'property_isset') { ?>
                            <div class="alert alert-danger" role="alert">
                                <?=$_->l('Свойство существует!')?>
                            </div>
                        <? } ?>
                    <? } ?>

                    <form
                        action="<?= $_->link(($property->id ? 'admin/plan-property/edit/' . $property->id . '/' . $plan->id : 'admin/plan-property/add/' . $plan->id)) ?>"
                        method="POST" <?= (isset($ajax) ? 'class="ajax-form"' : '') ?>>


                        <div class="form-group">

                            <label for="username"><?=$_->l('Выберите параметр')?></label>
                            <select name="id_param" class="form-control">
                    <? foreach ($params as $param) { ?>
                        <option
                            value="<?= $param->id ?>" <?= ($property->param_id == $param->id ? 'selected="selected"' : '') ?> ><?= $param->name ?></option>
                    <? } ?>
                            </select>
        </div>

                        <div class="form-group">
                            <label for="name"><?=$_->l('Значение')?></label>
                            <input type="text" class="form-control" name="param_value"
                       value="<?= ($property->id ? $property->value : '') ?>">
        </div>


                        <? if (!isset($ajax)) { ?>

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

    <script>
        $('.ajax-form').off('submit').on('submit', function (e) {
            loader.display();
            e.preventDefault();
            e.stopPropagation();

            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'post',
                dataType: 'json',
                data: form.serialize(),
                success: function (data) {
                    if (data.result) {
                        createNoty(data['message'], 'success');
                        //   Messenger().post({message: data['message'], type: "success"});
                    } else {
                        createNoty(data['message'], 'danger');
                        //   Messenger().post({message: data['message'], type: "error"});
                    }
                    var tb = (tab);

                    $.ajax({
                        type: 'post',
                        dataType: 'html',
                        data: {order: order, ajax: 1, filter: filter, page: page},
                        success: function (data) {

                            $('.ajax-block').replaceWith(data);


                            $('li.tb.active').removeClass('active');
                          //  $('div.tabpanel.active').removeClass('active');
                            tb = '#tab_'+tb+' a';

                            $(tb).addClass('active').trigger('click');
                            $('#ajaxModal').modal('hide');

                            loader.hide();
                        }
                    })


                    //$('.loaded-block').remove();
                }
            })

        })
    </script>
<? } ?>


</div>

