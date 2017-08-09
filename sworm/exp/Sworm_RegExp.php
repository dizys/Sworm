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
// Sworm RegExp Expression
//-------------------------
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