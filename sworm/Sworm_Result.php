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
// Sworm Result
//-------------------------
class Sworm_Result extends Sworm_Abstract
{

    private $mSworm, $mLink, $mResult, $mConnection;
    /**
     * Sworm_Result constructor.
     */
    public function __construct($sworm, $link, $result, $connection = false)
    {
        $this->mSworm = $sworm;
        $this->mLink = $link;
        $this->mResult = $result;
        $this->mConnection = $connection;
    }

    /**
     * Get Status
     * @return bool
     */
    public function getStatus(){
        if($this->mResult === false){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get Result
     * @return mixed
     */
    public function getResult(){
        if($this->mResult === false){
            return false;
        }else{
            return $this->mResult;
        }
    }

    /**
     * Get ErrorCode
     * @return int
     */
    public function getErrorCode(){
        if($this->mResult === false){
            return $this->mConnection?$this->mLink->connect_errno:$this->mLink->errno;
        }else{
            return 0;
        }
    }

    /**
     * Get ErrorMsg
     * @return int|null
     */
    public function getErrorMsg(){
        if($this->mResult === false){
            return $this->mConnection?$this->mLink->connect_error:(isset($this->mLink->error)?$this->mLink->error:null);
        }else{
            return 0;
        }
    }

    public function __get($name)
    {
        switch ($name){
            case "status":
                return $this->getStatus();
            case "result":
                return $this->getResult();
            case "errorCode":
                return $this->getErrorCode();
            case "errorMsg":
                return $this->getErrorMsg();
            case "sworm":
                return $this->mSworm;
        }
    }
}