<?= $_->js('chartist.js') ?>
<?= $_->css('chartist.min.css') ?>

<? if ($update_aviable) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong><?=$_->l('Внимание!')?></strong>
                <?=$_->l('Доступна новая версия')?> <a href="<?= $_->link('admin/settings/update') ?>"
                                                                    class="alert-link"><?= $new_version ?></a>.
            </div>
        </div>
    </div>
<? } ?>

<? if ($license_end_days) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong><?=$_->l('Внимание!')?></strong>
                <?=$_->l('Ваша лицензия истекает через %day {%day|дней|день|дней}.', array('day' => $license_end_days))?>
            </div>
        </div>
    </div>
<? } ?>

<div class="row">
    <div class="col-lg-12">

            <form id="range" action="#" method="post" class="form-inline">
                <div id="select-period" class="btn-group">
                    <button type="button" data-id="day" class="btn btn-default <?=$period == 'day' ? 'active' : ''?>">
                        <?=$_->l('День')?>
                    </button>
                    <button type="button" data-id="month" class="btn btn-default <?=$period == 'month'  ? 'active' : ''?>">
                        <?=$_->l('Месяц')?>
                    </button>
                    <button type="button" data-id="year" class="btn btn-default <?=$period == 'year' ? 'active' : ''?>">
                        <?=$_->l('Год')?>
                    </button>
                </div>


                <input type="hidden" name="period" id="period" value="" class="form-control">

            </form>
            <script>
                $('#select-period button').on('click', function () {
                    $(this).parent().find('button').removeClass('active');
                    $(this).addClass('active');
                    $('#period').val($(this).data('id'));
                    $('form#range').submit();
                })
            </script>
    </div>
</div>
<div id="draggablePanelList">
<div class="panels">
    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Статистика заказов хостинг услуг')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section " id="chart1"></div>
            </div>
        </div>
    </div>
    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Статистика регистрации клиентов')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section" id="chart2"></div>
            </div>
        </div>

    </div>
    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Активность хостинг аккаунтов')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section " id="chart3"></div>
            </div>
        </div>
    </div>

    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Время ответа серверов')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section " id="chart4"></div>
            </div>
        </div>
    </div>
    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Статистика выставленных счетов')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section " id="chart6"></div>
            </div>
        </div>
    </div>
    <div class="panel-block">
        <div class="panel panel-default">
            <div class="panel-heading text-center"><?=$_->l('Статистика тикетов')?></div>
            <div class="panel-body">
                <div class="ct-chart ct-golden-section " id="chart5"></div>
            </div>
        </div>
    </div>
</div>
</div>
<?= $_->js('jquery-ui.js') ?>
<style>
    .panel-block .panel-heading{
        cursor: move;
    }
    .panel-block {
        width: 33%;
        float: left;
        padding: 4px;
    }
    .panel-block-placeholder {
        width: 32%;
        float: left;
        height: 308.766px;
        width: 376.188px;
    }

    .panel-area {
        background: #f0f0f0;
        border: 1px dotted darkgrey;
        opacity: 0.3;
        height: 285px;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
    }

    .info {
        text-align: center;
        padding-top: 81px;
        color: grey;
    }
</style>
<script>
    jQuery(function($) {
        <?if($positions)foreach($positions as $k=>$position){?>
            $('#<?=$k?>').parents('.panel-block').appendTo('.panels');
        <?}?>


        $('.panels').sortable({
            handle: '.panel-heading',
           placeholder: {
                element: function(currentItem) {
                    return $('<div class="panel-block-placeholder"><div class="panel-area">&nbsp;</div></div>')[0];
                },

                update: function(container, p) {
                    return;
                }
            },
            helper: "clone",
            tolerance: 'pointer',
            revert: 'invalid',
            dropOnEmpty: false,
            stop: function (event, ui) {

                var arr = {};
                var p   = 1;

                $('.panels .panel-block .ct-chart').each(function () {
                    var id_e = ($(this).attr('id'));
                    if (id_e) {
                        arr[id_e] = p;

                        p++;
                    }
                });

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {ajax: 1, data: arr, action: 'setPositions'}
                });

                console.log(arr);
            }
        });
    });
</script>
<script>
    <?if($chart1){?>
    // Initialize a Line chart in the container with the ID chart1
    new Chartist.Line('#chart1', {
        labels: [
            <?foreach($chart1 as $d=>$c){?>
                '<?=$d?>',
            <?}?>
        ],
        series: [[
            <?foreach($chart1 as $d=>$c){?>
                <?=$c?>,
            <?}?>
        ]]
    });
    <?} else {?>
        $('#chart1').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>

    <?if($chart2){?>
    // Initialize a Line chart in the container with the ID chart2
    new Chartist.Line('#chart2', {
        labels: [
            <?foreach($chart2 as $d=>$c){?>
            '<?=$d?>',
            <?}?>
        ],
        series: [[
            <?foreach($chart2 as $d=>$c){?>
            <?=$c?>,
            <?}?>
        ]]
    });
    <?} else {?>
    $('#chart2').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>

    <?if($chart3){?>
    new Chartist.Pie('#chart3', {
        labels: [
            <?foreach($chart3 as $d=>$c){?>
            '<?=$d == 1 ? 'Активно' : 'Отключенно'?>',
            <?}?>
        ],
        series: [
            <?foreach($chart3 as $d=>$c){?>
            <?=$c?>,
            <?}?>
        ]
    });
    <?} else {?>
    $('#chart3').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>


    <?if($chart4){?>
    new Chartist.Bar('#chart4', {
        labels: [
            <?foreach($chart4 as $d=>$c){?>
            '<?=$d?>',
            <?}?>
        ],
        series: [[
            <?foreach($chart4 as $d=>$c){?>
            <?=$c ?>,
            <?}?>
        ]]
    });
    <?} else {?>
    $('#chart4').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>

    <?if($chart5){?>
    new Chartist.Line('#chart5', {
        labels: [
            <?foreach($chart5 as $d=>$c){?>
            '<?=$d?>',
            <?}?>
        ],
        series: [[
            <?foreach($chart5 as $d=>$c){?>
            <?=$c?>,
            <?}?>
        ]]
    });
    <?} else {?>
    $('#chart5').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>

    <?if($chart6){?>
    new Chartist.Line('#chart6', {
        labels: [
            <?foreach($chart6 as $d=>$c){?>
            '<?=$d?>',
            <?}?>
        ],
        series: [[
            <?foreach($chart6 as $d=>$c){?>
            <?=$c?>,
            <?}?>
        ]]
    });
    <?} else {?>
    $('#chart6').html('<div class="info"><?=$_->l('Нет данных за выбранный период')?></div>');
    <?}?>
</script>