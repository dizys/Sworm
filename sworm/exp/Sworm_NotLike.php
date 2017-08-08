<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 23:17
 */
class Sworm_NotLike extends Sworm_Abstract
{
    protected $condition;

    /**
     * Sworm_Literal constructor.
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function _get(){
        return " NOT LIKE '".addslashes($this->condition)."'";
    }
}