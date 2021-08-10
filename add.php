<?php
require_once 'helpers.php';
require_once 'Form.php';
require_once 'functions.php';
require_once 'session.php';

$errors = array();
$layoutContent = null;

//данные полей формы
$lot = [
    'lot-name' => $repo->getEscapeStr(getPostVal('lot-name', '')),
    'lot-date' => $repo->getEscapeStr(getPostVal('lot-date', '')),
    'lot-step' => $repo->getEscapeStr(getPostVal('lot-step', 0)),
    'lot-rate' => $repo->getEscapeStr(getPostVal('lot-rate', 0)),
    'message' => $repo->getEscapeStr(getPostVal('message', '')),
    'category' => $repo->getEscapeStr(getPostVal('category', '0')),
    'lot-img' => $repo->getEscapeStr(getPostVal('lot_img', '')),
    'new-img' => $repo->getEscapeStr(getPostVal('new_img', ''))
];


//правила верификации для полей формы
$lotRules = [
    'lot-name' => function ($lot) {
        $error = validateFilled('lot-name', $lot, 'Введите наименование лота');
        if ($error === null) {
            $error = isCorrectLength('lot-name', $lot, 1, 128);
        }
        return $error;
    },
    'lot-date' => function ($lot) {
        $error = validateFilled(
            'lot-date',
            $lot,
            'Введите дату в формате ГГГГ-ММ-ДД'
        );
        if ($error === null) {
            $error = validateDate('lot-date', $lot, 'Формат даты ГГГГ-ММ-ДД');
        }
        return $error;
    },
    'lot-step' => function ($lot) {
        $error = validateFilled('lot-step', $lot, 'Введите шаг ставки');
        if ($error === null) {
            $error = isNumeric('lot-step', $lot);
        }
        return $error;
    },
    'lot-rate' => function ($lot) {
        $error = validateFilled('lot-rate', $lot, 'Введите начальную цену');
        if ($error === null) {
            $error = isNumeric('lot-rate', $lot);
        }
        return $error;
    },
    'category' => function ($lot) {
        $error = isCorrectId('category', $lot, 'Выберите категорию');
        return $error;
    },
    'message' => function ($lot) {
        $error = validateFilled(
            'message',
            $lot,
            'Напишите описание лота'
        );
        return $error;
    }
];

if ($isAuth === 0) {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $lot[$key] = $repo->getEscapeStr($value);
    }

    //валидация полей формы
    Yeticave\Form::validateFields($lotRules, $lot);
    $errors = Yeticave\Form::getErrors();
    //отдельно валидация файла с изображением и перенос его в папку uploads
    if (empty($lot['lot-img'])) {
        if (Yeticave\Form::validateImageFile('lot-img', 'Укажите файл с изображением')) {
            $lot['lot-img'] = Yeticave\Form::getFileName();
            $lot['new-img'] = Yeticave\Form::getNewFileName();
        } else {
            $errors['lot-img'] = Yeticave\Form::getMessage();
        }
    }
    if ($repo->isOk() and  count($errors) === 0) {
        //запишем данные лота в базу
        if ($repo->addNewLot($lot, $authorId) === true) {
            //если все ок, переместимся на станицу лота
            if ($repo->isOk()) {
                $id = $repo->getLastId();
                header("Location:/lot.php?id=" . $id);
                exit();
            }
        } else {
            $errors['lot-name'] = 'Неизвестная ошибка. Данные лота не добавлены в базу';
        }
    }
}

//работаем с ошибками формы
if ($repo->isOk()) {
    $cats = $repo->getAllCategories();
    if ($repo->iSOk()) {
        $navContent = includeTemplate('nav.php', [
            'cats' => $cats
        ]);
        $addContent = includeTemplate('add.php', [
            'nav' => $navContent,
            'lot' => $lot,
            'cats' => $cats,
            'errors' => $errors
        ]);
        $layoutContent = includeTemplate('layout.php', [
            'nav' => $navContent,
            'is_auth' => $isAuth,
            'search' => '',
            'content' => $addContent,
            'cats' => $cats,
            'title' => $title,
            'user_name' => $userName,
            'user_id' => $authorId,
            'url' => $url,
            'pagesCount' => $pagesCount,
            'curPage' => $curPage,
            'pages' => $pages
        ]);
    }
}
//какая-то ошибка при обработке запроса
if (!$repo->isOk()) {
    $layoutContent = includeTemplate('error.php', [
        'error' => $repo->getError()
    ]);
}

print($layoutContent);
