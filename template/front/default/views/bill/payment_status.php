<? if (isset($bills)) { ?>
    <? if ($success) { ?>
        <h3 style="text-align: center;">
           <?=$_->l('Спасибо!')?>

            <?=$_->l('Счета № %bills успешно оплачены!', array('bills' => '<span style="color: rgb(54, 178, 13);">'.implode(', ', $bills).'</span>'))?>
        </h3>
    <? } else { ?>

        <? if (isset($psystem) && $psystem == 'balance') { ?>
            <h3 style="text-align: center;">
                <?= $_->l('К сожалению, счета %bills не были оплачены.', array('bills' => '<span style="color: red;">№ '.implode(', ', $bills) .'</span> ')) ?>
                <br><span style="color: red;"><?= $_->l('Не достаточно средств на') ?> <a
                        href="<?= $_->link('balance') ?>"><?= $_->l('балансе') ?></a>.</span>
                <br><?= $_->l('Пополните баланс или выберите другой способ оплаты!') ?>
            </h3>
        <? } else { ?>
            <h3 style="text-align: center;">
                <?= $_->l('К сожалению, счета') ?> <span style="color: red;">№<?= implode(', ', $bills) ?></span>
                <?= $_->l('не были оплачены.') ?>
                <?= $_->l('Обратитесь к администратору.') ?>
            </h3>
        <? } ?>

    <? } ?>
<? } elseif (isset($bill) && is_object($bill) && $bill->isLoadedObject()) { ?>
    <? if ($success) { ?>
        <h3 style="text-align: center;">
            <?= $_->l('Спасибо!') ?> <?= $_->l('Счет %bill успешно оплачен!', array('bill' => '<span style="color: rgb(54, 178, 13);">№ '.$bill->id.'</span>')) ?>
        </h3>
    <? } else { ?>
        <? if (isset($psystem) && $psystem == 'balance') { ?>
            <h3 style="text-align: center;">
                <?= $_->l('К сожалению, счет %bill не был оплачен.', array('bill' => '<span style="color: red;">№ '.$bill->id .'</span>')) ?>

                <br><span style="color: red;"><?= $_->l('Не достаточно средств на балансе.') ?> </span>
                <br><?= $_->l('Пополните баланс или выберите %0 другой способ оплаты %1', array('0' => '<a
                    href="'. $_->link('bill/' . $bill->id) .'">', '1' => '</a>'))?>
            </h3>
        <? } else { ?>
            <h3 style="text-align: center;">
                <?= $_->l('К сожалению, счет %bill не был оплачен.', array('bill' => '<span style="color: red;">№ '.$bill->id .'</span>')) ?>
            </h3>
        <? } ?>

    <? } ?>

<? } else { ?>
    <? if ($success) { ?>
        <h3 style="text-align: center;color: rgb(54, 178, 13);">
        <?= $_->l('Спасибо!') ?> <?= $_->l('Оплата произведена успешно!') ?>
        </h3>

    <? } else { ?>
        <h3 style="text-align: center;color: red;">
            <?= $_->l('К сожалению оплата не была произведена.') ?>
        </h3>
    <? } ?>
<? } ?>