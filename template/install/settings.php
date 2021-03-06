<div class="row">
    <div class="col-md-12">

    <div class="alert alert-warning" style="margin-top: 15px" role="alert">
        <?=$_->l('Attention! For the proper functioning of the billing system, you need to create the following cron record:')?>
        <br>

        <b><em> 0 1 * * * php -q <?php echo \System\Path::getRoot('app/cron.php') ?> </em></b>
        <br>
        или
        <br>
        <b><em> 0 1 * * * wget -q -O /dev/null <?php echo \System\Path::getURL('cron') ?>/<?= $config->uniq_key ?> </em></b>

    </div>

    </div>
</div>
<form method="post">
    <div class="row" style="padding: 10px">

        <div class="col-md-12">
            <div class="page-header">
                <h4><?=$_->l('Create administrator')?></h4>
            </div>
            <div class="form-group">
                <label for="login"><?=$_->l('Username')?></label>
                <input type="text" name="username" class="form-control" id="login" placeholder="username">
            </div>
            <div class="form-group">
                <label for="email"><?=$_->l('Email')?> </label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password"><?=$_->l('Password')?></label>
                <input type="password" name="password" class="form-control" id="password" placeholder="password">
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-success pull-right"><?=$_->l('Complete installation')?> <span
                    class="glyphicon glyphicon-chevron-right"></span></button>
        </div>
    </div>
</form>