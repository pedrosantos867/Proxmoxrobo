{s}<?=$_->l('Приоритет тикета был изменен')?>{/s}
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body
    style="width: 100% !important;min-width: 100%;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100% !important;margin: 0;padding: 0;background-color: #FFFFFF">
<table cellpadding="0" cellspacing="0" width="100%" class="body" border="0">
    <tbody>
    <tr style="vertical-align: top">
        <td class="center" align="center" valign="top" style="">
            <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0"
                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                <tbody>
                <tr style="vertical-align: top">
                    <td width="100%" style="background-color: #61626F; text-align: center;">
                        <div class="col num6"
                             style="display: inline-block;vertical-align: top;text-align: center;width: 272px">
                            <table cellpadding="0" cellspacing="0" width="100%" border="0"
                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                                <tbody>
                                <tr style="vertical-align: top">
                                    <td align="center"
                                        style="vertical-align: top;width: 100%;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 0px">
                                        <div align="center" style="font-size:12px">
                                            <img align="center" border="0"
                                                 src="<?= $_->path('front/images/logo250-75.png') ?>" alt="Image"
                                                 title="Image"
                                                 style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: 0;height: auto;line-height: 100%;margin: 0 auto;float: none;width: 100% !important;max-width: 250px"
                                                 width="250">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="display: inline-block;vertical-align: top;text-align: center;width: 272px">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                                <tbody>
                                <tr style="vertical-align: top">
                                    <td align="center"
                                        style="vertical-align: top;padding-top: 20px;padding-right: 10px;padding-bottom: 20px;padding-left: 10px">
                                        <div align="center"
                                             style="display: inline-block; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; max-width: 80%; width: 100%; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 34px">
                                                <tbody>
                                                <tr style="vertical-align: top">
                                                    <td valign="middle"
                                                        style="vertical-align: top;border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; color: #ffffff; background-color: #3AAEE0; padding-top: 5px; padding-right: 20px; padding-bottom: 5px; padding-left: 20px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align: center">
                                                        <a href="<?= $_->link('login') ?>" target="_blank"
                                                           style="display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #3AAEE0;color: #ffffff">
                                                            <span style="font-size:12px;line-height:24px;">Личный кабинет</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table cellpadding="0" cellspacing="0" align="center" width="100%" border="0"
       style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
    <tbody>
    <tr style="vertical-align: top">
        <td width="100%" style="vertical-align: top;background-color: #2C2D37">
            <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0" class="container"
                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 545px;margin: 0 auto;text-align: inherit">
                <tbody>
                <tr style="vertical-align: top">
                    <td width="100%" style="vertical-align: top">
                        <div style="display: inline-block;vertical-align: top;width: 100%">
                            <table cellpadding="0" cellspacing="0" align="center" width="100%" border="0"
                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                                <tbody>
                                <tr style="vertical-align: top">
                                    <td style="vertical-align: top;padding-top: 25px;padding-right: 25px;padding-bottom: 25px;padding-left: 25px">
                                        <div style="color:#ffffff;line-height:120%;">
                                            <div>
                                                <p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center">
                                                    <span
                                                        style="font-size: 20px; line-height: 24px;"><?= $_->l('Уважаемый(ая) %client', array('client' => $client->name)) ?></span>
                                                </p></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr style="vertical-align: top">
                                    <td style="color:#B8B8C0;line-height:150%;text-align:left; vertical-align: top;padding-top: 0px;padding-right: 30px;padding-bottom: 10px;padding-left: 30px">
                                        <p style="margin: 0;font-size: 14px;line-height: 21px;text-align: left">
                                            <?= $_->l('Приоритет тикета № %id изменен администратором.', array('id' => $ticket->id)) ?>
                                            <br>
                                            <?= $_->l('Посмотреть тикет можно по ссылке:') ?>
                                        </p>
                                    </td>
                                </tr>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                                    <tbody>
                                    <tr style="vertical-align: top">
                                        <td align="center"
                                            style="vertical-align: top;padding-top: 15px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
                                            <div align="center" style="display: inline-block;padding-bottom: 20px;">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top;height: 42px">
                                                    <tbody>
                                                    <tr style="vertical-align: top">
                                                        <td valign="middle"
                                                            style="vertical-align: top;border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; color: #ffffff; background-color: #C7702E; padding-top: 5px; padding-right: 20px; padding-bottom: 5px; padding-left: 20px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align: center">
                                                            <a href="<?= \System\Tools::link('support/ticket/show?ticket_id=' . $ticket->id); ?>"
                                                               target="_blank"
                                                               style="display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;background-color: #C7702E;color: #ffffff">
                                                                <span style="font-size:16px;line-height:32px;"><span
                                                                        style="font-size: 14px; line-height: 28px;"
                                                                        data-mce-style="font-size: 14px;"><?= $_->l('Посмотреть тикет') ?></span></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table align="center" border="0" cellspacing="0"
                                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 1px solid #BBBBBB;width: 100%">
                                    <tbody>
                                    <tr style="vertical-align: top">
                                        <td align="center" style="vertical-align: top"></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div
                                    style="padding-bottom: 30px; padding-top: 20px; color:#B8B8C0;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                    <div
                                        style="font-size:12px;line-height:14px;color:#B8B8C0;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
                                        <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><?= $_->l('Если у вас возникли вопросы или пожелания, присылайте их на %email', array('email' => $site_email)) ?>
                                            <br>
                                            <?= $_->l('Мы постараемся Вам помочь.') ?></p>
                                    </div>
                                </div>
                                <table align="center" border="0" cellspacing="0"
                                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top;border-top: 1px solid #BBBBBB;width: 100%">
                                    <tbody>
                                    <tr style="vertical-align: top">
                                        <td align="center" style="vertical-align: top"></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div
                                    style="padding-top: 20px; padding-bottom: 30px; color:#FFFFFF;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                    <div
                                        style="font-size:12px;line-height:14px;color:#FFFFFF;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
                                        <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><?= $_->l('Спасибо Вам за то, что выбрали нас!') ?></p>
                                    </div>
                                </div>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr style="vertical-align: top">
        <td width="100%" style="vertical-align: top;background-color: #FFFFFF">
    <tr style="vertical-align: top;">

        <td style="vertical-align: top;background-color: transparent;text-align: center;font-size: 0">

            <div style="padding-top: 20px; display: inline-block;vertical-align: top;width: 100%">
                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                    <tbody>
                    <tr style="vertical-align: top">
                        <td align="center" valign="top" style="vertical-align: top">
                            <table border="0" cellspacing="0" cellpadding="0"
                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top">
                                <tbody>
                                <tr style="vertical-align: top">
                                    <td width="37" align="left" valign="middle" style="vertical-align: top">
                                        <a href="https://www.facebook.com/" title="Facebook" target="_blank">
                                            <img src="<?= $_->path('front/images/facebook.png') ?>" alt="Facebook"
                                                 title="Facebook" width="32"
                                                 style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
                                        </a>
                                    </td>

                                    <td width="37" align="left" valign="middle" style="vertical-align: top">
                                        <a href="http://twitter.com/" title="Twitter" target="_blank">
                                            <img src="<?= $_->path('front/images/twitter.png') ?>" alt="Twitter"
                                                 title="Twitter" width="32"
                                                 style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
                                        </a>
                                    </td>

                                    <td width="37" align="left" valign="middle" style="vertical-align: top">
                                        <a href="http://plus.google.com/" title="Google+" target="_blank">
                                            <img src="<?= $_->path('front/images/googleplus.png') ?>" alt="Google+"
                                                 title="Google+" width="32"
                                                 style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
                                        </a>
                                    </td>

                                </tr>
                                </tbody>
                            </table>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div
                style="padding-top: 20px; color:#959595;line-height:150%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                <div
                    style="font-size:12px;line-height:18px;color:#959595;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
                    <p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center"><?= $_->l('Не отвечайте на это письмо, оно отправлено автоматически.') ?></p>
                    <p style="margin: 0;font-size: 14px;line-height: 21px;text-align: center"><?= $_->l('Вы получили это письмо, потому что являетесь клиентом %sitename', array('sitename' => $site_name)) ?></p>
                </div>
            </div>
        </td>
    </tr>
    </td>
    </tr>
    </tbody>
</table>

</body>
</html>