<div class="ajax-block">
    <div class="page-header">
        <h1>Backup settings</h1>
        <h3 class="text-muted">Here you can configure prices and general backup settings</h3>
    </div>
    <div class="well">
        <form method="POST">
            <h3>Pricing</h3>
            <div class="form-group">
                <label for="pricePBS"><?=$_->l('Price for an incremental backup')?></label>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="input-group ">
                            <div class="input-group-addon"><?= $this->currency->object->symbol?></div>
                            <input type="text" class="form-control" name="pricePBS" id="pricePBS" placeholder="Amount" value=<?=$backupConfig->pricePBS?>>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-3">
                        <label for="priceNAS"><?=$_->l('Price for an complete backup')?></label>
                        <div class="input-group ">
                            <div class="input-group-addon"><?= $this->currency->object->symbol?></div>
                            <input type="text" class="form-control" name="priceNAS" id="priceNAS" placeholder="Amount" value=<?=$backupConfig->priceNAS?>>
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
                            <input type="text" class="form-control" name="maxNumberOfRetentions" id="maxNumberOfRetentions" placeholder="Amount" value=<?=$backupConfig->maxNumberOfRetentions?>>
                            <div class="input-group-addon">Backups</div>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Other settings</h3>

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-5">
                        <label for="IOBandwidthLimit"><?=$_->l('Limit I/O bandwidth (KBytes per second)')?></label>
                        <div class="col">
                            <small class="text-muted"><?= $_->l('0 for unlimited') ?></small>
                        </div>
                        <div class="input-group ">
                            <input type="text" class="form-control" name="IOBandwidthLimit" id="IOBandwidthLimit" placeholder="Amount" value=<?=$backupConfig->IOBandwidthLimit?>>
                            <div class="input-group-addon">KBytes per second</div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-success"><?=$_->l('Save')?></button>
        </form>
    </div>
</div>