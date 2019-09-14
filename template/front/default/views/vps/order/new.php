<!-- Plans -->
<section id="plans">
    <div class="container">
        <div class="row">
            <? $i=1; foreach ($plans as $plan) { ?>
                <?if($i === 1){?>
                    <div class="row">
                <?}?>
                <?if($plan->hidden) continue;?>
                <!-- item -->
                <div class="col-md-4 text-center">
                    <div class="panel panel-danger panel-pricing">
                        <div class="panel-heading">


                            <h3><?= $plan->name ?></h3>
                        </div>
                        <div class="panel-body text-center">
                            <p><strong><?= $currency->displayPrice($plan->price) ?>  /
                                    <?= $_->l('месяц') ?></strong></p>
                        </div>
                        <ul class="list-group text-center">
                            <? foreach ($plan->details as $detail) { ?>
                                <li class="list-group-item"><i class="fa fa-check"></i><?= $detail->name ?>
                                    : <?= $detail->value ?></li>
                            <? } ?>

                        </ul>
                        <div class="panel-footer">
                            <a class="btn btn-lg btn-block btn-danger"
                               href="<?= $_->link('vps-order/plan/' . $plan->id) ?>"><?= $_->l('ЗАКАЗАТЬ') ?>!</a>
                        </div>
                    </div>
                </div>
                <!-- /item -->
                <?if($i/3 === 1){?>
                    </div>
                    <?$i=0;}?>
                <!-- /item -->
                <? $i++; } ?>


        </div>
    </div>
</section>
<!-- /Plans -->