<div class="hidden-print">
<div class="bottom10">
   <?=$bankconfig->desc?>
</div>
<div>
    <a href="<?=$_->link('modules/banktransfer/savepdf/'.$id_bill)?>" class="btn btn-default btn-primary"><span class="glyphicon glyphicon-download"></span> <?=$_->l("Скачать PDF счет")?></a>
    <a href="#" class="btn btn-default btn-warning print-invoice"><span class="glyphicon glyphicon-print"></span> <?=$_->l("Распечатать счет")?></a>
</div>
</div>

<style media="print">
    .printing {display: block !important}
    footer, nav{
        display: none !important;}
</style>

<div class="printing visible-print-block"></div>
<script>
    $('.print-invoice').on('click', function () {
        $.ajax({
            method: 'post',
            dataType: 'json',
            data: {action: 'getInvoice', ajax:1},
            success: function (data) {
                if(data.result){
                    $('.printing').html(data.content);
                    window.print();
                }
            }
        })
    })
</script>