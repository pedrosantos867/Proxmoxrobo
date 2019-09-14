<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 13.06.15
 * Time: 20:28
 */

namespace model;


use System\ObjectModel;
use System\Validation;

class Ticket extends ObjectModel
{
    protected static $table = 'tickets';
    public $files = [];

    public function close()
    {
        $this->status = 1;
        $this->save();
    }

    public function validateFields()
    {

        if ($this->subject == '') {
            $this->validateErrors[] = 'subject';
        }

        if ($this->message == '') {
            $this->validateErrors[] = 'message';
        }

        if (empty($this->validateErrors)) {
            return true;
        }

        return false;

    }

    public function getFiles()
    {
        if (is_dir(_BASE_DIR_STORAGE_ . 'tickets/' . $this->id)) {
            $files = scandir(_BASE_DIR_STORAGE_ . 'tickets/' . $this->id);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $this->files[] = $file;
                }
            }
        }

        return $this->files;
    }

    public function remove()
    {
        if (is_dir(_BASE_DIR_STORAGE_ . 'tickets/' . $this->id)) {
            $files = $this->getFiles();

            foreach ($files as $file) {
                @unlink(_BASE_DIR_STORAGE_ . 'tickets/' . $this->id . '/' . $file);
            }
            rmdir(_BASE_DIR_STORAGE_ . 'tickets/' . $this->id);
        }
        $ta = new TicketAnswer();
        $ta->where('ticket_id', $this->id)->removeRows();

        return parent::remove();
    }
} 