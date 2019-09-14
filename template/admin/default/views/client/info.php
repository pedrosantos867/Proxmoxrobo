<div class="loaded-block">
    <style>

    </style>
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$_->l('Информация о клиенте')?>: <?= $client->name ?></h4>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="row">

                            <div class="col-md-12">

                                <div class="row">
                                    <div class="col-md-3 col-lg-3 " align="center">
                                        <img alt="User Pic"
                                            src="http://www.gravatar.com/avatar/<?= md5(strtolower($client->email)) ?>"
                                            class="img-circle img-responsive">
                                    </div>


                                    <div class=" col-md-9 col-lg-9 ">
                                        <table class="table table-user-information">
                                            <tbody>

                                            <tr>
                                                <td><?=$_->l('ФИО')?></td>
                                                <td><?=$client->name?></td>
                                            </tr>
                                            <tr>

                                                <td>Email</td>
                                                <td><a href="mailto:<?= $client->email ?>"
                                                       target="_blank"><?= $client->email ?></a></td>
                                            </tr>
                                            <tr>
                                                    <td><?=$_->l('Номер телефона')?></td>
                                                    <td><?= $client->phone ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?if ($client->comment){?>
                                    <table  class="table table-user-information">
                                        <tbody>
                                        <tr>
                                            <td><?= $_->l('Комментарий')?></td>
                                            <td><?= $client->comment ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <?}?>
                                <? if ($client->getDocs()) { ?>
                                    <table class="table table-user-information">
                                        <tbody>
                                        <tr>
                                            <td><?= $_->l('Документы') ?></td>
                                            <td>
                                                <? foreach ($client->getDocs() as $doc) { ?>
                                                    <a target="_blank"
                                                       href="<?= $_->link('/storage/docs/' . $client->id . '/' . $doc) ?>"><?= $doc ?></a>
                                                    <br/>
                                                <? } ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <? } ?>



                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$_->l('Закрыть')?></button>
                    <a href="<?= $_->link('admin/login-client/' . $client->id) ?>"
                       class="btn btn-default "><span
                            class="glyphicon glyphicon-log-in" aria-hidden="true"></span><?=$_->l('Войти')?></a>

                    <? /*  <a href="<?=$_->link('admin/client/edit-info/'.$info->id)?>" class="btn btn-default btn-primary ajax-modal">Редактировать</a>*/ ?>
                </div>
            </div>
        </div>
    </div>
</div>