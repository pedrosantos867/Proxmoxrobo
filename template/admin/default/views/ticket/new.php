<?= $_->JS('validator.js') ?>
<script>
    $(function () {
        $('.main-form').validate({messages: validate_messages});

    })
</script>
<form class="main-form" action='' method="POST" enctype="multipart/form-data">


    <div class="form-group">
        <label class="control-label" for="subject">Тема</label>
        <input type="text" id="subject" name="subject" data-validate="required" placeholder=""
               class="input-xlarge form-control">
    </div>

    <?= $_->js('select2/select2.min.js') ?>
    <?= $_->js('select2/i18n/ru.js') ?>
    <?= $_->css('select2/select2.min.css') ?>
    <div class="form-group">
        <label for="client_id"><?=$_->l('Клиент')?></label>
        <select name="client_id" data-validate="required" class="form-control">
            <option value=""> ---</option>
            <? foreach ($clients as $client) { ?>
                <option
                    value="<?= $client->id ?>" <?= (($order->client_id == $client->id) || \System\Tools::rPOST('user_id') == $client->id ? 'selected' : '') ?> ><?= $client->name ?></option>
            <? } ?>
        </select>
        <script type="text/javascript">
            function formatRepo(repo) {
                if (repo.loading) return '<?=$_->l('Загрузка...')?>';

                var markup = '<div class="clearfix">' +
                        //  '<div class="col-sm-1">' +
                        //  '<img src="' + repo.login + '" style="max-width: 100%" />' +
                        // '</div>' +

                    '<div class="col-sm-10">' +
                    '<div class="clearfix">' +
                    '<div class="col-sm-2">' + repo.username + '</div>' +
                    '<div class="col-sm-4">' + repo.name + '</div>' +
                    '<div class="col-sm-3"><i class="fa fa-code-fork"></i> ' + repo.email + '</div>' +
                    '<div class="col-sm-2"><i class="fa fa-star"></i> ' + repo.phone +
                    '</div>' +
                    '</div>';


                markup += '</div></div>';

                return markup;
            }

            function formatRepoSelection(repo) {
                console.log(repo);
                return repo.name;
            }
            $("select[name=client_id]").select2({
                ajax: {
                    method: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            action: 'getClients',
                            ajax: 1
                        };
                    },
                    processResults: function (data, page) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                language: "ru",
                placeholder: '<?=$_->l('Введите логин, ФИО, телефон или Email')?>',
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        </script>
    </div>

    <div class="form-group">
        <!-- E-mail -->
        <label class="control-label" for="name"><?=$_->l('Приоритет')?></label>
        <select type="text" id="priority" name="priority" data-validate="required" class="input-xlarge form-control">
            <option value="0"><?=$_->l('Низкий')?></option>
            <option value="1"><?=$_->l('Средний')?></option>
            <option value="2"><?=$_->l('Высокий')?></option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="message"><?=$_->l('Сообщение')?></label>
        <textarea id="message" name="message" placeholder="" data-validate="required"
                  class="input-xlarge form-control"></textarea>
    </div>
    <div class="form-group">
        <label class="control-label" for="message"><?=$_->l('Файлы')?></label>
        <input type="file" multiple="multiple" class="input-xlarge form-control" name="files[]">
    </div>
    <div class="form-group">
        <button class="btn btn-success "><span class="glyphicon glyphicon-send"></span> <?=$_->l('Отправить')?></button>
        <a href="<?= $_->link('admin/tickets') ?>" class="btn btn-info pull-right"><span
                class="glyphicon glyphicon-arrow-left"></span> <?=$_->l('К списку тикетов')?></a>
    </div>
</form>