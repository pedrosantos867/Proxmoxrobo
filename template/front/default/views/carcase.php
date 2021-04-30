<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="HopeBilling">

    <link rel="icon" href="<?= $_->path('img/favicon.ico') ?>" sizes="16x16" type="image/ico">

    <title><?= $_->l('Биллинг панель') ?> | HopeBilling</title>

    <!-- Bootstrap core CSS -->
    <?php $_->CSS("bootstrap.min.css"); ?>
    <?php $_->CSS("bootstrap-theme.min.css"); ?>

    <?php $_->Js("jquery.min.js"); ?>
    <?php $_->Js("jquery-ui.min.js"); ?>
    <?php $_->Js("global.js"); ?>

    <?php $_->JS('inputmask/jquery.inputmask.bundle.js') ?>
    <?php $_->JS('inputmask/phone.js') ?>

    <?php $_->CSS("global.css"); ?>





    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        var validate_messages = {
            required: "<?=$_->l('Required field')?>",
            username: "<?=$_->l('3-20 characters, which can be letters and numbers, the first character must be a letter')?>",
            hosting_username: "<?=$_->l('3-20 characters, which can only be small letters and numbers, the first character must be a letter')?>",
            email: "<?=$_->l('Enter correct email')?>",
            pass: "<?=$_->l('Lowercase and uppercase Latin letters, numbers, special characters. Minimum 4 characters.')?>",
            pass2: "<?=$_->l('Re-enter your password')?>",
            fio: "<?=$_->l('Enter your last name and first name')?>",
            ajax: "<?=$_->l('The value is taken')?>",
            phone: "<?=$_->l('Enter the phone number in international format (for example +380921235478)')?>",
            phone_new : "<?=$_->l('Enter the phone number in international format (for example +38 (092) 123-54-78)')?>",
            date: "<?=$_->l('Date in the format: 2015-06-18')?>",
            domain: "<?=$_->l('Enter your domain name')?>",
            possitive_number: "<?=$_->l('Please enter a number greater than zero')?>",
            valid: "<?=$_->l('The field is filled in correctly!')?>"
        };
    </script>
    <script>

        var currency = {
            format:  '<?=$currency->short_name?>',
            coefficient:  '<?=$currency->coefficient?>',
            displayPrice : function(sum) {
                var returnValue = 0;
                if(currency.format.indexOf('{0}')!==false){
                    returnValue =  currency.format.replace('{0}', currency.getPrice(sum));
                }
                return returnValue;
            },
            getPrice: function (price) {

                var number = price*currency.coefficient;
                if(Number.isInteger(number)){
                    return number;
                }

                return (number).toFixed(2);
            }

        };



    </script>


</head>

<body>

<?/*Include Modules*/?>
<?=isset($displayBeforeContent) ?   $displayBeforeContent : ''?>
<?/*End Include Modules*/?>

<?= $content ?>

<?/*Include Modules*/?>
<?=isset($displayAfterContent) ?   $displayAfterContent : ''?>
<?/*End Include Modules*/?>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php $_->Js("bootstrap.min.js"); ?>

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<?php $_->Js("ie10-viewport-bug-workaround.js"); ?>
<?php $_->JS('inputmask/inputmask.binding.js') ?>


</body>
</html>

