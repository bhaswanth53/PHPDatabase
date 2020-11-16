<?php

    namespace database\Facades;

    class QB
    {
        private static $instance = null;
        private $table;
        private $con;
        private $query;
        private $result;
        private $columns;
        private $sql;
        private $keys;
        private $values;
        private $where = false;
        private $update = false;

        private function __construct()
        {
            $dbhost ="localhost";
            $dbuser = "root";
            $dbpassword = "";
            $dbname = "intern";

            $this->con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
        }

        public static function open()
        {
            if (!self::$instance) {
                self::$instance = new QB();
            }
    
            return self::$instance;
        }

        public function table($table)
        {
            $this->table = $table;
            return $this;
        }

        public function select($columns)
        {
            $this->columns = $columns;
            $sql = "SELECT " . $columns . " FROM " . $this->table;
            $this->sql = $sql;
            return $this;
        }

        public function first()
        {
            $sql = " ORDER BY id ASC LIMIT 1";
            $this->sql .= $sql;
            return $this;
        }

        public function last()
        {
            $sql = " ORDER BY id DESC LIMIT 1";
            $this->sql .= $sql;
            return $this;
        }

        public function where($key, $exp, $value)
        {
            $value = is_int($value) ? $value : "'".$value."'";
            if(!$this->where) {
                $this->where = true;
                $sql = " WHERE `" . $key . "` " . $exp . " " . $value;
            } else {
                $sql = " AND `" . $key . "` " . $exp . " " . $value;
            }
            $this->sql .= $sql;
            return $this;
        }

        public function whereEquals($key, $value)
        {
            $value = is_int($value) ? $value : "'".$value."'";
            if(!$this->where) {
                $this->where = true;
                $sql = " WHERE `" . $key . "` = " . $value;
            } else {
                $sql = " AND `" . $key . "` = " . $value;
            }
            $this->sql .= $sql;
            return $this;
        }

        public function find($id)
        {
            $sql = " WHERE `id` = ".$id;
            $this->sql .= $sql;
            return $this;
        }

        public function orderBy($key, $order)
        {
            $sql = " ORDER BY " . $key . " " . $order;
            $this->sql .= $sql;
            return $this;
        }

        public function latest()
        {
            $sql = " ORDER BY `id` DESC";
            $this->sql .= $sql;
            return $this;
        }

        public function getQuery()
        {
            return $this->sql;
        }

        public function execute()
        {
            $this->query = mysqli_query($this->con, $this->sql);
            return $this;
        }

        public function numrows()
        {
            return mysqli_num_rows($this->query);
        }

        public function result()
        {
            $data = array();
            if($this->numrows() > 1) {
                while($row = mysqli_fetch_assoc($this->query))
                {
                    $data[] = $row;
                }
            } else {
                while($row = mysqli_fetch_assoc($this->query))
                {
                    $data = $row;
                }
            }
            return $data;
        }

        public function insert($data)
        {
            foreach($data as $key => $value) {
                $keys[] = "`".$key."`";
                $values[] = is_int($value) ? $value : "'".$value."'";
            }
            
            $this->keys = implode(",", $keys);
            $this->values = implode(",", $values);

            $sql = 'INSERT INTO '.$this->table.' ('. $this->keys .') VALUES ('. $this->values .')';
            $this->sql = $sql;
            return $this;
        }

        public function update($key, $value)
        {
            $value = is_int($value) ? $value : "'".$value."'";
            if(!$this->sql)
            {
                $this->sql = "UPDATE `". $this->table ."` SET";
            }
            if(!$this->update) {
                $this->sql .= " `".$key."` = ".$value;
                $this->update = true;
            } else {
                $this->sql .= ", `".$key."` = ".$value;
            }
            return $this;
        }

        public function delete()
        {
            $this->sql = "DELETE FROM `".$this->table."`";
            return $this;
        }

        public function rows()
        {
            return mysqli_affected_rows($this->con);
        }

        public function success()
        {
            $success = false;
            if($this->rows() > 0)
            {
                $success = true;
            }
            return $success;
        }

        public function getId()
        {
            return mysqli_insert_id($this->con);
        }

        public function close()
        {
            mysqli_close($this->con);
            return $this;
        }
    }