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
// Sworm Literal Expression
//-------------------------
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