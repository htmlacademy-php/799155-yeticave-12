<?php
/**
 * Форматирует цену в рублях, отделяя каждые три знака цены пробелом
 * Добавляет в конец строки символ рубля
 *
 * @param float $price цена товара в рублях
 *
 * @return string фрматированная строка с ценой
 */

function formatPrice(float $price) : string
{
    $formatted_price = number_format(ceil($price), 0, null, " ") . " ₽";
    return $formatted_price;
}

/**
 * Возвращает количество целых часов и остатка минут до даты из будущего в формате «ЧЧ:ММ»
 *
 * @param string $time дата из будущего в формате ГГГГ-ММ-ДД
 *
 * @return [int, int, string, string] количество целых часов и остатка минут и их строковый вариант
 */

function getTimeLeft(string $time)
{
    if (!isDateValid($time)) {
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

/**
 * Возвращает строку-сообщение о том, сколько времени прошло с какого-то события
 *
 * @param string $dt_add время наступления события
 * @param string $dt_fin время существования события
 *
 * @return string строка, содержащая сообщение о том, сколько времени прошло в свободном формате
*/
function getTimeStr($dt_add, $dt_fin) : string
{
    $add = strtotime($dt_add);
    $fin = strtotime($dt_fin);
    $now = strtotime("now");
    if ($now > $fin) {
        return "Торги окончены";
    }
    $nowArray = getdate();
    $addArray = getdate($add);

    $months = $nowArray['mon'] - $addArray['mon'];
    $days = $nowArray['mday'] - $addArray['mday'];
    $hours = $nowArray['hours'] - $addArray['hours'];
    $mins = $nowArray['minutes'] - $addArray['minutes'];

    if ($months > 0 || $days > 1) {
        return date("d.m.Y в H:i", $add);
    } elseif ($days === 1) {
        return date("вчера, в H:i", $add);
    } elseif ($hours > 1) {
        return date("в H:i", $add);
    } elseif ($hours === 1) {
        return date("час назад");
    }
    if ($mins === 0) {
        return "меньше минуты назад";
    }
    return $mins . " " .getNounPluralForm($mins, "минута", "минуты", "минут") . " назад";
}


/**
 * Возвращает значение массива $_POST по ключу, если оно существвует, или значение по умолчанию
 *
 * @param string $key значение ключа
 * @param string $default значение величины по умолчанию
 *
 * @return значение поля элемента массива $_POST или значение по умолчанию
*/
function getPostVal($key, $default)
{
    return $_POST[$key] ?? $default;
}

/**
 * Производит валидацию строки, содержащей адрес электронной почты
 *
 * @param string $emailKey значение ключа в массиве, где содержится адрес
 * @param array $data некоторый массив, обычно копия $_POST
 * @param boolean $login false, если проверка не требует авторизации, true, если происходит авторизация
 *
 * @return string либо null, если валидация прошла успешно
*/
function validateEmail($emailKey, $data, $login = false)
{
    if (!isset($data[$emailKey]) or !filter_var($data[$emailKey], FILTER_VALIDATE_EMAIL)) {
            return "Введите корректный email";
    }
    $repo = new Yeticave\Repository();
    $userId = ($repo->getUser($data[$emailKey]))['id'];
    if ($userId and !$login) {
        return "Пользователь с этим email уже существует";
    } elseif (!$userId and $login) {
        return "Пользователь с этим email не найден";
    }
    return null;
}

/**
 * Производит валидацию элемента массива по ключу
 *
 * @param string $key значение ключа
 * @param array $data массив с данными
 * @param string $message строка с сообщение об ошибке
 *
 * @return string, если элемента массива не существует или поле пусто,
 * null, если валидация прошла успешно
*/
function validateFilled($key, $data, $message)
{
    if (!isset($data[$key]) or empty($data[$key])) {
        return $message;
    }
    return null;
}

/**
 * Производит валидацию числового элемента массива по ключу
 *
 * @param string $key значение ключа
 * @param array $data массив с данными
 * @param string $message строка с сообщение об ошибке
 *
 * @return string, если элемента массива не существует или числовое значение поля равно нулю,
 * null, если валидация прошла успешно
*/
function isCorrectId($key, $data, $message)
{
    if (!isset($data[$key]) or intVal($data[$key]) === 0) {
        return $message;
    }
    return null;
}

/**
 * Производит валидацию элемента типа текст массива по ключу
 *
 * @param string $key значение ключа
 * @param array $data массив с данными
 * @param int $min минимальная длина строки
 * @param int $max максимальное количество символов текста
 *
 * @return string, если количество символов текста не соответсвуют ограничениям,
 * null, если валидация прошла успешно
*/
function isCorrectLength($key, $data, $min, $max)
{
    $len = strlen($data[$key]);
    if ($len < $min or $len > $max) {
            return "Значение должно быть от ". $min ." до " . $max ." символов";
    }
    return null;
}

/**
 * Производит валидацию элемента типа дата массива по ключу
 *
 * @param string $dateKey значение ключа
 * @param array $data массив с данными
 * @param string $message строка с сообщение об ошибке
 *
 * @return string, если дата имеет неправильный формат или значение,
 * null, если валидация прошла успешно
*/
function validateDate($dateKey, $data, $message)
{
    if (!isDateValid($data[$dateKey])) {
        return $message;
    }
    $now_time = strtotime('now');
    $fin_time = strtotime($data[$dateKey]);
    if ($fin_time - $now_time < 24 * 3600) {
        return "Дата должна больше текущей хотя бы на один день";
    }
    return null;
}

/**
 * Производит валидацию элемента числового типа массива по ключу
 *
 * @param string $numKey значение ключа
 * @param array $data массив с данными
 *
 * @return string, если поле не содержит числа или число равно нулю,
 * null, если валидация прошла успешно
*/
function isNumeric($numKey, $data)
{
    if (!is_numeric($data[$numKey])) {
        return "Поле должно содержать только числа";
    }
    if (intVal($data[$numKey]) <= 0) {
        return "Содержимое поля должно быть числом больше нуля";
    }
    return null;
}

/**
 * Производит валидацию элемента массива, содержащего пароль, по ключу
 *
 * @param string$passKey значение ключа элемента массива с паролем
 * @param string $emailKey значение ключа элемента массива с адресом эл. почты
 * @param array $data массив с данными
 *
 * @return string, если пароль не отвечает требованиям или введен неверный адрес почты,
 * null, если валидация прошла успешно
*/
function validatePassword($passKey, $emailKey, $data)
{
    $repo = new Yeticave\Repository();
    $userId = ($repo->getUser($data[$emailKey]))['id'];
    if ($userId) {
        $passwordHash = $repo->getUserPwd($userId);
        if (!password_verify($data[$passKey], $passwordHash)) {
            return "Введен неверный пароль";
        } else {
            return null;
        }
    }
    return "Пользователь не найден";
}

/**
 * Производит валидацию элемента массива, содержащего ставку, по ключу
 *
 * @param string $betKey значение ключа элемента массива $_POST, соержащего ставку
 * @param string $lotKey значение ключа элемента массива $_POST, содержащего id лота
 * @param array $data данные из массива $_POST
 *
 * @return string, если величина ставки не отвечает требованиям,
 * null, если валидация прошла успешно
*/
function validateBet($betKey, $lotKey, $data)
{
    $repo = new Yeticave\Repository();
    $lotId = $data[$lotKey];
    $lot = $repo->getLot($lotId);
    $maxBet = $repo->getMaxBet($lotId)['price'];
    $nextBet = $lot['price'];
    if ($maxBet > 0) {
        $nextBet = $maxBet + $lot['bet_step'];
    }
    if ($data[$betKey] < $nextBet) {
        return "Следующая ставка должна быть не меньше " . $nextBet;
    }
    return null;
}
