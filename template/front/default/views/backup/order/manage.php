<script>
$(document).ready(function() {
    $('.revert_btn').click(function() {
        $.ajax({
            method: 'post',
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

    $('#delete_btn').click(function() {
        $("#myModal").modal("toggle");
    });

    $('#btn-confirm-deletion').click(function() {
        var backup = $('#delete_btn').attr('value')
        $.ajax({
            method: 'post',
            data: {
                backup: backup,
                action: 'deleteBackup',
                ajax: 1
            },
            complete: function(data) {
                alert("deleted!");
            }
        })
    });
});
</script>
<div class="ajax-block">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?=$_->l('Backup Date')?></th>
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
            <? foreach($backup_list as $backup){ ?>
            <tr>
                <td><?= date('Y-m-d h\h:m', $backup["ctime"]) ?></td>
                <td><?= round($backup["size"] / 1024 / 1024 / 1024, 2)." GB" ?></td>
                <td><?= strval($backup["format"]) ?></td>
                <td>
                    <button value=<?=http_build_query($backup, '', ',')?>
                        class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-repeat revert_btn"></span>
                        <?= $_->l('Revert to') ?>
                    </button>
                    <button data-target="#myModal" id="delete_btn"
                        value=<?=http_build_query($backup, '', ',')?> class="btn btn-xs btn-danger"><span
                            class="glyphicon glyphicon-trash"></span>
                        <?= $_->l('Delete') ?>
                    </button>
                </td>
            </tr>
            <?} ?>
        </tbody>
    </table>
    <div class="container">
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Delete backup</h4>
                    </div>
                    <div class="modal-body">
                        <b>Are you sure you want to delete this backup?</b>
                        <h6>This process is irreversible</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left" id="btn-confirm-deletion">Yes</button>
                        <button type="button" class="btn btn-success pull-left" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <style>
    dow,
    th,
    td {
        text-align: center;
    }
    </style>
</div>