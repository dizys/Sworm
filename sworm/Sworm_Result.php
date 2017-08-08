<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 22:28
 */
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

    public function getStatus(){
        if($this->mResult === false){
            return false;
        }else{
            return true;
        }
    }

    public function getResult(){
        if($this->mResult === false){
            return false;
        }else{
            return $this->mResult;
        }
    }

    public function getErrorCode(){
        if($this->mResult === false){
            return $this->mConnection?$this->mLink->connect_errno:$this->mLink->errno;
        }else{
            return 0;
        }
    }

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