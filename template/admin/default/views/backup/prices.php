<div class="ajax-block">
    <div class="page-header">
        <h1>Backup settings</h1>
        <h3 class="text-muted">Here you can configure prices and general backup settings</h3>
    </div>
    <div class="well">
        <form method="POST">
            <h3>Pricing</h3>
            <div class="form-group">
                <label for="pricePerGB"><?=$_->l('Price for each GB')?></label>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="input-group ">
                            <div class="input-group-addon"><?= $this->currency->object->symbol?></div>
                            <input type="text" class="form-control" name="pricePerGB" id="pricePerGB" placeholder="Amount" value=<?=$backupConfig->pricePerGB?>>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Price per retention</h3>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-3">
                        <label for="multiplierForRetention"><?=$_->l('Multiplier for each retention')?></label>
                        <div class="input-group ">
                            <input type="text" class="form-control" name="multiplierForRetention" id="multiplierForRetention" placeholder="Amount" value=<?=$backupConfig->multiplierForRetention?>>
                            <div class="input-group-addon">%</div>
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