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
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Услуга')?></h4>
                </div>
                <div class="modal-body">

                    <? } ?>
                    <div class="top-tabs">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=$_->l('Основные');?></a></li>
                            <li role="presentation"><a href="#fields" aria-controls="fields" role="tab" data-toggle="tab"><?=$_->l('Дополнительны поля')?></a></li>
                            <li role="presentation"><a href="#events" aria-controls="events" role="tab" data-toggle="tab"><?=$_->l('События')?></a></li>
                        </ul>
                    </div>
                    <form action="<?= $_->link('admin/services/edit?id_service=' . ($service->id ? $service->id : '')) ?>"
                          method="POST" class="<?= (isset($ajax) ? 'ajax-form' : '') ?> validate-form">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">


                                <input type="hidden" name="fields" value="">
                                <input type="hidden" name="ajax" value="1">
                                <input type="hidden" name="form" value="home">

                        <div class="form-group">
                            <label for="username"><?=$_->l('Название')?></label>
                            <input type="text" name="name" value="<?= $service->name ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>
                                <div class="form-group">
                                    <label for="username"><?=$_->l('Тип услуги')?></label>
                                    <select name="type" class="form-control">
                                        <option value="0" <?=$service->type == 0 ? 'selected="selected"' : ''?>><?=$_->l("Месячная оплата")?></option>
                                        <option value="1" <?=$service->type == 1 ? 'selected="selected"' : ''?>><?=$_->l("Единоразовая услуга")?></option>
                                    </select>
                                </div>

                        <div class="form-group">
                            <label for="username"><?=$_->l('Цена')?></label>
                            <input type="number" name="price" value="<?= $service->price ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div class="form-group">
                            <label for="username"><?=$_->l('Категория')?></label>
                            <select class="form-control" name="category_id">
                                <? foreach($categories as $category){ ?>
                                    <option value="<?=$category->id?>" <?=($category->id == $service->category_id ? 'selected="selected"' : '')?>><?=$category->name?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username"><?=$_->l('Описание')?></label>
                            <textarea name="description" placeholder=""
                                   class="form-control" data-validate="required"><?= $service->description ?></textarea>
                        </div>



                        </div>
                        <div role="tabpanel" class="tab-pane" id="fields">
                            <div class="fields">
                                <table class="table">

                                </table>
                            </div>

                            <button id="add-field" class="btn btn-default btn-xs"><span
                                    class="glyphicon glyphicon-plus"></span> <?= $_->l('Добавить') ?></button>

                            <div class="add-panel" style="display: none;">

                                    <div class="form-group">
                                        <label for="field_name"><?=$_->l('Название')?></label>
                                        <input type="text" placeholder="" id="field_name"
                                               class="form-control" >
                                    </div>
                                    <div class="form-group">
                                        <label for="field_type"><?=$_->l('Тип поля')?></label>
                                        <select id="field_type" class="form-control">
                                            <option value="1"><?=$_->l("Текстовое поле")?></option>
                                            <option value="2"><?=$_->l("Пароль")?></option>
                                            <option value="3"><?=$_->l("Текстовая область")?></option>
                                            <option value="4"><?=$_->l("Поле выбора")?></option>
                                            <option value="5"><?=$_->l("Ползунок (rangepicker)")?></option>
                                        </select>
                                    </div>
                                    <script>
                                        $('#field_type').on('change', function () {
                                                if($(this).val() == 4){
                                                    $('div.field_values').show();
                                                }
                                                else {
                                                    $('div.field_values').hide();
                                                }

                                            if($(this).val() == 5){
                                                $('div.range_values').show();
                                            }
                                            else {
                                                $('div.range_values').hide();
                                            }
                                        })
                                    </script>
                                    <div class="form-group field_values" style="display: none">
                                        <label for="field_values"><?=$_->l('Варианты для выбора')?></label>
                                        <textarea placeholder="value1[-100]|value2|value3[+200]" id="field_values"
                                               class="form-control" ></textarea>
                                    </div>
                                <div class="form-group range_values" style="display: none">
                                    <label for="range_values"><?=$_->l('Варианты для выбора')?></label>
                                    <textarea placeholder="0|1[+10]|..|25" id="range_values"
                                              class="form-control" ></textarea>
                                </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input id="field_required" type="checkbox"> <?=$_->l('Обязательное поле')?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-default btn-sm btn-primary add-new"><span
                                                class="glyphicon glyphicon-plus"></span> <?= $_->l('Добавить') ?>
                                        </button>
                                    </div>

                            </div>

                            <script>
                                var fields = [];

                                <? foreach($service_fields as $field){?>

                                fields.push( {
                                    id :   '<?=$field->id?>',
                                    name :   '<?=$field->name?>',
                                    type :  '<?=$field->type?>',
                                    required : <?=$field->validate == 'required' ? 'true' : 'false' ?>,
                                    values :  '<?=$field->values?>'
                                });
                                <?
                                    $field_type = '';
                                    if($field->type == 1){
                                        $field_type = $_->l('Текстовое поле');
                                    } else if($field->type == 2){
                                        $field_type = $_->l('Пароль');
                                    }else if($field->type == 3){
                                        $field_type = $_->l('Текстовая область');
                                    }else if($field->type == 4){
                                        $field_type = $_->l('Поле выбора');
                                    }else if($field->type == 5){
                                        $field_type = $_->l('Ползунок (rangepicker)');
                                    }
                                ?>
                                $('.fields table').append('<tr data-id="'+fields.length+'">' +
                                    '<td> <?=$field->name?> </td>' +
                                    '<td> <?=$field_type?> </td>' +
                                    '<td>' + '<button class="btn btn-xs btn-danger remove_field">Удалить</button>' + '</td>' +
                                    '</tr>'
                                );

                                <? } ?>
                                $('input[name=fields]').val(JSON.stringify(fields));
                                console.log(fields);

                                $('#add-field').on('click', function () {
                                    $('.add-panel').toggle();
                                    //$('.fields').after('<input class="form-control">');
                                    return false;
                                });

                                $('body').on('click', '.remove_field', function(){
                                   var id = $(this).parents('tr').data('id');
                                    fields.splice(id-1, 1, {});
                                    console.log(fields);
                                    $('input[name=fields]').val(JSON.stringify(fields));
                                    $(this).parents('tr').remove();
                                });

                                $('button.add-new').on('click', function () {

                                    var $form =$(this).parents('div#fields');


                                    fields.push( {
                                        id :   '',
                                        name :   $form.find('input#field_name').val(),
                                        type :  $form.find('select#field_type').val(),
                                        required : $form.find('input#field_required').prop('checked'),
                                        values: $form.find('select#field_type').val() == 4 ? $form.find('#field_values').val() : $form.find('select#field_type').val() == 5 ? $form.find('#range_values').val() : ''
                                    });

                                    $('.fields table').append('<tr data-id="'+fields.length+'">' +
                                        '<td>' + $form.find('input#field_name').val() + '</td>' +
                                        '<td>' + $form.find('select#field_type option:selected').text() + '</td>' +
                                        '<td>' + '<button class="btn btn-xs btn-danger remove_field"><?=$_->l("Удалить")?></button>' + '</td>' +
                                        '</tr>'
                                    );


                                    $('input[name=fields]').val(JSON.stringify(fields));


                                    return false;
                                })
                            </script>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="events">



                                <div class="form-group">
                                    <label for="username"><?=$_->l('Создание услуги')?></label>
                                    <input type="text" name="event_create" value="<?= $service->event_create ?>" placeholder=""
                                           class="form-control" data-validate="">
                                </div>

                                <div class="form-group">
                                    <label for="username"><?=$_->l('Продление услуги')?></label>
                                    <input type="text" name="event_prolong" value="<?= $service->event_prolong ?>" placeholder=""
                                           class="form-control" data-validate="">
                                </div>

                                <div class="form-group">
                                    <label for="username"><?=$_->l('Отключение услуги')?></label>
                                    <input type="text" name="event_end" value="<?= $service->event_end ?>" placeholder=""
                                           class="form-control" data-validate="">
                                </div>




                                <hr>




                        </div>
                    </div>
    </form>

                    <? if (isset($ajax)) { ?>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <button type="button" class="btn btn-primary submit-active-form"><?=$_->l('Сохранить')?></button>
                    <script>
                        $(function(){
                            $('.submit-active-form').on('click', function () {

                                  $('form.ajax-form').submit();

                            })
                        })

                    </script>
                </div>
            </div>
        </div>
    </div>
<? } ?>
</div>
