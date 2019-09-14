<div class="ajax-block">


    <div>
        <div class="top-menu">
            <a href="<?= $_->link('admin/employee/add') ?>" class="btn btn-default ajax-modal"><span
                    class="glyphicon glyphicon-ok"
                    aria-hidden="true"></span><?=$_->l('Добавить')?></a>
        </div>

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
                <th><?=$_->l('ФИО')?>
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
                <th>Email
                    <div class="sorting">
                        <a href="#" class="order" data-field="email" data-type="desc"><span
                                class="glyphicon glyphicon-chevron-up"></span></a>
                        <a href="#" class="order" data-field="email" data-type="asc"><span
                                class="glyphicon glyphicon-chevron-down"></span></a>
                    </div>
                    <div>
                        <input type="text" class="form-control filter" name="email" data-type="like"
                               value="<?= isset($filter['email']) ? $filter['email'] : '' ?>">
                    </div>
                </th>
                <th></th>

            </tr>
            </thead>
            <tbody>
            <? if (count($employees) == 0) { ?>
                <tr>
                    <td colspan="11"><?=$_->l('Результаты не найдены.')?></td>
                </tr>
            <? } ?>
            <? foreach ($employees as $client) { ?>
                <tr>
                    <th scope="row"><?= $client->id ?></th>
                    <td><?= $client->username ?></td>
                    <td><?= $client->name ?></td>
                    <td><?= $client->email ?></td>

                    <td>
                        <a href="<?= $_->link('admin/employee/' . $client->id) ?>"
                           class="btn btn-xs btn-default ajax-modal"><span
                                class="glyphicon glyphicon-cog" aria-hidden="true"></span><?=$_->l('Изменить')?></a>
                        <a href="<?= $_->link('admin/employee/remove/' . $client->id) ?>"
                           class="btn btn-xs btn-danger ajax-action"
                           data-confirm="Вы уверены, что хотите удалить работника?"><span
                                class="glyphicon glyphicon-trash"
                                aria-hidden="true"></span><?=$_->l('Удалить')?></a>
                    </td>
                </tr>
            <? } ?>
            </tbody>
        </table>


        <script>
            $('form').on('submit', function () {

                return false;
            })
        </script>
    </div>
    <?= $pagination ?>

</div>