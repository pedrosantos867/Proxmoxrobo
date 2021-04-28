<script>
$(document).ready(function() {
    $('#revert_button').click(function() {
        alert("olasda");
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                job: "",
                action: 'revertTo',
                ajax: 1
            },
            success: function(data) {
                alert("success!");
            }
            alert("clicked");
        })
    });
});
</script>
<div class="ajax-block">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?=$_->l('Backup Data')?></th>
                <th><?=$_->l('Status')?></th>
                <th><?=$_->l('Action')?></th>
            </tr>
        </thead>
        <tbody>
            <? if(count($jobs) == 0){ ?>
            <tr class="text-center">
                <td colspan="100%"><?= $_->l('No results found') ?></td>
            </tr>
            <? }?>
            <? foreach($jobs as $job){ ?>
            <tr>
                <td><?= date('Y-m-d h\h:m', $job["starttime"]) ?></td>
                <td><b><?= strval($job["status"]) ?></b></td>
                <td>
                    <button id="revert_button" value=<?=$job?> class="btn btn-lg btn-primary"><span
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