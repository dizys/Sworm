<?php
// +----------------------------------------------------------------------
// | Sworm [Version: 1.0.0]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://dizy.club All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://opensource.org/licenses/MIT )
// +----------------------------------------------------------------------
// | Author: Dizy <derzart@gmail.com>
// +----------------------------------------------------------------------

//------------------------
// Sworm Like Expression
//-------------------------
class Sworm_Like extends Sworm_Abstract
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
        return " LIKE '".addslashes($this->condition)."'";
    }
}