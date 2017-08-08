<?php

/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/8/8
 * Time: 16:16
 */
class Sworm_Table extends Sworm_Abstract
{

    protected $mSworm, $table;
    protected $select, $where, $order, $limit, $group;
    /**
     * Sworm_Table constructor.
     */
    public function __construct($sworm ,$table)
    {
        Sworm_Abstract::__construct();
        $this->mSworm = $sworm;
        $this->connection = $sworm->connection;
        $this->options = $sworm->options;
        $this->table = $table;
        $this->select = array();
        $this->where = array();
        $this->order = array();
        $this->limit = array();
        $this->group = array();
    }

    public function where($clause, ...$side){
        $where = clone $this;
        if(is_array($clause)){
            $keys = array_keys($clause);
            foreach ($keys as $key){
                array_push($where->where, array(
                    'type' => 'AND',
                    'clause' => $key,
                    'side' => $clause[$key]
                ));
            }
        }else{
            array_push($where->where, array(
                'type' => 'AND',
                'clause' => $clause,
                'side' => $side
            ));
        }
        return $where;
    }

    public function whereOr($clause, ...$side){
        $where = clone $this;
        if(is_array($clause)){
            $keys = array_keys($clause);
            foreach ($keys as $key){
                array_push($where->where, array(
                    'type' => 'OR',
                    'clause' => $key,
                    'side' => $clause[$key]
                ));
            }
        }else{
            array_push($where->where, array(
                'type' => 'OR',
                'clause' => $clause,
                'side' => $side
            ));
        }
        return $where;
    }

    public function select($fields){
        $select = clone $this;
        if(!is_array($fields))
            $fields = explode(",", $fields);
        $select->select = array_merge($select->select, $fields);
        return $select;
    }

    public function order($fields){
        $order = clone $this;
        if(!is_array($fields))
            $fields = explode(",", $fields);
        $order->order = array_merge($order->order, $fields);
        return $order;
    }

    public function limit($num, $offset=0){
        $limit = clone $this;
        $limit->limit = array(
            "offset" => $offset,
            "num" => $num
        );
        return $limit;
    }

    public function group($by, $having = null){
        $group = clone $this;
        $group->group = array(
            "by" => $by,
            "having" => $having
        );
        return $group;
    }

    private function genSelect(){
        if(sizeof($this->select)==0){
            return "SELECT * ";
        }
        $selects = "";
        foreach ($this->select as $item){
            $selects.=trim($item).", ";
        }
        $selects = substr($selects, 0, -2);
        return "SELECT ".$selects." ";
    }

    public function getTable(){
        $prefix = "";
        if(isset($this->options['prefix'])){
            $prefix = $this->options['prefix'];
        }
        return $prefix.$this->table;
    }

    private function genTable(){

        return "FROM ".$this->getTable()." ";
    }

    private function genWhere(){
        if(sizeof($this->where)==0){
            return "";
        }
        $wheres = "";
        foreach ($this->where as $item){
            $addon = $item['type']." ";
            if($wheres==""){
                $addon = "";
            }
            if(is_array($item['side'])){
                $wheres.=$addon.$item['clause']." ";
                foreach ($item['side'] as $sideitem){
                    $wheres = str_replace_once("?", "'".addslashes($sideitem)."'", $wheres);
                }
            }else{
                if(gettype($item['side'])!="object"){
                    $wheres.=$addon.$item['clause']." = '".addslashes($item['side'])."' ";
                }else{
                    $wheres.=$addon.$item['clause'].($item['side']->_get())." ";
                }
            }
        }
        return "WHERE ".$wheres;
    }

    private function genGroup(){
        if(sizeof($this->group)==0){
            return "";
        }
        $addon = "";
        if(!is_null($this->group['having'])){
            $addon = " HAVING ".$this->group['having'];
        }
        return "GROUP BY ".$this->group['by'].$addon." ";
    }

    private function genOrder(){
        if(sizeof($this->order)==0){
            return "";
        }
        $orders = "";
        foreach ($this->order as $item){
            $orders.=trim($item).", ";
        }
        $orders = substr($orders, 0, -2);
        return "ORDER BY ".$orders." ";
    }

    private function genLimit(){
        if(sizeof($this->limit)==0){
            return "";
        }
        if($this->limit['offset']==0){
            return "LIMIT ".$this->limit['num']." ";
        }else{
            return "LIMIT ".$this->limit['offset'].",".$this->limit['num']." ";
        }
    }

    public function fetch($callback){
        $query = $this->genSelect().$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function count($colum_name, $callback = ""){
        if($callback == ""){
            $callback = $colum_name;
            $colum_name = "*";
        }
        $query = "SELECT COUNT($colum_name) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function sum($colum_name, $callback){
        $query = "SELECT SUM($colum_name) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result===false){
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }else{
                $val = null;
                foreach ($result[0] as $num){
                    $val = $num;
                }
                if(is_null($val)){
                    $result = 0;
                }else{
                    $result = intval($val);
                }
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }
        });
    }

    public function max($colum_name, $callback){
        $query = "SELECT MAX($colum_name) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result==false){
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }else{
                $val = null;
                foreach ($result[0] as $num){
                    $val = $num;
                }
                if(is_null($val)){
                    $result = false;
                }else{
                    $result = intval($val);
                }
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }
        });
    }

    public function min($colum_name, $callback){
        $query = "SELECT MIN($colum_name) ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result==false){
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }else{
                $val = null;
                foreach ($result[0] as $num){
                    $val = $num;
                }
                if(is_null($val)){
                    $result = false;
                }
                $callback(new Sworm_Result($this->mSworm, $link, $result));
            }
        });
    }

    public function getBy($column_name, $value, $callback){
        $query = "SELECT * ".$this->genTable()."WHERE $column_name = '".addslashes($value)."'";
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function get($column_name, $callback){
        $query = "SELECT $column_name ".$this->genTable();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function update($data, $callback){
        $sets = "";
        $keys = array_keys($data);
        foreach ($keys as $key){
            if(gettype($data[$key]) != "object"){
                $sets.= $key." = '".addslashes($data[$key])."', ";
            }else{
                $sets.= $key.($data[$key]->_get()).", ";
            }
        }
        if($sets != "")
            $sets = substr($sets, 0, -2);
        $query = "UPDATE ".$this->getTable()." SET ".$sets." ".$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result != false){
                $result = $link->affected_rows;
            }
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function insert($data, $callback){
        $fields = ""; $values = "";
        $keys = array_keys($data);
        foreach ($keys as $key){
                $fields.= $key.", ";
                $values.= "'".addslashes($data[$key])."', ";
        }
        if($fields != "")
            $fields = substr($fields, 0, -2);
        if($values != "")
            $values = substr($values, 0, -2);
        $query = "INSERT INTO ".$this->getTable()." ($fields) VALUES ($values)";
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result != false){
                $result = $link->affected_rows;
            }
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    public function delete($callback){
        $query = "DELETE ".$this->genTable().$this->genWhere().$this->genGroup().$this->genOrder().$this->genLimit();
        if(isset($this->options['debug']) && $this->options['debug']==true){
            echo $query."\n";
        }
        $this->connection->query($query, function(swoole_mysql $link, $result) use ($callback){
            if($result != false){
                $result = $link->affected_rows;
            }
            $callback(new Sworm_Result($this->mSworm, $link, $result));
        });
    }

    function __call($name, $arguments)
    {
        if(strpos($name, "getBy")==0 && sizeof($arguments)==2){
            $column = substr($name, 5);
            $column[0] = strtolower($column[0]);
            for($i = 0; $i<strlen($column); ++$i){
                $cbit = strtolower($column[$i]);
                if($cbit!=$column[$i]){
                    $column[$i] = $cbit;
                    $column = substr_replace($column,"_",$i,0);
                    $i++;
                }
            }
            $this->getBy($column, $arguments[0], $arguments[1]);
        }
    }

}

function str_replace_once($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}