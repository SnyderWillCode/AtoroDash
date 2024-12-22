<?php
use MythicalClient\App;
use MythicalClient\Chat\Mails;
use MythicalClient\Chat\User;
use MythicalClient\Chat\Roles;
use MythicalClient\Chat\Billing;
use MythicalClient\Chat\Session;
use MythicalClient\Chat\UserActivities;
use MythicalClient\Chat\columns\UserColumns;


$router->get('/api/user/session/emails', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    $appInstance->OK('User emails', [
        'emails' => Mails::getAll(User::getInfo($accountToken, UserColumns::UUID, false))
    ]);
});


$router->get('/api/user/session/emails/(.*)/raw', function (string $id): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();
    if ($id == '') {
        die(header('location: /account'));
    }

    if (!is_numeric($id)) {
        die(header('location: /account'));
    }
    $id = (int) $id;

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    if (Mails::exists($id)) {
        if (Mails::doesUserOwnEmail(User::getInfo($accountToken, UserColumns::UUID, false), $id)) {
            $mail = Mails::get($id);
            header('Content-Type: text/html; charset=utf-8');
            echo $mail['body'];
            exit;
        } else {
            die(header('location: /account'));
        }
    } else {
        die(header('location: /account'));
    }
});

$router->delete('/api/user/session/emails/(.*)/delete', function (string $id): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();
    if ($id == '') {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }

    if (!is_numeric($id)) {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }
    $id = (int) $id;

    $appInstance->allowOnlyDELETE();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    if (Mails::exists($id)) {
        if (Mails::doesUserOwnEmail(User::getInfo($accountToken, UserColumns::UUID, false), $id)) {
            Mails::delete($id, User::getInfo($accountToken, UserColumns::UUID, false));
            $appInstance->OK('Email deleted successfully!', []);
        } else {
            $appInstance->Unauthorized('Unauthorized', ['error_code' => 'UNAUTHORIZED']);
        }
    } else {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }
});
