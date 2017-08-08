<?php
include_once dirname(__FILE__) . "/sworm/Sworm_Abstract.php";
include_once dirname(__FILE__) . "/sworm/Sworm_Table.php";
include_once dirname(__FILE__) . "/sworm/Sworm_Result.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_Literal.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_In.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_NotIn.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_Like.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_NotLike.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_RegExp.php";
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 15:32
 */
class Sworm extends Sworm_Abstract
{
    public function __construct()
    {
        $this->connection = new swoole_mysql();
    }

    public function connect($server, $callback){
        $this->options = $server;
        $this->connection->connect($server, function(swoole_mysql $db, $result) use ($callback) {
            $callback(new Sworm_Result($this, $db, $result, true));
        });
    }

    public function disconnect(){
        $this->connection->close();
    }

    public function __get($name)
    {
        return new Sworm_Table($this, $name);
    }

    public function table($name){
        return new Sworm_Table($this, $name);
    }

    public function query($sql, $callback){
        $this->connection->query($sql, function(swoole_mysql $link, $result) use ($callback){
            if($result === true){
                $result = $link->affected_rows;
            }
            $callback(new Sworm_Result($this, $link, $result));
        });
    }

    public function begin($callback){
        $this->connection->begin(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }

    public function commit($callback){
        $this->connection->commit(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }
    public function rollback($callback){
        $this->connection->rollback(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }
}