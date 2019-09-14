<?php

namespace admin;

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

class TicketsController extends FrontController
{
    public function actionIndex()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
        $this->layout->ticketList = true;
    }

    public function actionCloseAjax()
    {
        $ticket = new Ticket(Router::getParam('id_ticket'));
        $ticket->close();
        echo json_encode(array('result' => 1));
    }

    public function actionCheckerGetTicketsCountAjax(){
        $newTicket = false;
        
        $t = new Ticket();
        $count = $t->select('*')->getRowsCount();
        if (isset($_POST["data"]) && ($_POST["data"]["count_t"] > 0) && $count > $_POST["data"]["count_t"]){
            $newTicket = true;
            $countNewTickets = $count - $_POST["data"]["count_t"];
            $tickets = $t->limit($countNewTickets)->order('date', 'desc')->getRows();
            $items = [];
            foreach ($tickets as $ticket){
                $client = new Client($ticket->client_id);
                $items[] = [
                    'id' => $ticket->id,
                    'client' => $client->name,
                    'subject' => $ticket->subject
                ];
            }
        }
        
        $ta = new TicketAnswer();
        $countTA = $ta->select('*')->getRowsCount();
        if (!$newTicket && isset($_POST["data"]) && ($_POST["data"]["count_ta"] > 0) && $countTA > $_POST["data"]["count_ta"]){
            $countNewTickets = $countTA - $_POST["data"]["count_ta"];
            $ticketsAnsers = $ta->limit($countNewTickets)->order('date', 'desc')->getRows();
            $items = [];
            foreach ($ticketsAnsers as $ticketsAnser){
                $client = new Client($ticketsAnser->client_id);
                $ticket = new Ticket($ticketsAnser->ticket_id);
                $items[] = [
                    'ticket_id' => $ticket->id,
                    'client' => $client->name,
                    'subject' => $ticket->subject
                ];
            }
        }

        $t2 = new Ticket();
        $openTicketsCount = $t2->where('status', -1)->getRowsCount();
        
        echo json_encode(array(
            'count' => $count + $countTA,
            'items' => isset($items) ? $items : [],
            'open_ticket_count' => $openTicketsCount,
            'additional' => [
                'count_t' => $count,
                'count_ta' => $countTA
            ]
        ));
        exit();
    }

    public function actionCheckerGetTicketsMessagesCountAjax(){
        $ta = new TicketAnswer();
        $countTA = $ta->select('*')->getRowsCount();
        if (isset($_POST["count"]) && $countTA > $_POST["count"]){
            $countNewTickets = $countTA - $_POST["count"];
            $ticketsAnsers = $ta->limit($countNewTickets)->order('date', 'desc')->getRows();
            $items = [];
            foreach ($ticketsAnsers as $ticketsAnser){
                $client = new Client($ticketsAnser->client_id);
                $ticket = new Ticket($ticketsAnser->ticket_id);
                $items[] = [
                    'ticket_id' => $ticket->id,
                    'client' => $client->name,
                    'subject' => $ticket->subject
                ];
            }
        }
        echo json_encode(array(
            'count' => $countTA,
            'items' => isset($items) ? $items : []
        ));
        exit();
    }

    public function actionIndexAjax()
    {
        $view = $this->getView('ticket/list.php');
        $this->layout->import('content', $view);
        $t = new Ticket();
        $t->select('*')->select(Client::getInstance(), 'name', 'user')->join(Client::getInstance(), 'client_id', 'id');
        $tickets = $t->limit($this->from, $this->count)->order('date', 'desc')->getRows();

        $view->pagination = $this->pagination($t->lastQuery()->getRowsCount());

        foreach ($tickets as &$ticket) {
            $ticketAnswerObject = new TicketAnswer();
            $ticket->count_new = $ticketAnswerObject->select('id')->where('ticket_id', $ticket->id)->where('is_new', 1)->where('client_id', '!=', 0)->getRowsCount();
        }

        $view->tickets    = $tickets;
    }

    public function actionNewTicket()
    {
        $view = $this->getView('ticket/new.php');
        $this->layout->import('content', $view);

        if (Tools::rPOST()) {


            $ticket            = new Ticket();
            $ticket->subject   = strip_tags(Tools::rPOST('subject'));
            $ticket->message   = strip_tags(Tools::rPOST('message'));
            $ticket->priority  = Tools::rPOST('priority');
            $ticket->client_id = Tools::rPOST('client_id');
            $ticket->status    = -1;

            if ($ticket->validateFields() && $ticket->save()) {

                if (!is_dir(_BASE_DIR_STORAGE_ . 'tickets/' . $ticket->id . '/')) {
                    mkdir(_BASE_DIR_STORAGE_ . 'tickets/' . $ticket->id . '/');
                }
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

                $client = new Client(Tools::rPOST('client_id'));
                /*Send alert to client*/
               Notifier::NewTicket($client, $ticket);

                Tools::redirect('admin/tickets');
            } else {
                // print_r($ticket->getValidationErrors());
            }

        }
    }
    
    public function actionChangePriority()
    {
        $priority  = Router::getParam('priority');
        $ticket_id = Router::getParam('id_ticket');

        $ticket           = new Ticket($ticket_id);
        $ticket->priority = $priority;
        $ticket->save();

        Notifier::TicketChangePriority($ticket);

        Tools::redirect('/admin/ticket/' . $ticket->id);
    }

    public function actionChangeStatus()
    {
        $status    = Router::getParam('status');
        $ticket_id = Router::getParam('id_ticket');

        $ticket         = new Ticket($ticket_id);
        $ticket->status = $status;
        $ticket->save();

        Notifier::TicketChangeStatus($ticket);


        Tools::redirect('/admin/ticket/' . $ticket->id);
    }

    public function actionRemoveAjax()
    {
        $ticket = new Ticket(Router::getParam('id_ticket'));
        if ($ticket->remove()) {
            echo json_encode(['result' => 1]);
        }

    }

    public function actionDownloadAnswerFile()
    {
        $filename         = Router::getParam('filename');
        $id_answer_ticket = Router::getParam('id_answer');


        if (file_exists(Path::getRoot('storage/answers/' . $id_answer_ticket . '/' . $filename))) {

            header("Content-Type: application/download");
            header("Content-Disposition: attachment; filename=" . $filename . "");
            header("Content-Length: " . filesize(Path::getRoot('storage/answers/' . $id_answer_ticket . '/' . $filename)));
            $fp = fopen(Path::getRoot('storage/answers/' . $id_answer_ticket . '/' . $filename), "r");
            fpassthru($fp);

            // echo Path::getURL('storage/tickets/' . $id_ticket . '/' . $filename);
        }
    }

    public function actionGetClientsAjax()
    {
        $Client = new Client();

        $clients = $Client
            ->where('name', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->whereOr()
            ->where('username', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->whereOr()
            ->where('phone', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->whereOr()
            ->where('email', 'LIKE', '%' . Tools::rPOST('q') . '%')
            ->getRows();

        echo json_encode(['results' => $clients]);
    }
    
    public function actionDownloadFile()
    {
        $filename  = Router::getParam('filename');
        $id_ticket = Router::getParam('id_ticket');

        if (file_exists(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename))) {

            header("Content-Type: application/download");
            header("Content-Disposition: attachment; filename=" . $filename . "");
            header("Content-Length: " . filesize(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename)));
            $fp = fopen(Path::getRoot('storage/tickets/' . $id_ticket . '/' . $filename), "r");
            fpassthru($fp);

            // echo Path::getURL('storage/tickets/' . $id_ticket . '/' . $filename);
        }

    }

    public function actionTicket($ajax = false)
    {
        $view = $this->getView('ticket/edit.php');
        $this->layout->import('content', $view);
        $this->layout->ticketAnwerList = true;
        $ticket       = new Ticket(Router::getParam('id_ticket'));

        $view->ticket = $ticket;

        if (Tools::rPOST('comment')) {
            $ta              = new TicketAnswer();
            $ta->ticket_id   = $ticket->id;
            $ta->employee_id = $this->employee->id;
            $ta->answer      = strip_tags(Tools::rPOST('comment'));
            $ta->save();

            $client = new Client($ticket->client_id);

            Notifier::TicketAnswer($client, $ticket);

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
                $r = $icou->upload();
            }
            Tools::reload();
        }

        $answers = TicketAnswer::getInstance()->where('ticket_id', $ticket->id)->getRows();
        foreach ($answers as &$answer) {
            $answer = new TicketAnswer($answer);

            if ($answer->is_new && $answer->client_id != 0) {

                $answer->is_new = 0;
                $answer->save();
            }

            $answer->getFiles();

            if ($answer->client_id) {
                $answer->admin = 0;
                $answer->author = new Client($answer->client_id);
            } else {
                $answer->admin = 1;
                $answer->author = new Employee($answer->employee_id);
            }

            $answer->days = Tools::time2ago($answer->date);
        }

        $view->answers = $answers;
        if ($ajax) $view->ajax = 1;
    }

    public function actionTicketAjax()
    {
        $this->actionTicket(true);
    }
}