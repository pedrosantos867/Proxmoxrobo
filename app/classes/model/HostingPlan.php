<?php
/**
 * Created by PhpStorm.
 * Client: Viktor
 * Date: 09.06.15
 * Time: 15:42
 */
namespace model;
class HostingPlan extends \System\ObjectModel
{
    protected static $table = 'hosting_plans';
    protected $_sortable = true;

    public function getServers()
    {
        return explode('|', trim($this->aviable_servers, '|'));
    }

    public function remove()
    {
        if (parent::remove()) {
            $hp = new HostingPlanDetail();
            $hp->where('plan_id', $this->removedObject->id);
            $hp->removeRows();

            $ha = new HostingAccount();
            $ha->where('plan_id', $this->removedObject->id)->removeRows();

            $HostingPrices = new HostingPlanExtendedPrice();
            $HostingPrices->where('plan_id', $this->removedObject->id)->removeRows();

            return true;
        }

        return false;

    }
} 