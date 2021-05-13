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
                    <div class="panel panel-default panel-pricing">
                        <div class="panel-heading">


                            <h3><?= $plan->name ?></h3>
                        </div>
                        <div class="panel-body text-center">
                            <p><strong><?= $currency->displayPrice($plan->price) ?> /
                                    <?= $_->l('месяц') ?></strong></p>
                        </div>
                        <ul class="list-group text-center">
                            <li class="list-group-item">Image: <?= $plan->images ?></li>
                            <li class="list-group-item"><?= $plan->memory ?> MB RAM</li>
                            <li class="list-group-item"><?= $plan->cores ?> cores</li>
                            <li class="list-group-item"><?= $plan->socket ?> sockets</li>
                            <li class="list-group-item"><?= $plan->hdd ?> GB HDD</li>

                            <? if($plan->bandwith == 0){ ?>
                            <li class="list-group-item">Bandwith: Unlimited speed</li>
                            <? } else { ?>
                            <li class="list-group-item">Bandwith: <?= $plan->bandwith ?> MB/s</li>
                            <? } ?>

                            <? if($plan->transfer == 0){ ?>
                            <li class="list-group-item">Unmetered</li>
                            <? } else { ?>
                            <li class="list-group-item"><?= $plan->transfer ?> GB transfer/Month</li>
                            <? } ?>

                            <? if($plan->test_days > 0){ ?>
                            <li class="list-group-item"><?= $plan->test_days ?> test days</li>
                            <? } ?>
                            <? foreach ($plan->details as $detail) { ?>
                            <li class="list-group-item"><i class="fa fa-check"></i><?= $detail->name ?>
                                : <?= $detail->value ?></li>
                            <? } ?>

                        </ul>
                        <div class="panel-footer">
                            <a class="btn btn-lg btn-block btn-success"
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