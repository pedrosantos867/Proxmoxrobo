<?php
/**
 * Created by PhpStorm.
 * User: Stanislav
 * Date: 29.09.2016
 * Time: 14:03
 */

namespace model;

use System\ObjectModel;

class Promocode extends ObjectModel
{
    public static $table = 'promocode';

    public function remove()
    {
        $promocodeServiceCategory = new PromocodeServiceCategory();
        $promocodeServiceCategory->where('promocode_id', $this->id)->removeRows();
        return parent::remove();
    }

    public function isAvailable($service_category_id)
    {
        if ($this->isLoadedObject()){
            if (($this->total_count > $this->used_count) && strtotime($this->end_date) > time()){
                $promocodeServiceCategory = new PromocodeServiceCategory();
                $promocodeServiceCategory = new PromocodeServiceCategory(
                    $promocodeServiceCategory   ->where('promocode_id', $this->id)
                                                ->whereAnd()
                                                ->where("service_category_id", $service_category_id)
                                                ->getRow()
                );
                if($promocodeServiceCategory->isLoadedObject()) return true;
            }
        }
        return false;
    }

    public function calcPrice($total, $service_category_id)
    {
        if ($this->isAvailable($service_category_id) && $total > 0){

            if ($this->sale_type) {// procent
                $procent = 1 - $this->sale/100;
                if ($procent >= 0 && $procent < 1) $total = $total * $procent;
            }
            else {
                if ($total >= $this->sale) $total -= $this->sale;
                else $total = 0;
            }
            $this->used_count++;
            $this->save();
        }

        return $total;
    }
}