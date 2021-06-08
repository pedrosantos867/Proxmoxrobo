<div class="ajax-block">
    <div class="page-header">
        <h1>Backup settings</h1>
        <h3 class="text-muted">Here you can configure prices and general backup settings</h3>
    </div>
    <div class="well">
        <form method="POST">
            <h3>Pricing</h3>
            <div class="form-group">
                <label for="pricePerGbPBS"><?=$_->l('Price per GB on PBS')?></label>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="input-group ">
                            <div class="input-group-addon"><?= $this->currency->object->symbol?></div>
                            <input type="text" class="form-control" id="pricePerGbPBS" placeholder="Amount" value=<?=$backupConfig->pricePerGbPBS?>>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-3">
                        <label for="pricePerGB"><?=$_->l('Price per GB on other storage media')?></label>
                        <div class="input-group ">
                            <div class="input-group-addon"><?= $this->currency->object->symbol?></div>
                            <input type="text" class="form-control" id="pricePerGB" placeholder="Amount" value=<?=$backupConfig->pricePerGB?>>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Retention options</h3>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-3">
                        <label for="maxNumberOfRetentions"><?=$_->l('Maximum number of backups per vm')?></label>
                        <div class="input-group ">
                            <input type="text" class="form-control" id="maxNumberOfRetentions" placeholder="Amount" value=<?=$backupConfig->maxNumberOfRetentions?>>
                            <div class="input-group-addon">Backups</div>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Other settings</h3>

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-3">
                        <label for="typeOfBackup"><?=$_->l('Type of backup')?></label>
                        <select name="typeOfBackup" class="form-control">
                            <option value="snapshot">Snapshot</option>
                            <option value="suspend">Suspend</option>
                            <option value="stop">Stop</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-5">
                        <label for="IOBandwidthLimit"><?=$_->l('Limit I/O bandwidth (KBytes per second)')?></label>
                        <div class="col">
                            <small class="text-muted"><?= $_->l('0 for unlimited') ?></small>
                        </div>
                        <div class="input-group ">
                            <input type="text" class="form-control" id="IOBandwidthLimit" placeholder="Amount" value=<?=$backupConfig->IOBandwidthLimit?>>
                            <div class="input-group-addon">KBytes per second</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" id="enableIncrementalBackups" name="enableIncrementalBackups" value="1">
                    <?=$_->l('Activate the ability to do incremental backups (Proxmox Backup Server)')?>
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="enableFullBackups" value="1">
                    <?=$_->l('Activate the ability to do full backups (Every other storage media)')?>
                </label>
            </div><br>

            <button class="btn btn-success"><?=$_->l('Save')?></button>
        </form>
    </div>
</div>