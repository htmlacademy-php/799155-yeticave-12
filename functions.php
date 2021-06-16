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

function validateEmail($emailKey, $data, $login = false) {
	if (!isset($data[$emailKey]) or !filter_var($data[$emailKey], FILTER_VALIDATE_EMAIL)) {
			return "Введите корректный email";
	}
    $repo = new Repository();
    $userId = ($repo->getUser($data[$emailKey]))['id'];
    if ($userId and !$login) {
        return "Пользователь с этим email уже существует";
    } elseif (!$userId and $login) {
        return "Пользователь с этим email не найден";
    }
	return null;
}

function validateFilled($key, $data, $message) {
	if (!isset($data[$key]) or empty($data[$key])) {
		return $message;
	}
	return null;
}

function isCorrectId($key, $data, $message) {
	if (!isset($data[$key]) or intVal($data[$key]) == 0) {
		return $message;
	}
	return null;
}

function isCorrectLength($key, $data, $min, $max) {
	$len = strlen($data[$key]);
	if ($len < $min or $len > $max) {
			return "Значение должно быть от ". $min ." до " . $max ." символов";
	}
	return null;
}

function validateDate($dateKey, $data, $message)
{
    if (!is_date_valid($data[$dateKey])) {
        return $message;    
    }
    $now_time = strtotime('now');
    $fin_time = strtotime($data[$dateKey]);
    if ($fin_time - $now_time < 24 * 3600) {
        return "Дата должна больше текущей хотя бы на один день";
    }
    return null;
}

function isNumeric($numKey, $data) {
    if (!is_numeric($data[$numKey]) or intval($data[$numKey]) === 0) {
        return "Поле должно содержать только числа";
    }
    return null;
}

function validateLogin($nameKey, $data) {
	$repo = new Repository();
	if ($repo->findUserName($data[$nameKey])) {
		return "Пользователь с таким именем уже есть. Укажите другое имя";
	}
	return null;
}

function validatePassword($passKey, $emailKey, $data) {
	$repo = new Repository();
	$userId = ($repo->getUser($data[$emailKey]))['id'];
	if ($userId) {
		$passwordHash = $repo->getUserPwd($userId);
		if (!password_verify($data[$passKey], $passwordHash)) {
			return "Введен неверный пароль";
		}
	} else {
		return "Пользователь не найден";
	}
	return null;
}

function validateBet($betKey, $lotKey, $data) {
    $repo = new Repository();
    $lotId = $data[$lotKey];
    $maxBet = $repo->getMaxBet($lotId);
    $lot = $repo->getLot($lotId);
    if ($maxBet < $lot['price']) {
        $maxBet = $lot['price'];
    }
    $nextBet = $maxBet + $lot['bet_step'];
    if ($data[$betKey] < $nextBet) {
        return "Следующая ставка должна быть не меньше " . $nextBet;
    } elseif (($data[$betKey] - $maxBet) % $lot['bet_step'] > 0) {
        return "Шаг ставки должен быть равен " . $lot['bet_step'];
    }
    return null;
}
