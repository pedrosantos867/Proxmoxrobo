<div class="loaded-block">

    <?= $_->JS('validator.js') ?>
    <script>
        $(function () {
            $('form').validate({messages: validate_messages});

        })
    </script>

    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Пользователь')?></h4>
                </div>
                <div class="modal-body">

                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><?=$_->l('Текущий баланс')?></th>
                        <th><?=$_->l('Изменить на')?></th>
                        <th><?=$_->l('Новый баланс')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="text-center">
                        <td><?=$balance?></td>
                        <td>
                            <span class="blance-symbol">+</span>
                            <span class="balance-change-on">0</span>
                        </td>
                        <td><span class="balance-new-val"><?=$balance?></span></td>
                    </tr>
                    </tbody>


                </table>
                    <form method="post" action="<?=$_->link('admin/client/edit-balance/') . $user_id?>" class="ajax-form">
                        <input type="hidden" name="ajax" value="1">
                        <div class="form-group">
                            <label>
                                <?=$_->l('Тип операции')?>
                            </label>
                            <select name="change_type" class="form-control">
                                <option  value="plus">
                                    <?=$_->l('Пополнение')?>
                                </option>
                                <option  value="minus">
                                    <?=$_->l('Вычет')?>
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= $_->l('Сумма'); ?></label>
                            <input name="value" id="balance-change-on-sum" type="number" class="form-control"
                                   value="0" data-validate="possitive_number|required">
                        </div>
                    </form>
                    <script>
                        var balance = <?=$balance?>;
                        function changeBalance() {
                            var val = $('select[name=change_type').val();
                            var sum = Number($('#balance-change-on-sum').val());
                            var newBalance = 0;
                            if(sum < 0) return;
                            if (val == 'plus'){
                                $('.blance-symbol').html('+');
                                newBalance = balance + sum;
                            }
                            else if(val == 'minus') {
                                $('.blance-symbol').html('-');
                                newBalance = balance - sum;
                            }
                            $('.balance-change-on').html(sum);
                            if ((newBalance % 1) > 0) newBalance = newBalance.toFixed(3);
                            $('.balance-new-val').html(newBalance);
                        }
                        $(document).on('keyup', '#balance-change-on-sum', changeBalance);
                        $(document).on('change', '#balance-change-on-sum', changeBalance);
                        $(document).on('change', 'select[name=change_type]', changeBalance);
                    </script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= $_->l('Закрыть'); ?></button>
                    <button type="button" onclick="$('.ajax-form').submit();"
                            class="btn btn-primary"><?= $_->l('Сохранить'); ?></button>
                </div>
            </div>
        </div>
    </div>

</div>