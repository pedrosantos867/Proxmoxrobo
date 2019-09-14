<?= $_->JS('validator.js') ?>


<form method="post">


    <div class="form-group">
        <label for="id_plan"><?= $_->l('Выбранный хостинг тариф') ?> (<a
                href="<?= $_->link('modules/billmanagervps/order/new') ?>"><?= $_->l('Изменить') ?></a>)</label>
        <input type="hidden" name="id_plan" value="<?= $plan->id ?>">
        <input class="form-control" value="<?= $plan->name ?>" disabled>
    </div>

    <div class="form-group">
        <label><?= $_->l('Период оплаты') ?></label>
        <select name="pay_period" class="form-control">
            <option value="1">1 <?= $_->l('месяц') ?>
                - <?= $currency->displayPrice($plan->price) ?>      </option>
            <option value="3">3 <?= $_->l('месяца') ?>
                - <?= $currency->displayPrice($plan->price * 3)  ?>   </option>
            <option value="6">6 <?= $_->l('месяцев') ?>
                - <?= $currency->displayPrice($plan->price * 6)  ?>   </option>
            <option value="12">12 <?= $_->l('месяцев') ?>
                - <?= $currency->displayPrice($plan->price * 12)  ?>  </option>
        </select>
    </div>

    <div class="form-group">
        <label><?= $_->l('Домен') ?></label>
        <input type="text" name="domain" data-validate="domain" class="form-control" value="<?= $_->p('domain') ?>">
    </div>

    <div class="form-group">
        <label><?= $_->l('Шаблон') ?></label>
        <select name="template" class="form-control">
        <?foreach($templates as $id=>$template){?>
            <option value="<?=$id?>"><?= $template?></option>
        <?}?>
        </select>
    </div>

    <button id="order-button" type="submit" class="btn btn-success"><span class="glyphicon glyphicon-play-circle"></span> <?= $_->l('Сформировать заказ') ?>
    </button>
</form>
