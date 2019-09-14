<?php

namespace hosting;

interface IHostingAPI
{
    /**
     * Check connection with the hosting panel
     * @return integer result code
    */
    public function checkConnection();

    /**
     * Suspending user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function suspendUser($userName);

    /**
     * Unsuspending user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function unsuspendUser($userName);


    /**
     * Checking existence of the user account in the hosting panel
     * @param $userName
     * @return integer result code
     */
    public function userExist($userName);


    /**
     * Change user account password in the hosting panel
     * @param string $userName
     * @param string $newPassword
     * @return integer result code
     */
    public function changeUserPassword($userName, $newPassword);

    /**
     * Checking existence of the plan in the hosting panel
     * @param string $planName
     * @return integer result code
     */
    public function planExist($planName);


    /**
     * Get all plans of the hosting panel
     * @return array plans
     */
    public function getPlans();


    /**
     * @param string $userName
     * @param $newPlanName
     * @return integer result code
     */
    public function changePlan($userName, $newPlanName);


    /**
     * Creating new account in the hosting panel
     * @param array $data
     * @return integer result code
     */
    public function createUser($data);


    /**
     * Removing the user account in the hosting panel
     * @param string $userName
     * @return integer result code
     */
    public function removeUser($userName);

}