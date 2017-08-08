<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 23:17
 */
class Sworm_RegExp extends Sworm_Abstract
{
    protected $codes;

    /**
     * Sworm_Literal constructor.
     */
    public function __construct($reg)
    {
        $this->codes = $reg;
    }

    public function _get(){
        return " REGEXP '".addslashes($this->codes)."'";
    }
}