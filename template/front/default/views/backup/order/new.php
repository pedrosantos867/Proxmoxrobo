<title>Create backup</title>
<div class="ajax-block">
    <div class="page-header">
        <h1>Create a periodic backup job</h1>
    </div>
    <form method="POST">
        <div class="well">
            <div class="form-group">
                <label class="control-label" for="daysOfWeek"><?=$_->l('Days of the week')?></label>

                <? $daysOfWeek = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday") ?>

                <? foreach($daysOfWeek as $day){?>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="check_list_days[]" aria-label="" value="<? echo $day?>">
                            </span>
                            <input type="text" class="form-control" aria-label="..." readonly value="<? echo ucfirst($day)?>">
                        </div>
                    </div>
                </div>
                <? } ?>
                <br>
                <div class="form-group">
                    <label class="control-label" for="vps_list"><?=$_->l('VPS available to backup ')?></label>
                    <? foreach($vps_list as $vps){?>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="check_list_vps[]" aria-label=""
                                        value="<?= strval($vps->vmid)?>">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly
                                    value="<?= strval($vps->vmid)?>">
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>
                <br>
                <div class="from-group">
                    <label class="control-label" for="backup_type"><?=$_->l('Backup type')?></label>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="backup_type" aria-label="" value="incremental">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly
                                    value="Incremental backup">
                            </div>
                        </div>
                        <div>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="popover" data-placement="right"
                                title="Incremental backups" data-content="Incremental backups are faster but the administrator needs to configure a PBSServer."></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="backup_type" aria-label="" value="full">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly value="Full backup">
                            </div>
                        </div>
                        <div>
                        <i class="glyphicon glyphicon-info-sign" data-toggle="popover" data-placement="right"
                                title="Full backups" data-content="Full backups are slower and can result in a longer downtime."></i>
                        </div>
                    </div>
                </div><br>

                <div class="from-group">
                    <label class="control-label" for="backup_mode"><?=$_->l('Backup mode')?></label>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="backup_mode" aria-label="" value="snapshot">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly value="Snapshot">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="backup_mode" aria-label="" value="suspend">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly value="Suspend">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="backup_mode" aria-label="" value="stop">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly value="Stop">
                            </div>
                        </div>
                    </div>
                </div><br>

                <div class="from-group">
                    <label class="control-label" for="retention"><?=$_->l('Maximum number of stored backups')?></label>
                    <div class="row">
                        <div class="col-lg-1">
                            <div class="input-group">
                                <input type="number" id="retention" name="retention" value="1" min="1" max="5">
                            </div>
                        </div>
                    </div>
                </div><br>

                <div class="from-group">
                    <label class="control-label" for="time_of_the_day"><?=$_->l('Time of the day')?></label>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="time" id="time" name="time">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-success"><?=$_->l('Create')?></button>
    </form>
    <script>
    $(document).ready(function() {
        $('[data-toggle="popover"]').popover();
    });
    </script>
    <style>
        .row{
            padding-bottom: 0.5em;
        }
    </style>
</div>