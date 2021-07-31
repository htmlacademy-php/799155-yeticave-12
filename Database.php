<?php
namespace Yeticave;

/**
 * Database класс-обертка для работы с БД MySql
*/
class Database
{
    private $connection;
    private $error;

    /**
     * Зафиксировать описание ошибки
     * @param string описание ошибки
     * @return ничего
     */
    protected function setError(string $error)
    {
        $this->error = $error;
    }
    
    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->error = null;
        $this->connection = mysqli_connect("localhost", "root", "", "yeticave");
        if ($this->connection === false) {
            $this->error = mysqli_connect_error();
        } else {
            mysqli_set_charset($this->connection, "utf8");
        }
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        mysqli_close($this->connection);
    }

    /**
     * Проверка наличия ошибки
     * @param ничего
     * @return bool true, если ошибок нет
     */
    public function isOk() : bool
    {
        return $this->error === null;
    }

    /**
     * Получить описание ошибки
     * @param ничего
     * @return string описание ошибки
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Получить описание ошибки БД
     * @param ничего
     * @return string текст с описанием ошибки
     */
    public function getBaseError()
    {
        return mysqli_error($this->connection);
    }

    /**
     * Выполнить SQL-запрос к БД
     * @param string $sql SQL-запрос
     * @return mysqli_result или true в случае успеха выполнения запроса к БД, иначе - false
     */
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

    /**
     * Подготавливает SQL-выражение к выполнению
     * @param string $sql SQL-выражение подготавливаемого запроса
     * @param array  $data массив переменных для привязки к параметрам подготавливаемого запроса
     * @return string в случае успеха - подготовленное SQL-выражние, false - в противном случае
     */
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

    /**
     * Получить строку с экранированием специальных символов
     * @param string $str - исходная строка
     * @return string строка с экранированием. Если была ошибка соединения с БД, исходная строка
     */
    public function getEscapeStr(string $str) : string
    {
        if ($this->connection) {
            return mysqli_real_escape_string($this->connection, $str);
        }
        return $str;
    }

    /**
     * Получить автоматически генерируемый ID, используя последний запрос
     * @param ничего
     * @return int ID последнего SQL-запроса
     */
    public function getLastId()
    {
        if ($this->isOk()) {
            return mysqli_insert_id($this->connection);
        }
        return false;
    }
}
