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
include_once dirname(__FILE__) . "/sworm/Sworm_Abstract.php";
include_once dirname(__FILE__) . "/sworm/Sworm_Table.php";
include_once dirname(__FILE__) . "/sworm/Sworm_Result.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_Literal.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_In.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_NotIn.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_Like.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_NotLike.php";
include_once dirname(__FILE__) . "/sworm/exp/Sworm_RegExp.php";
//------------------------
// Sworm
//-------------------------
class Sworm extends Sworm_Abstract
{
    public function __construct()
    {
        $this->connection = new swoole_mysql();
    }

    /**
     * Connect
     * @param array $server database config
     * @param callable $callback callback function
     */
    public function connect($server, $callback){
        $this->options = $server;
        $this->connection->connect($server, function(swoole_mysql $db, $result) use ($callback) {
            $callback(new Sworm_Result($this, $db, $result, true));
        });
    }

    /**
     *  Disconnect
     */
    public function disconnect(){
        $this->connection->close();
    }

    public function __get($name)
    {
        return new Sworm_Table($this, $name);
    }

    /**
     * Get Table Object
     * @param string $name Table's Name
     * @return Sworm_Table
     */
    public function table($name){
        return new Sworm_Table($this, $name);
    }

    /**
     * Run Query and Get Result
     * @param string $sql sql sentence
     * @param callable $callback callback function
     */
    public function query($sql, $callback){
        $this->connection->query($sql, function(swoole_mysql $link, $result) use ($callback){
            if($result === true){
                $result = $link->affected_rows;
            }
            $callback(new Sworm_Result($this, $link, $result));
        });
    }

    /**
     * Begin a Transaction
     * @param callable $callback callback function
     */
    public function begin($callback){
        $this->connection->begin(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }

    /**
     * Commit the Transaction
     * @param callable $callback callback function
     */
    public function commit($callback){
        $this->connection->commit(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }

    /**
     * Rollback the Transaction
     * @param callable $callback callback function
     */
    public function rollback($callback){
        $this->connection->rollback(function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this, $link, $result));
        });
    }
}