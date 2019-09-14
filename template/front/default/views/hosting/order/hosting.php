<style>

    body {
        padding-top: 75px;
    }

    .panel-pricing {
        -moz-transition: all .3s ease;
        -o-transition: all .3s ease;
        -webkit-transition: all .3s ease;
    }

    .panel-pricing:hover {
        box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.2);
    }

    .panel-pricing .panel-heading {
        padding: 20px 10px;
    }

    .panel-pricing .panel-heading .fa {
        margin-top: 10px;
        font-size: 58px;
    }

    .panel-pricing .list-group-item {
        color: #777777;
        border-bottom: 1px solid rgba(250, 250, 250, 0.5);
    }

    .panel-pricing .list-group-item:last-child {
        border-bottom-right-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    .panel-pricing .list-group-item:first-child {
        border-top-right-radius: 0px;
        border-top-left-radius: 0px;
    }

    .panel-pricing .panel-body {
        background-color: #f0f0f0;
        font-size: 40px;
        color: #777777;
        padding: 20px;
        margin: 0px;
        line-height: 1;
    }


    .panel-pricing .panel-body > p {
        margin: 0;
    }

    .panel .list-group-item {
        text-align: left;
        padding-left: 25%;
        font-weight: 500;
    }

    .panel .list-group-item .fa-check {
        margin-right: 5%;
    }
</style>

<!-- Plans -->
<section id="plans">
    <div class="container">
        <div class="row">
            <? $i=1; foreach ($plans as $plan) { if($plan->hidden){continue;} ?>
            <?if($i === 1){?>
            <div class="row">
                <?}?>
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
                                <li class="list-group-item" data-toggle="tooltip" data-placement="top" title="<?= $detail->desc ?>"><i class="fa fa-check"></i><?= $detail->name ?>
                                    : <?= $detail->value ?></li>
                            <? } ?>

                        </ul>
                        <div class="panel-footer">
                            <a class="btn btn-lg btn-block btn-danger"
                               href="<?= $_->link('order/plan/' . $plan->id) ?>"><?= $_->l('ЗАКАЗАТЬ') ?>!</a>
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