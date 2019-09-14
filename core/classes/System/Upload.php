<?php

namespace System;
use finfo;


class Upload {


    /**
     * Default directory persmissions (destination dir)
     */
    protected $default_permissions = 750;


    /**
     * File post array
     *
     * @var array
     */
    protected $files_post = array();


    /**
     * Destination directory
     *
     * @var string
     */
    protected $destination;


    /**
     * Fileinfo
     *
     * @var object
     */
    protected $finfo;


    /**
     * Data about file
     *
     * @var array
     */
    public $file = array();

    protected $file_post;

    /**
     * Max. file size
     *
     * @var int
     */
    protected $max_file_size;


    /**
     * Allowed mime types
     *
     * @var array
     */
    protected $mimes = array();


    /**
     * External callback object
     *
     * @var obejct
     */
    protected $external_callback_object;


    /**
     * External callback methods
     *
     * @var array
     */
    protected $external_callback_methods = array();


    /**
     * Temp path
     *
     * @var string
     */
    protected $tmp_name;


    /**
     * Validation errors
     *
     * @var array
     */
    protected $validation_errors = array();


    /**
     * Filename (new)
     *
     * @var string
     */
    protected $filename;


    /**
     * Internal callbacks (filesize check, mime, etc)
     *
     * @var array
     */
    private $callbacks = array();

    /**
     * Root dir
     *
     * @var string
     */
    protected $root;

    /**
     * Return upload object
     *
     * $destination		= 'path/to/your/file/destination/folder';
     *
     * @param string $destination
     * @param string $root
     * @return Upload
     */
    public static function factory($destination, $root = false) {

        return new Upload($destination, $root);

    }


    /**
     *  Define ROOT constant and set & create destination path
     *
     * @param string $destination
     * @param string $root
     */
    public function __construct($destination, $root = false) {

        if ($root) {
            $this->root = $root;
        } else {
            $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        }

        // set & create destination path
        if (!$this->setDestination($destination)) {
            throw new Exception('Upload: Can\'t create destination. '.$this->root . $this->destination);
        }

        //create finfo object
        $this->finfo = new finfo();
    }

    /**
     * Set target filename
     *
     * @param string $filename
     */
    public function setFilename($filename) {

        $this->filename = $filename;

    }

    /**
     * Check & Save file
     *
     * Return data about current upload
     *
     * @return array
     */
    public function upload($filename = '') {

        if ($this->check()) {
            $this->save();
        }

        // return state data
        return $this->getFile();

    }


    /**
     * Save file on server
     *
     * Return state data
     *
     * @return array
     */
    public function save() {

        $this->saveFile();
        return $this->getFile();

    }


    /**
     * Validate file (execute callbacks)
     *
     * Returns TRUE if validation successful
     *
     * @return bool
     */
    public function check() {

        //execute callbacks (check filesize, mime, also external callbacks
        $this->validate();

        //add error messages
        $this->file['errors'] = $this->getErrors();

        //change file validation status
        $this->file['status'] = empty($this->validation_errors);

        return $this->file['status'];

    }


    /**
     * Get current state data
     *
     * @return array
     */
    public function getFile() {

        return $this->file;

    }


    /**
     * Save file on server
     */
    protected function saveFile() {

        //create & set new filename
        if(empty($this->filename)){
            $this->createNewFilename();
        }

        //set filename
        $this->file['filename']	= $this->filename;

        //set full path
        $this->file['full_path'] = $this->root . $this->destination . $this->filename;
        $this->file['path'] = $this->destination . $this->filename;

        $status = move_uploaded_file($this->tmp_name, $this->file['full_path']);

        //checks whether upload successful
        if (!$status) {
            throw new Exception('Upload: Can\'t upload file.');
        }

        //done
        $this->file['status']	= true;

    }


    /**
     * Set data about file
     */
    protected function setFileData() {

        $file_size = $this->getFileSize();

        $this->file = array(
            'status'				=> false,
            'destination'			=> $this->destination,
            'size_in_bytes'			=> $file_size,
            'size_in_mb'			=> $this->bytesToMb($file_size),
            'mime'					=> $this->getFileMime(),
            'original_filename'		=> $this->file_post['name'],
            'tmp_name'				=> $this->file_post['tmp_name'],
            'post_data'				=> $this->file_post,
        );

    }

    /**
     * Set validation error
     *
     * @param string $message
     */
    public function setError($message) {

        $this->validation_errors[] = $message;

    }


    /**
     * Return validation errors
     *
     * @return array
     */
    public function getErrors() {

        return $this->validation_errors;

    }


    /**
     * Set external callback methods
     *
     * @param object $instance_of_callback_object
     * @param array $callback_methods
     */
    public function callbacks($instance_of_callback_object, $callback_methods) {

        if (empty($instance_of_callback_object)) {

            throw new Exception('Upload: $instance_of_callback_object can\'t be empty.');

        }

        if (!is_array($callback_methods)) {

            throw new Exception('Upload: $callback_methods data type need to be array.');

        }

        $this->external_callback_object	 = $instance_of_callback_object;
        $this->external_callback_methods = $callback_methods;

    }


    /**
     * Execute callbacks
     */
    protected function validate() {

        //get curent errors
        $errors = $this->getErrors();

        if (empty($errors)) {

            //set data about current file
            $this->setFileData();

            //execute internal callbacks
            $this->executeCallbacks($this->callbacks, $this);

            //execute external callbacks
            $this->executeCallbacks($this->external_callback_methods, $this->external_callback_object);

        }

    }


    /**
     * Execute callbacks
     */
    protected function executeCallbacks($callbacks, $object) {

        foreach($callbacks as $method) {

            $object->$method($this);

        }

    }

    public function getExtension($filename)
    {
        $res = pathinfo($filename, PATHINFO_EXTENSION);
        return $res;
    }


    /**
     * File mime type validation callback
     *
     * @param obejct $object
     */
    protected function checkMimeType($object) {

        if (!empty($object->mimes)) {

            if (!in_array($object->file['mime'], $object->mimes)) {

                $object->setError('Mime type not allowed.');

            }

        }

    }


    /**
     * Set allowed mime types
     *
     * @param array $mimes
     */
    public function setAllowedMimeTypes($mimes) {

        $this->mimes		= $mimes;

        //if mime types is set -> set callback
        $this->callbacks[]	= 'checkMimeType';

    }


    /**
     * File size validation callback
     *
     * @param object $object
     */
    protected function checkFileSize($object) {

        if (!empty($object->max_file_size)) {

            $file_size_in_mb = $this->bytesToMb($object->file['size_in_bytes']);

            if ($object->max_file_size <= $file_size_in_mb) {

                $object->setError('File is too big.');

            }

        }

    }


    /**
     * Set max. file size
     *
     * @param int $size
     */
    public function setMaxFileSize($size) {

        $this->max_file_size	= $size;

        //if max file size is set -> set callback
        $this->callbacks[]	= 'checkFileSize';

    }





    /**
     * Set file array
     *
     * @param array $file
     */
    public function setFileArray($file) {

        //checks whether file array is valid
        if (!$this->checkFileArray($file)) {

            //file not selected or some bigger problems (broken files array)
            $this->setError('Please select file.');

        }

        //set file data
        $this->file_post = $file;

        //set tmp path
        $this->tmp_name  = $file['tmp_name'];

    }


    /**
     * Checks whether Files post array is valid
     *
     * @return bool
     */
    protected function checkFileArray($file) {

        return isset($file['error'])
        && !empty($file['name'])
        && !empty($file['type'])
        && !empty($file['tmp_name'])
        && !empty($file['size']);

    }


    /**
     * Get file mime type
     *
     * @return string
     */
    protected function getFileMime() {

        return $this->finfo->file($this->tmp_name, FILEINFO_MIME_TYPE);

    }


    /**
     * Get file size
     *
     * @return int
     */
    protected function getFileSize() {

        return filesize($this->tmp_name);

    }


    /**
     * Set destination path (return TRUE on success)
     *
     * @param string $destination
     * @return bool
     */
    protected function setDestination($destination) {

        $this->destination = $destination . DIRECTORY_SEPARATOR;

        return $this->destinationExist() ? TRUE : $this->createDestination();

    }


    /**
     * Checks whether destination folder exists
     *
     * @return bool
     */
    protected function destinationExist() {

        return is_writable($this->root . $this->destination);

    }


    /**
     * Create path to destination
     *
     * @param string $dir
     * @return bool
     */
    protected function createDestination() {

        return mkdir($this->root . $this->destination, $this->default_permissions, true);

    }


    /**
     * Set unique filename
     *
     * @return string
     */
    protected function createNewFilename() {

        $filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
        $this->setFilename($filename);

    }


    /**
     * Convert bytes to mb.
     *
     * @param int $bytes
     * @return int
     */
    protected function bytesToMb($bytes) {

        return round(($bytes / 1048576), 2);

    }


} // end of Upload