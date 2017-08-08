<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 16:29
 */
abstract class Sworm_Abstract
{
    protected $connection, $options;

    /**
     * Sworm_Abstract constructor.
     */
    public function __construct()
    {

    }

    public function escape($str){
        if(isset($this->connection)){
            return $this->connection->escape($str);
        }else{
            return $str;
        }
    }
}