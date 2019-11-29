<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SQLite3Ex
 *
 * @author BARIS
 */

class SQLite3Paging {
    private $stmtQ = null;
    private $stmtC = null;
    private $sql;
    private $start = 0;
    private $limit = 10;
    private $conn = null;
    private $fields = [];
    public $values = [];

    public function __construct( SQLite3 $conn ,$sql,$start = 0,$limit = 10,$fields = [])
    {
        $this->sql = $sql;
        $this->start = $start;
        $this->limit = $limit;
        $this->conn = $conn;
        $this->fields = $fields;
        $this->refresh();
    }

    public function addField($name,$groupFnc) {
        $this->fields[$name] = $groupFnc;
    }

    private function generateGroupSql() {
        $sqlG = "SELECT COUNT(1) AS MAXROW";
        foreach($this->fields as $field => $fnc) {
            $sqlG.=",$fnc AS $field";
        }
        $sqlG.=" FROM (".$this->sql.") QPAGING";
        return $sqlG;
    }

    private function refresh() {
        if ( is_null($this->stmtC) || is_null($this->stmtQ) ) {            
            $this->stmtC = $this->conn->prepare($this->generateGroupSql());
            $this->stmtQ = $this->conn->prepare("SELECT * FROM ($this->sql) QPAGING LIMIT $this->start,$this->limit");                        
        }
    }

    public function bindParam($sql_param,&$param,int $type) {     
        $this->stmtQ->bindParam($sql_param,$param,$type);
        $this->stmtC->bindParam($sql_param,$param,$type);
    }

    public function bindValue($sql_param,&$param,int $type) {        
        $this->stmtQ->bindValue($sql_param,$param,$type);
        $this->stmtC->bindValue($sql_param,$param,$type);
    }

    public function result(&$numrow,$close = true,$arrayType = SQLITE3_ASSOC) {
        
        $rc = $this->stmtC->execute();
        $ac = $rc->fetchArray(SQLITE3_ASSOC);

        $rq = $this->stmtQ->execute();
        $aq = [];
        while ($r = $rq->fetchArray($arrayType)) {
            array_push($aq, $r);
        }

        if ( $close ) {
            $this->stmtC->close();
            $this->stmtQ->close();       
        } else {
            $this->stmtC->reset();
            $this->stmtQ->reset();
        }
        $this->values = $ac;
        $numrow = $ac["MAXROW"];
        return $aq;

    }

}

class SQLite3Ex extends SQLite3 {

    public function __construct(string $filename, int $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, string $encryption_key = null) {
        parent::__construct($filename, $flags, $encryption_key);

        $this->enableExceptions(true);
        $this->createFunction("locale", "SQLite3Ex::Locale");
        $this->createFunction("rownum", "SQLite3Ex::Rownum");
    }

    public function queryAsArray(string $sql, $arrayType = SQLITE3_ASSOC) {
        $data = array();
        $result = $this->query($sql);
        while ($r = $result->fetchArray($arrayType)) {
            array_push($data, $r);
        }
        return $data;
    }



    public static function resultToArray(SQLite3Result $result, $arrayType = SQLITE3_ASSOC) {
        $data = array();
        while ($r = $result->fetchArray($arrayType)) {
            array_push($data, $r);
        }
        return $data;
    }

    public function table($table, $row) {

        function findPk($info) {
            foreach ($info as $row) {
                if ($row["pk"] == 1) {
                    return $row;
                }
            }
            return false;
        }

        function getField($info, $name) {
            for ($i = 0; $i < count($info); $i++) {
                $r = $info[$i];
                if ($r["name"] == $name) {
                    return $r;
                }
            }
            return false;
        }

        function getParamType($type) {
            $t = trim(explode("(", $type)[0]);
            if ($t == "TEXT") {
                return SQLITE3_TEXT;
            } elseif ($t == "INTEGER") {
                return SQLITE3_INTEGER;
            } elseif ($t == "FLOAT") {
                return SQLITE3_FLOAT;
            } elseif ($t == "NUMERIC") {
                return SQLITE3_TEXT;
            } elseif ($t == "BLOB") {
                return SQLITE3_BLOB;
            }
            return SQLITE3_TEXT;
        }

        $info = self::resultToArray($this->query("PRAGMA TABLE_INFO($table)"));
        $names = "";
        $values = "";
        $sets = "";
        $where = "";
        $params = [];
        $sql = "";
        $stmt = null;
        $action = "";

        if (is_array($row)) {
            foreach ($row as $k => $v) {
                $field = getField($info, $k);
                if ($field != false) {
                    $param = [
                        "name" => $k,
                        "type" => getParamType($field["type"]),
                        "value" => ( (is_string($v) && $v == "") ? null : $v)
                    ];
                    //var_dump($param);
                    array_push($params, $param);
                    if ($field["pk"] == 1) {
                        $where = "$k = :$k";
                    } else {
                        $sets .= (strlen($sets) > 0 ? "," : "") . "$k = :$k";
                        $names .= (strlen($names) > 0 ? "," : "") . "$k";
                        $values .= (strlen($values) > 0 ? "," : "") . ":$k";
                    }
                } else {
                    
                }
            }
            if ($where == "") { //insert
                $sql = "INSERT INTO $table ( $names ) VALUES ( $values )";
                $stmt = $this->prepare($sql);

                $action = "INSERT";
            } else { //update
                $sql = "UPDATE $table SET $sets WHERE $where";
                //echo $sql;
                $stmt = $this->prepare($sql);
                $action = "UPDATE";
            }
        } else { //delete
            $pk = findPk($info);
            if ($pk != false) {
                $pkname = $pk["name"];
                $sql = "DELETE FROM $table WHERE $pkname = :$pkname";
                $stmt = $this->prepare($sql);
                $action = "DELETE";
                array_push($params, ["name" => $pkname, "type" => getParamType($pk["type"]), "value" => $row]);
            } else {
                throw new Exception("Tabel dose not have PK");
            }
        }

        for ($i = 0; $i < count($params); $i++) {
            //echo $params[$i]["name"]." / ".$params[$i]["value"]." / ".$params[$i]["type"]."\n";
            $stmt->bindValue($params[$i]["name"], $params[$i]["value"], $params[$i]["type"]);
        }

        $stmt->execute();
        return [
            "action" => $action,
            "id" => ($action == "INSERT") ? $this->lastInsertRowID() : 0
        ];
    }

    static private $_collators = array();
    
    static private $_rownum = 0;

    static public function Rownum($reset = false) {
        if ( $reset ) {
            self::$_rownum = 0;
        } else {
            self::$_rownum++;
        }        
        return self::$_rownum;
    }

    static public function Locale($data, $locale = 'root') {
        if (class_exists("Collator")) {
            if (isset(self::$_collators[$locale]) !== true) {
                self::$_collators[$locale] = new \Collator($locale);
            }
            return self::$_collators[$locale]->getSortKey($data);
        } else {
            return $data;
        }
    }

}
