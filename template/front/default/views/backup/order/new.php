<div class="ajax-block">
<br>
<h1>Create backup</h1>
    <form method="POST">
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
                        <input type="text" class="form-control" aria-label="..." readonly value="<? echo $day?>">
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
                                <input type="checkbox" name="check_list_vps[]" aria-label="" value="<? echo strval($vps->vmid)?>">
                            </span>
                            <input type="text" class="form-control" aria-label="..." readonly
                                value="<? echo strval($vps->vmid)?>">
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
            <br>

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
            <br>
            <button class="btn btn-success"><?=$_->l('Create')?></button>
        </div>
    </form>
</div>