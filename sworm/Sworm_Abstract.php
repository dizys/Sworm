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
// Sworm Abstract Class
//-------------------------
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