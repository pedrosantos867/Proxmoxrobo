<?php
/**
 * Created by PhpStorm.
 * User: Viktor
 * Date: 10.06.15
 * Time: 20:52
 */

namespace model;


use System\ObjectModel;

class TicketAnswer extends ObjectModel
{
    protected static $table = 'ticket_answers';
    public $files = [];

    public function getFiles()
    {
        if (is_dir(_BASE_DIR_STORAGE_ . 'answers/' . $this->id)) {
            $files = scandir(_BASE_DIR_STORAGE_ . 'answers/' . $this->id);
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
        if (is_dir(_BASE_DIR_STORAGE_ . 'answers/' . $this->id)) {
            $files = $this->getFiles();

            foreach ($files as $file) {
                @unlink(_BASE_DIR_STORAGE_ . 'answers/' . $this->id . '/' . $file);
            }
            rmdir(_BASE_DIR_STORAGE_ . 'answers/' . $this->id);
        }

        return parent::remove();
    }
} 