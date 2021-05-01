<script>
$(document).ready(function() {
    $('#revert_button').click(function() {
        //alert($(this).attr("value")); 

        $.ajax({
            method: 'post',
            //dataType: 'json',
            data: {
                backup: $(this).attr("value").split(';'),
                action: 'revertTo',
                ajax: 1
            },
            complete: function(data) {
                alert(data);
                alert("success!");
            }
        })
    });
});
</script>
<div class="ajax-block">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?=$_->l('Backup Data')?></th>
                <th><?=$_->l('Size')?></th>
                <th><?=$_->l('Format')?></th>
                <th><?=$_->l('Action')?></th>
            </tr>
        </thead>
        <tbody>
            <? if(count($backup_list) == 0){ ?>
            <tr class="text-center">
                <td colspan="100%"><?= $_->l('No results found') ?></td>
            </tr>
            <? }?>
            <? foreach($backup_list["data"] as $backup){ ?>
            <tr>
                <td><?= date('Y-m-d h\h:m', $backup["ctime"]) ?></td>
                <td><?= strval($backup["size"]) ?></td>
                <td><?= strval($backup["format"]) ?></td>
                <td>
                    <button id="revert_button" value=<?=http_build_query($backup, '', ',')?> class="btn btn-lg btn-primary"><span
                            class="glyphicon glyphicon-repeat"></span> <?= $_->l('Revert to') ?>
                    </button>
                </td>
            </tr>
            <?} ?>
        </tbody>
    </table>
    <style>
    dow,
    th,
    td {
        text-align: center;
    }
    </style>
</div>