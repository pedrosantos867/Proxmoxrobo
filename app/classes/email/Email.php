<?php

namespace email;

use System\Config;
use System\View\View;

class Email extends \System\Mail
{

    public $isSmtp = false;

    public $from;
    public $headers = array();

    public $files;

    protected $template = 'default';

    public function __construct(){
        $this->from = Config::factory()->notification_email;
        $this->template = Config::factory()->email_template;
    }

    public function getView($name, $id_lang = null, $panel = 'front'){
        $view = new View('email/'.$this->template.'/'.$panel, $name);

        if($id_lang){
            $view->setDataToHelper('force_lang', $id_lang);
        }
        return $view;
    }

    public function addAttachment($file_name, $base64_encoded_file, $file_type = ''){

        $this->files[] = array('name' => $file_name, 'content' => $base64_encoded_file);
    }



    private function prepareMessage(){
        $boundary = md5(date('r', time()));

        if (!empty($this->from)) {
            $this->headers['From'] = "From: " . $this->from;
            $this->from = null;
        }


        $this->headers['MIME-Version'] = "MIME-Version: 1.0";
        $this->headers['Content-Type'] = "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"";
        $this->headers['Content-Transfer-Encoding'] = "Content-Transfer-Encoding: 8bit";
        $this->headers[''] = "This is a multi-part message in MIME format.";

        $this->body .= "--".$boundary.PHP_EOL;
        $this->body .= "Content-Type: text/html; charset=\"utf-8\"\n";
        $this->body .= "Content-Transfer-Encoding: 7bit\n\n";
        $this->body .= $this->msg. "\n\n";


        if(isset($this->files)) {
            foreach ($this->files as $file) {
                // Attachment
                $this->body .= "--".$boundary.PHP_EOL;
                $this->body .= "Content-Type: application/octet-stream; name=\"{$file['name']}\"\n";
                $this->body .= "Content-Transfer-Encoding: base64\n";
                $this->body .= "Content-Disposition: attachment\n\n";

                $this->body .= $file['content'] . "\n\n";

            }
        }
        $this->body .= "--" . $boundary . "--";
    }

    public function send() {

        $subject_match = '';
        preg_match('/{s}(.*){\/s}/', $this->msg, $subject_match);
        $this->subject = isset($subject_match[1]) ? $subject_match[1] : 'Billing Notification';

        if(isset($subject_match[0])) {
            $this->msg = str_replace($subject_match[0], '', $this->msg);
        }


        $this->prepareMessage();

        $config = Config::factory();

        if($config->email_method == 'smtp') {

            $mail = new SMTPClient($config->smtp_server, $config->smtp_port);

            if ($config->smtp_protocol) {
                $mail->setProtocol(SMTPClient::TLS);
            } else {
                $mail->setProtocol(SMTPClient::SSL);
            }


            $mail->setHeaders($this->headers);

            $mail->setLogin($config->smtp_username, $config->smtp_password);
            $mail->setFrom($config->smtp_email);
            $mail->addTo($this->to);
            $mail->setSubject($this->subject);

            $mail->setMessage(trim($this->body));

            return $mail->send();

        } else{

           return parent::send();
        }
    }

}