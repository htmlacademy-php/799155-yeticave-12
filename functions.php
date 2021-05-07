<?php
/**
 * Форматирует цену в рублях, отделяя каждые три знака цены пробелом
 * Добавляет в конец строки символ рубля
 *
 * @param $price цена товара в рублях
 *
 * @return $formatted_price Форматированная строка с ценой
 */

function format_price(float $price) : string {
    $formatted_price = number_format(ceil($price), 0, null, " ") . "₽";
    return $formatted_price;
}

/**
 * Возвращает количество целых часов и остатка минут до даты из будущего в формате «ЧЧ:ММ»
 * 
 * @param $time дата из будущего в формате ГГГГ-ММ-ДД
 * @return [$hours, $mins, $s_hours, $s_mins] количество целых часов и остатка минут и их строковый вариант
 */

function get_time_left(string $time)  {
    if (!is_date_valid($time)) {
        return false;
    }
    $now_time = strtotime('now');
    $fin_time = strtotime($time);
    if ($fin_time <= $now_time) {
        return false;
    }
    $diff = $fin_time - $now_time;
    $hours = floor($diff / 3600);
    $mins = floor($diff % 3600 / 60);
    $s_hours = strval($hours);
    if ($hours < 10) {
        $s_hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
    }
    $s_mins = strval($mins);
    if ($mins < 10) {
        $s_mins = str_pad($mins, 2, "0", STR_PAD_LEFT);
    }
    return [$hours, $mins, $s_hours, $s_mins];
}

class Database {
    private $connection;
    private $error;
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
        $this->error = null;
        $this->connection = mysqli_connect("localhost", "root", "", $name);
        if ($this->connection == false) {
            $this->error = mysqli_connect_error();
        } else {
            mysqli_set_charset($this->connection, "utf8");
        }
    }

    public function is_ok() {
        return $this->error == null;
    }

    public function get_error() {
        return $this->error;
    }

    public function query($sql) {
        $result = mysqli_query($this->connection, $sql);
        if ($result == false) {
            $this->error = mysqli_error($this->connection);
         } else {
            $this->error = null;
        }
        return $result;
    }
}

?>
