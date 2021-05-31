<?php
/**
 * Форматирует цену в рублях, отделяя каждые три знака цены пробелом
 * Добавляет в конец строки символ рубля
 *
 * @param $price цена товара в рублях
 *
 * @return $formatted_price Форматированная строка с ценой
 */

function formatPrice(float $price) : string {
    $formatted_price = number_format(ceil($price), 0, null, " ") . "₽";
    return $formatted_price;
}

/**
 * Возвращает количество целых часов и остатка минут до даты из будущего в формате «ЧЧ:ММ»
 * 
 * @param $time дата из будущего в формате ГГГГ-ММ-ДД
 * @return [$hours, $mins, $s_hours, $s_mins] количество целых часов и остатка минут и их строковый вариант
 */

function getTimeLeft(string $time)  {
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

function getPostVal($key, $default) {
	return $_POST[$key] ?? $default;
}

function getFileName($key, $default) {
    return $_FILES[$key]['name'];
}

function validateEmail($emailKey) {
	if (!isset($_POST[$emailKey]) or !filter_var($_POST[$emailKey], FILTER_VALIDATE_EMAIL)) {
			return "Введите корректный email";
	}
	return null;
}

function validateFilled($key, $array, $message) {
	if (!isset($array[$key]) or empty($array[$key])) {
		return $message;
	}
	return null;
}

function isCorrectLength($key, $array, $min, $max) {
	$len = strlen($array[$key]);
	if ($len < $min or $len > $max) {
			return "Значение должно быть от ". $min ." до " . $max ." символов";
	}
	return null;
}

function validateDate($dateKey, $array, $message)
{
    if (!is_date_valid($array[$dateKey])) {
        return $message;    
    }
    $now_time = strtotime('now');
    $fin_time = strtotime($array[$dateKey]);
    if ($fin_time - $now_time < 24 * 3600) {
        return "Дата должна больше текущей хотя бы на один день";
    }
    return null;
}

function isNumeric($numKey, $array) {
    if (!is_numeric($array[$numKey]) or intval($array[$numKey]) === 0) {
        return "Поле должно содержать число";
    }
    return null;
}
