<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 23:35
 */
class Sworm_NotIn extends Sworm_Abstract
{
    protected $mArray;
    /**
     * Sworm_In constructor.
     */
    public function __construct($array)
    {
        $this->mArray = $array;
    }

    public function _get(){
        $ins = "";
        if(is_array($this->mArray)){
            foreach ($this->mArray as $item){
                $ins .= "'$item', ";
            }
            if($ins!=""){
                $ins = substr($ins, 0, -2);
            }
        }else{
            $ins = $this->mArray;
        }
        return " NOT IN (".$ins.")";
    }
}