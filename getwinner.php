<?php
require_once 'vendor/autoload.php';

function sendCongratulation($content, $context)
{
    $transport = new Swift_SmtpTransport($content['smtp'], 465, 'ssl');
    $transport->setPassword($content['password']);
    $transport->setUsername($content['sender']);
    $message = new Swift_Message('Аукцион YetiCave');
    $message->setTo([$content['recipient'], $content['recipient'] => $content['name']]);
    $message->setFrom([$content['sender'] => 'YetiCave']);
    $message->setSubject($content['subject']);
    $message->setMaxLineLength(255);
    $message->setBody($context, $content['type']);
    $mailer = new Swift_Mailer($transport);
    $result = $mailer->send($message);
    return $result;
}

/**
 * Ищем победителей и отправляем сообщение
 */
$winners = $repo->defineWinners();
foreach ($winners as $winner) {
    $user = $repo->getUserById($winner['id']);
    $lot = $repo->getLot($winner['lotId'], true);
    $content['smtp'] = 'smtp.inbox.ru';
    $content['subject'] = 'Ваша ставка победила';
    $content['sender'] = 'yeticave@inbox.ru';
    $content['password'] = 'htmlacademy';
    $content['recipient'] = $user['email'];
    $content['name'] = $user['name'];
    $content['type'] = 'text/html';
    $context = includeTemplate('email.php', [
        'user' => $user,
        'lot' => $lot
    ]);
    sendCongratulation($content, $context);
}
