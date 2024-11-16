<?php

namespace MythicalDash\Cli\Commands;

use MythicalDash\Cli\App;
use MythicalDash\Cli\CommandBuilder;
use MythicalSystems\Utils\EncryptionHandler;
use MythicalSystems\Utils\XChaCha20;

class Setup extends App implements CommandBuilder
{

    /**
     * @inheritDoc
     */
    public static function execute(array $args): void
    {
        $cliApp = App::getInstance();
        if (file_exists(__DIR__ . '/../../../storage/.env')) {
            $cliApp->send('&aThe application is already setup!');
            die();
        }

        self::createDBConnection($cliApp);


        $cliApp->send('&aThe application has been setup!');
    }
    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return "Setup the application!";
    }
    /**
     * @inheritDoc
     */
    public static function getSubCommands(int $index): array
    {
        return [];
    }

    public static function createDBConnection(App $cliApp): void
    {
        $defultEncryption = 'xchacha20';
        $defultDBName = 'mythicaldash';
        $defultDBHost = '127.0.0.1';
        $defultDBPort = '3306';
        $defultDBUser = 'mythical';
        $defultDBPassword = '';



        $cliApp->send("&7Please enter the database encryption &8[&e$defultEncryption&8]&7");
        $dbEncryption = readline('> ') ?: $defultEncryption;
        $allowedEncryptions = ['xchacha20'];
        if (!in_array($dbEncryption, $allowedEncryptions)) {
            $cliApp->send("&cInvalid database encryption.");
            die();
        }


        $cliApp->send("&7Please enter the database name &8[&e$defultDBName&8]&7");
        $defultDBName = readline('> ') ?: $defultDBName;

        $cliApp->send("&7Please enter the database host &8[&e$defultDBHost&8]&7");
        $defultDBHost = readline('> ') ?: $defultDBHost;

        $cliApp->send("&7Please enter the database port &8[&e$defultDBPort&8]&7");
        $defultDBPort = readline('> ') ?: $defultDBPort;

        $cliApp->send("&7Please enter the database user &8[&e$defultDBUser&8]&7");
        $defultDBUser = readline('> ') ?: $defultDBUser;

        $cliApp->send("&7Please enter the database password &8[&e$defultDBPassword&8]&7");
        $defultDBPassword = readline('> ') ?: $defultDBPassword;

        try {
            $dsn = "mysql:host=$defultDBHost;port=$defultDBPort;dbname=$defultDBName";
            $pdo = new \PDO($dsn, $defultDBUser, $defultDBPassword);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $cliApp->send("&aSuccessfully connected to the MySQL database.");
        } catch (\PDOException $e) {
            $cliApp->send("&cFailed to connect to the MySQL database: " . $e->getMessage());
            die();
        }



        $envTemplate = "DATABASE_HOST=" . $defultDBHost . "
DATABASE_PORT=" . $defultDBPort . "
DATABASE_USER=" . $defultDBUser . "
DATABASE_PASSWORD=" . $defultDBPassword . "
DATABASE_DATABASE=" . $defultDBName . "
DATABASE_ENCRYPTION=" . $dbEncryption . "
DATABASE_ENCRYPTION_KEY=" . XChaCha20::generateStrongKey(true) . "";

        $cliApp->send("&aEnvironment file created successfully.");
        $cliApp->send("&aEncryption key generated successfully.");

        $envFile = fopen(__DIR__ . '/../../../storage/.env', 'w');
        fwrite($envFile, $envTemplate);
        fclose($envFile);
    }
}