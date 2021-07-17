<title>Create backup</title>
<div class="ajax-block">
    <div class="page-header">
        <h1>Create a periodic backup job</h1>
    </div>
    <form method="POST">
        <div class="well">
            <div class="form-group">
                <input type="hidden" id="backup_prices" value="<?=$backupsConfig["pricePerGB"] . ' ' .$backupsConfig["multiplierForRetention"]?>">
                <label class="control-label" for="daysOfWeek"><?=$_->l('Days of the week')?></label>

                <? $daysOfWeek = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday") ?>

                <? foreach($daysOfWeek as $day){?>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" name="check_list_days[]" aria-label="" value="<? echo $day?>">
                            </span>
                            <input type="text" class="form-control" aria-label="..." readonly
                                value="<? echo ucfirst($day)?>">
                        </div>
                    </div>
                </div>
                <? } ?>
                <br>
                <div class="form-group">
                    <label class="control-label" for="vps_to_backup"><?=$_->l('VPS available to backup ')?></label>
                    <? foreach($vps_list as $vps){?>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="radio" name="vps_to_backup" aria-label=""
                                        value="<?= strval($vps->vmid)." ".$vps->disk_size?>">
                                </span>
                                <input type="text" class="form-control" aria-label="..." readonly
                                    value="<?= "VPS with vmid: ".strval($vps->vmid)?>">
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
                                title="Incremental backups"
                                data-content="Incremental backups are faster but the administrator needs to configure a PBSServer."></i>
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
                                title="Full backups"
                                data-content="Full backups are slower and can result in a longer downtime."></i>
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
                        <div>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="popover" data-placement="right"
                                title="Attention"
                                data-content="This mode will only work if your VPS have the Qemu Guest Agent installed!"></i>
                        </div>
                    </div>
                </div><br>

                <div class="from-group">
                    <label class="control-label" for="retention"><?=$_->l('Maximum number of stored backups')?></label>
                    <div class="row">
                        <div class="col-lg-1">
                            <div class="input-group">
                                <input type="number" id="retention" name="retention" value="1" min="1" max="25">
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
                <div class="from-group">
                    <label class="control-label" for="price_to_pay"><?=$_->l('Price to pay')?></label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" id="price_to_pay" class="form-control" readonly aria-label="Amount (to the nearest dollar)" value="0">
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-success"><?=$_->l('Create')?></button>
    </form>
    <br>
    <button class="btn btn-warning glyphicon glyphicon-arrow-left btn-go-back"><?=$_->l('   Go back')?></button>
    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });

        $('input[name=retention]').on('change', function () {
            calculatePrice();
        });

        $('input[name=vps_to_backup]').on('change', function () {
            calculatePrice();
        });

        function calculatePrice(){
            var val = $('input[name="vps_to_backup"]:checked').val();
            var splitted = val.split(' ');
            var disk_size = splitted[1];
            var number_of_retentions = $("#retention").val();

            var splitted_backup_prices = $('#backup_prices').val();
            var backup_prices = splitted_backup_prices.split(' ');

            var pricePerGB = backup_prices[0];
            var multiplierForRetention = backup_prices[1];
            var price = disk_size*parseInt(pricePerGB, 10)*1+(parseFloat(multiplierForRetention)*parseInt(number_of_retentions, 10));
            $('#price_to_pay').val(price);
        }

        $(".btn-go-back").click(function() { 
            var getUrl = window.location;
            $(location).attr("href", getUrl .protocol + "//" + getUrl.host + "/bills");
        });
    </script>
    <style>
    .row {
        padding-bottom: 0.5em;
    }
    </style>
</div>