<?php
namespace front;

use email\Email;
use model\Client;
use model\Employee;
use model\Ticket;
use model\TicketAnswer;
use System\FileUpload;
use System\Notifier;
use System\Path;
use System\Router;
use System\Tools;
use System\Upload;
use System\View\View;

class SupportController extends FrontController
{
    public function actionIndexAjax()
    {
        $this->actionIndex(true);
    }

    public function actionIndex($ajax = false)
    {
        $view = $this->getView('support/tickets.php');
        $ticketObject = new Ticket();

        $tickets = $ticketObject->where('client_id', $this->client->id)->limit($this->from, $this->count)->order('date', 'desc')->getRows();

        foreach ($tickets as &$ticket) {
            $ticketAnswerObject = new TicketAnswer();
            $ticket->count_new = $ticketAnswerObject->select('id')->where('ticket_id', $ticket->id)->where('is_new', 1)->where('employee_id', '!=', 0)->getRowsCount();
        }

        $view->tickets = $tickets;
        $this->pagination($ticketObject->lastQuery()->getRowsCount());
        $this->layout->import('content', $view);
    }

    public function actionCheckerGetMessagesCountAjax(){

        $t = new TicketAnswer();
        $count
            = $t->select('*')->getRowsCount();

        if (isset($_POST["count"]) && $count > $_POST["count"]){
            $newMessagesCount = $count - $_POST["count"];
            $ticketsAnsers = $t
                ->select('*')
                ->where('client_id', 0)
                ->order('date', 'desc')
                ->join(Ticket::factory(), 'ticket_id', 'id')
                ->where(Ticket::factory(), 'client_id', $this->client->id)
                ->limit($newMessagesCount)
                ->getRows();
            $items = [];

            foreach ($ticketsAnsers as $ticketsAnser){
                $ticket = new Ticket($ticketsAnser->ticket_id);

                $items[] = [
                    'ticket_id' => $ticket->id,
                    'subject' => $ticket->subject
                ];
            }
        }
        echo json_encode(array(
            'count' => $count,
            'items' => isset($items) ? $items : []
        ));
        exit();
    }

    public function actionShow()
    {
        $view = $this->getView('support/ticket.php');
        $id_ticket    = Tools::rGET('ticket_id');
        $ticket       = new Ticket($id_ticket);

        $this->checkAccess($ticket);


        $ticket->getFiles();
        $view->ticket = $ticket;
        // print_r($ticket->files);

        if (Tools::rPOST('comment')) {
            $ta            = new TicketAnswer();
            $ta->ticket_id = $ticket->id;
            $ta->client_id = $this->client->id;
            $ta->answer    = Tools::clearXSS(Tools::rPOST('comment'));
            $ta->is_new = 1;
            $ta->save();

            $ticket->save();

            Notifier::TicketAnswer($this->client, $ticket);

            if (!is_dir(_BASE_DIR_STORAGE_ . 'answers/' . $ta->id . '/')) {
                mkdir(_BASE_DIR_STORAGE_ . 'answers/' . $ta->id . '/');
            }

            for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                $icou = new Upload(('storage/answers/' . $ta->id . '/'));
                $icou->setFileArray(
                    array(
                        'name'     => $_FILES['files']['name'][$i],
                        'type'     => $_FILES['files']['type'][$i],
                        'tmp_name' => $_FILES['files']['tmp_name'][$i],
                        'size'     => $_FILES['files']['size'][$i],
                        'error'    => $_FILES['files']['error'][$i]
                    )
                );
                $icou->setFilename(Tools::transliteration($_FILES['files']['name'][$i]));
                $icou->setMaxFileSize(5);
                $icou->upload();
            }
            Tools::reload();
        }

        $answers = TicketAnswer::getInstance()->where('ticket_id', $ticket->id)->getRows();

        foreach ($answers as &$answer) {

            $answer            = new TicketAnswer($answer);

            if ($answer->is_new && $answer->employee_id != 0) {

                $answer->is_new = 0;
                $answer->save();
            }


            if ($answer->client_id) {
                $answer->author = new Client($answer->client_id);
                $answer->admin = 0;
            } else {
                $answer->author = new Employee($answer->employee_id);
                $answer->admin = 1;
            }
            $answer->getFiles();
            //  $datetime1 = new \DateTime();
            // $datetime2 = new \DateTime($answer->date);
            // $interval = $datetime1->diff($datetime2);
            $answer->days = Tools::time2ago($answer->date);
        }

        $view->answers = $answers;

        $this->layout->import('content', $view);
        $this->layout->ticketAnwerList = true;
    }

    public function actionShowAjax()
    {
        $this->actionShow();
    }

    public function actionDownloadAnswerFile()
    {
        $filename      = Router::getParam('filename');
       // echo $filename;
        $id_answer     = Router::getParam('id_answer');
        $ticket_answer = new TicketAnswer($id_answer);
        $ticket        = new Ticket($ticket_answer->ticket_id);
        $this->checkAccess($ticket);

            if (file_exists(Path::getRoot('storage/answers/' . $id_answer . '/' . $filename))) {
                header("Content-Type: application/download");
                header("Content-Disposition: attachment; filename=" . $filename . "");
                header("Content-Length: " . filesize(Path::getRoot('storage/answers/' . $id_answer . '/' . $filename)));
                $fp = fopen(Path::getRoot('storage/answers/' . $id_answer . '/' . $filename), "r");
                fpassthru($fp);
            }

    }

    public function actionDownloadFile()
    {
        $filename  = Router::getParam('filename');
        $id_ticket = Router::getParam('id_ticket');
        $ticket    = new Ticket($id_ticket);
        $this->checkAccess($ticket);


            if (file_exists(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename))) {

                header("Content-Type: application/download");
                header("Content-Disposition: attachment; filename=" . $filename . "");
                header("Content-Length: " . filesize(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename)));
                $fp = fopen(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename), "r");
                fpassthru($fp);

                // echo Path::getURL('storage/tickets/' . $id_ticket . '/' . $filename);
            }

    }

    public function actionCloseAjax()
    {
        $id_ticket      = Tools::rGET('ticket_id');
        $ticket         = new Ticket($id_ticket);
        $this->checkAccess($ticket);

        $ticket->status = 1;
        $ticket->save();
        $this->returnAjaxAnswer(1, 'Тикет закрыт');
    }

    public function actionNew()
    {
        $view = $this->getView('support/new_ticket.php');


        $errors = array();
        //print_r($_POST);
        if (Tools::rPOST()) {



            for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                if (round(($_FILES['files']['size'][$i] / 1048576), 2) >= 5) {
                    $errors[] = 'file_greater5';
                }
            }

            $ticket           = new Ticket();
            $ticket->subject  = (Tools::rPOST('subject'));
            $ticket->message  = (Tools::rPOST('message'));
            $ticket->priority = intval(Tools::rPOST('priority'));
            $ticket->client_id = $this->client->id;
            $ticket->status   = -1;

            if (empty($errors) && $ticket->validateFields() && $ticket->save()) {


                if (!is_dir(_BASE_DIR_STORAGE_ . 'tickets/' . $ticket->id . '/')) {
                    mkdir(_BASE_DIR_STORAGE_ . 'tickets/' . $ticket->id . '/');
                }


                if (empty($errors)) {

                for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                    $icou = new Upload(('storage/tickets/' . $ticket->id . '/'));
                    $icou->setFileArray(
                        array(
                            'name'     => $_FILES['files']['name'][$i],
                            'type'     => $_FILES['files']['type'][$i],
                            'tmp_name' => $_FILES['files']['tmp_name'][$i],
                            'size'     => $_FILES['files']['size'][$i],
                            'error'    => $_FILES['files']['error'][$i]
                        )
                    );
                    $icou->setFilename(Tools::transliteration($_FILES['files']['name'][$i]));
                    $icou->setMaxFileSize(5);
                    $icou->upload();

                }

                Notifier::NewTicket($this->client, $ticket);



                Tools::redirect('/support');
            }
            } else {

            }

        }
        $view->errors = $errors;
        $this->layout->import('content', $view);
    }
}