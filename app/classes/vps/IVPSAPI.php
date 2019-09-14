<?php

namespace vps;


interface IVPSAPI
{
    /**
     * Function return array of nodes
     * @return array
     */
    public function returnNodesList();

    /**
     * Function create new VM and delegate it to user
     * @param $node string Node of VPS
     * @param $type integer Type of VPS server (0 - ISO, 1 - LXC)
     * @param $memory integer Memory size in Mb
     * @param $hdd integer HDD size in Gb
     * @param $cores integer Number of cores
     * @param $image string Image for cdrom
     * @param $socket integer Socket number
     * @param $user string UserID in VPS environment
     * @param $password string User password for VM
     * @return object result
     */
    public function createVM($node, $type, $memory, $hdd, $cores, $image, $socket, $user, $password, $net_type);


    /**
     * Function return Images in server
     * @param $node string Node name
     * @return array
     */
    public function returnImagesList($node);


    /**
     * Function create new User in VPS environment
     * @param $login
     * @param $full_name
     * @param $email
     * @param $password
     * @return object result
     */
    public function createUser($login, $full_name, $email, $password);


    /**
     * @param $node string Node of VM
     * @param $vmid string VM id
     * @param $username string Username
     * @return object result
     */
    public function removeVM($node, $vmid, $username, $type);


    /**
     * @param $username string Username
     * @return object result
     */
    public function removeUser($username);


    /**
     * Suspend VM and User
     * @param $node string Node
     * @param $vmid string VM ID
     * @param $username string Username
     * @return object result
     */
    public function suspendVM($node, $vmid, $username, $type);

    /**
     * Resume VM and User
     * @param $node string Node
     * @param $vmid string VM ID
     * @param $username string Username
     * @return object result
     */
    public function unsuspendVM($node, $vmid, $username, $type);


    public function checkConnection();
}