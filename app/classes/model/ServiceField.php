<?php
namespace model;

use System\ObjectModel;

class ServiceField extends ObjectModel{
    public static $table = 'service_fields';

    public function parseSelectValues($field_value=null)
    {
        $values = explode('|', $this->values);
        $new_values = array();

        foreach ($values as &$value){
            $price = 0;
            $old_value = $value;

            preg_match('/\[(.*)\]/', $value, $match);
            if(isset($match[1])){
                $price = $match[1];
            }


            $value = preg_replace('/\[.*\]/', '', $value);
            $new_values[$old_value] = (object)array( 'value' => $value, 'price' => $price);

            if($field_value && $old_value == $field_value){

                return $new_values[$old_value];
            }
        }
        return $new_values;
    }
    public function parseRangeValues()
    {
        $values =$this->values;
        if(strpos($values, '|..|')!== false){ //it's set as range
            $values = explode('|', $values);
            $first = $values[0];
            $two = $values[1];
            $price = 0;
            preg_match('/\[(.*)\]/', $two, $match);
            if(isset($match[1])){
                $price = $match[1];
            }
            $two = preg_replace('/\[.*\]/', '', $two);
            $last  = $values[3];

            return (object)array('from' => $first, 'to'=> $last, 'step'=> $two-$first,'price' => $price);
        }
    }

    public function remove(){

        $ServiceFieldValue = new ServiceFieldValue();
        $ServiceFieldValue->where('field_id', $this->id)->removeRows();

        parent::remove();
    }



}