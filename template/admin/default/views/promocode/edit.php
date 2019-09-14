<div class="loaded-block">

    <style>
        .sale_type_item{display: none}
        .form-group-chackbox label{
            display: inline-block;
            margin-bottom: 0;
            margin-right: 12px;
        }
    </style>

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
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Промокод')?></h4>
                </div>
                <div class="modal-body">
                    <? } ?>

                    <form action="<?=$_->link("/admin/promocodes/edit?promocode_id=" . $promocode->id  )?>"
                          method="POST" class="<?= (isset($ajax) ? 'ajax-form' : '') ?> validate-form">

                        <div class="form-group">
                            <label><?=$_->l('Название')?></label>
                            <input type="text" name="name" value="<?= $promocode->name ?>" placeholder=""
                                   class="form-control" data-validate="required">
                        </div>

                        <div class="form-group code-generate-inner">
                            <label><?=$_->l('Код')?></label>
                            <input type="text" name="code" value="<?= $promocode->code ?>" placeholder=""
                                   class="form-control code-generate-inp"  data-validate="required|ajax" <?= $promocode->code ? 'data-validate-def="'.$promocode->code.'"' : '' ?>
                                   data-validate-message-fail-code="<?=$_->l('Такой код уже существует')?>"
                            >
                            <span class="code-generate generate-rand" data-input=".code-generate-inp" data-toggle="tooltip"
                                  data-placement="left" title="<?=$_->l('Сгенерировать код')?>">
                                <span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>
                            </span>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip()
                                })
                            </script>
                        </div>

                        <div class="form-group">
                            <label><?=$_->l('Тип скидки')?></label>
                            <select id="sale_type" name="sale_type" class="form-control" data-validate="required">
                                <option value="1"
                                    <?if($promocode->sale_type) echo "selected"?>>
                                    <?=$_->l('Процент')?>
                                </option>
                                <option value="0"
                                    <?if(!$promocode->sale_type) echo "selected"?>>
                                    <?=$_->l('Сумма')?>
                                </option>
                            </select>
                        </div>

                        <div id="sale_type_percent" class="form-group sale_type_item">
                            <label><?=$_->l('Скидка')?> (%)</label>
                            <input type="number" name="sale" value="<?= $promocode->sale ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div id="sale_type_sum" class="form-group sale_type_item">
                            <label><?=$_->l('Скидка')?> (<?=$_->l('Сумма')?>)</label>
                            <input type="number" name="sale" value="<?= $promocode->sale ?>" placeholder=""
                                   class="form-control" data-validate="required"
                            >
                        </div>

                        <div class="form-group">
                            <label><?=$_->l('Количество применений')?></label>
                            <input type="number" name="total_count" value="<?= $promocode->total_count ?>" placeholder=""
                                   class="form-control"  data-validate="required">
                        </div>
                        <?= $_->JS('momentjs/moment.min.js') ?>
                        <?if($lang->iso_code != 'en'){?>
                            <?= $_->JS('momentjs/locale/'.$lang->iso_code.'.js') ?>
                        <?}?>

                        <div class="form-group">
                            <label><?=$_->l('Дата окончания')?></label>
                            <input type="text" name="end_date" value="<?= $promocode->end_date ?>" placeholder=""
                                   class="form-control"  data-validate="required|date">
                        </div>
                        
                        <script>
                            $('input[name="end_date"]').inputmask('y-m-d');
                            function select_type() {
                                if($('#sale_type').val() == 1) {
                                    $('#sale_type_percent').show();
                                    $('#sale_type_percent input').attr("name", "sale");
                                    $('#sale_type_sum').hide();
                                    $('#sale_type_sum  input').attr("name", "sale_");
                                }else{
                                    $('#sale_type_percent').hide();
                                    $('#sale_type_percent input').attr("name", "sale_");
                                    $('#sale_type_sum').show();
                                    $('#sale_type_sum input').attr("name", "sale");
                                }
                                $('.validate-form').validate({messages: validate_messages});
                            }
                            select_type();
                            $('#sale_type').on('change', select_type)
                        </script>


                        <?if(isset($ServiceCategories)){?>
                            <?$p = [];
                            foreach ($pSCs as $pSC){
                                $p[] = $pSC->service_category_id;
                            }
                            ?>
                            <div class="form-group form-group-chackbox">
                                <label><input type="checkbox" <?=(in_array('-1', $p) ? 'checked="checked"' : '') ?> name="service[-1]">
                                <?=$_->l('Хостинг')?></label>
                                <label><input type="checkbox" <?=(in_array('-2', $p) ? 'checked="checked"' : '') ?>  name="service[-2]">
                                <?=$_->l('Домен')?></label>
                                <label><input type="checkbox" <?=(in_array('-3', $p) ? 'checked="checked"' : '') ?>  name="service[-3]">
                                <?=$_->l('VPS')?></label>
                            <?foreach ($ServiceCategories as $serviceCategory){?>
                                    <label><input type="checkbox" <?=(in_array($serviceCategory->id, $p) ? 'checked="checked"' : '') ?> name="service[<?=$serviceCategory->id?>]">
                                    <?=$serviceCategory->name?></label>
                            <?}?>
                            </div>
                        <?}?>
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
    <script>
        console.log($('div.form-group:first-child').find('input, select, textarea').is(':visible'));
    </script>

</div>