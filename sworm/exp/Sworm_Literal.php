<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 23:17
 */
class Sworm_Literal extends Sworm_Abstract
{
    protected $codes;

    /**
     * Sworm_Literal constructor.
     */
    public function __construct($code)
    {
        $this->codes = $code;
    }

    public function _get(){
        return " = ".$this->codes;
    }
}