<?php
namespace admin;


use tools\NMail;

use System\Path;

use System\Tools;

class BugReportController
{
    public function run()
    {
        $comment     = Tools::rPOST('comment');
        $screen      = Tools::rPOST('screenshot');
        $encodedData = str_replace(' ', '+', $screen);
        $encodedData = preg_replace('/^data:image\/(png|jpg);base64,/', '', $encodedData);

        $decocedData = base64_decode($encodedData);
        $screen      = ($decocedData);


        @file_put_contents(Path::getRoot('storage/tmp/screen.png'), $screen);


        $mail = new NMail('bugreport@hopebilling.com', '', 'New bug report', $comment, '');
      //  $mail->add_header('Content-Type: text/html; charset=UTF-8');
        $mail->from = Tools::rPOST('email');
        if(file_exists(Path::getRoot('storage/tmp/screen.png'))) {
            $mail->add_attachment(Path::getRoot('storage/tmp/screen.png'));
        }

        $mail->send();

        @unlink(Path::getRoot('storage/tmp/screen.png'));

        echo json_encode(array('result' => 'success'));
    }
}