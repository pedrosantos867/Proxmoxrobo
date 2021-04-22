<?php

namespace admin;

use System\Router;
use model\BackupServer;
use System\Exception;

class BackupServersController extends FrontController{
    public function actionList()
    {
        $view = $this->getView('empty-ajax-block.php');
        $this->layout->import('content', $view);
    }

    public function actionListAjax()
    {
        $view = $this->getView('backup/server/list.php');
        
        $backupServerObject = new BackupServer();
        
        $backupServerObject
            ->select('id')
            ->select('name')
            ->select('address')
            ->select('retention')
            ->select('datastore');
            
        $rows = $backupServerObject->getRows();
            
        $view->backupServers = $rows;
        $this->layout->import('content', $view);
    }
}

