<div class="ajax-block">
    <div>
        <a href="<?= $_->link('admin/client/add') ?>" class="btn btn-default ajax-modal"><span
                class="glyphicon glyphicon-ok"
                aria-hidden="true"></span><?=$_->l('Добавить')?></a>


        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="8%">№
                    <div class="sorting">
                        <a href="#" class="order" data-field="id" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="id" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="id" data-field="id"
                               value="<?= isset($filter['id']) ? $filter['id'] : '' ?>">
                    </div>
                </th>

                <th><?=$_->l('Логин')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="username" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="username" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="username"
                               value="<?= isset($filter['username']) ? $filter['username'] : '' ?>">
                    </div>
                </th>
                <th width="15%"><?=$_->l('ФИО')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="name" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="name" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="name"
                               value="<?= isset($filter['name']) ? $filter['name'] : '' ?>">
                    </div>
                </th>
                <th><?=$_->l('Email')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="email" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="email" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="email"
                               value="<?= isset($filter['email']) ? $filter['email'] : '' ?>">
                    </div>
                </th>
                <th><?=$_->l('Телефон')?>
                    <div class="sorting">
                        <a href="#" class="order" data-field="phone" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="phone" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="phone"
                               value="<?= isset($filter['phone']) ? $filter['phone'] : '' ?>">
                    </div>
                </th>
                <th width="10%"><?=$_->l('Баланс')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <? if (count($clients) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($clients as $client) { ?>
                <tr>
                    <th scope="row"><?= $client->id ?></th>
                    <td><?= $client->username ?></td>
                    <td><?= $client->name ?></td>
                    <td><?= $client->email ?></td>
                    <td><?= $client->phone ?></td>
                    <td>
                        <?= $currency->displayPrice($client->balance) ?>
                        <a href="<?= $_->link('admin/client/edit-balance/' . $client->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"
                           data-toggle="tooltip" data-placement="top" title="<?=$_->l('Изменить')?>"
                        ><span class="glyphicon glyphicon-edit"
                               style="margin: 0" aria-hidden="true"></span></a>
                    </td>
                    <td>
                        <a href="<?= $_->link('admin/client/' . $client->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"><span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Изменить')?></a>
                        <a href="<?= $_->link('admin/client/info/' . $client->id) ?>"
                           class="btn btn-xs btn-info ajax-modal"><span class="glyphicon glyphicon-info-sign"
                                                                        aria-hidden="true"></span><?=$_->l('Информация')?></a>
                        <a href="<?= $_->link('admin/client/remove/' . $client->id) ?>"
                           class="btn btn-xs btn-danger ajax-action" data-confirm="<?=$_->l('Вы уверены, что хотите безвозвратно удалить клиента?')?> ">
                            <span class="glyphicon glyphicon-trash"
                                                                           aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                        <a href="<?= $_->link('admin/login-client/' . $client->id) ?>"
                           class="btn btn-xs btn-default "><span
                                class="glyphicon glyphicon-log-in" aria-hidden="true"></span><?=$_->l('Войти')?></a>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>


        <script>
            $('form').on('submit', function () {

                return false;
            });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>
    </div>
    <?= $pagination ?>

</div>