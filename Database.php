<?php
namespace Yeticave;

/**
 * Database класс-обертка для работы с БД MySql
*/
class Database
{
    private $connection;
    private $error;

    protected function setError(string $error)
    {
        $this->error = $error;
    }
    
    public function __construct()
    {
        $this->error = null;
        $this->connection = mysqli_connect("localhost", "root", "", "yeticave");
        if ($this->connection == false) {
            $this->error = mysqli_connect_error();
        } else {
            mysqli_set_charset($this->connection, "utf8");
        }
    }

    public function __destruct()
    {
        mysqli_close($this->connection);
    }

    public function isOk() : bool
    {
        return $this->error == null;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getBaseError()
    {
        return mysqli_error($this->connection);
    }

    public function query($sql)
    {
        if ($this->connection) {
            $this->error = null;
            $result = mysqli_query($this->connection, $sql);
            if ($result === false) {
                $this->error = mysqli_error($this->connection);
            }
            return $result;
        } else {
            return false;
        }
    }


    public function prepare($sql, $data = [])
    {
        if ($this->connection) {
            $this->error = null;
            $stmt = mysqli_prepare($this->connection, $sql);
            if ($stmt === false) {
                $this->error = mysqli_error($this->connection);
                return false;
            }
            if ($data) {
                $types = '';
                $stmt_data = [];

                foreach ($data as $value) {
                    $type = 's';
                    if (is_int($value)) {
                        $type = 'i';
                    } elseif (is_string($value)) {
                        $type = 's';
                    } elseif (is_double($value)) {
                        $type = 'd';
                    }
                    if ($type) {
                        $types .= $type;
                        $stmt_data[] = $value;
                    }
                }
                $values = array_merge([$stmt, $types], $stmt_data);

                $func = 'mysqli_stmt_bind_param';
                $func(...$values);
                if (mysqli_errno($this->connection) > 0) {
                    $this->error = mysqli_error($this->connection);
                    return false;
                }
            }
            return $stmt;
        } else {
            return false;
        }
    }

    public function getEscapeStr(string $str) : string
    {
        if (get_magic_quotes_gpc()) {
            $str  =  stripslashes($str);
        }
        if ($this->connection) {
            return mysqli_real_escape_string($this->connection, $str);
        }
        return $str;
    }

    public function getLastId()
    {
        if ($this->isOk()) {
            return mysqli_insert_id($this->connection);
        }
        return false;
    }

    public function beginTransaction()
    {
        mysqli_begin_transaction($this->connection);
    }

    public function commitTransaction()
    {
        mysqli_commit($this->connection);
    }

    public function rollbackTransaction()
    {
        mysqli_rollback($this->connection);
    }
}
