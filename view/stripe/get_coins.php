<?php
use MythicalDash\ErrorHandler;
use MythicalDash\SettingsManager;


include(__DIR__ . '/../requirements/page.php');
if (SettingsManager::getSetting("enable_stripe") == "false") {
    header('location: /');
}
try {
    if (isset($_GET['code']) && !$_GET['code'] == "") {
        $user_query = "SELECT * FROM mythicaldash_payments WHERE code = ?";
        $stmt = mysqli_prepare($conn, $user_query);
        mysqli_stmt_bind_param($stmt, "s", $_GET['code']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paymentsdb = $conn->query("SELECT * FROM mythicaldash_payments WHERE code = '" . mysqli_real_escape_string($conn, $_GET['code']) . "'")->fetch_array();
            if ($paymentsdb['status'] == "pending") {
                if ($paymentsdb['ownerkey'] == $_COOKIE['token']) {
                    $code = mysqli_real_escape_string($conn, $_GET['code']);
                    $conn->query("UPDATE `mythicaldash_payments` SET `status` = 'paid' WHERE `code` = '$code'");
                    $coins = $session->getUserInfo('coins') + $paymentsdb['coins'];
                    $conn->query("UPDATE `mythicaldash_users` SET `coins` = '" . $coins . "' WHERE `mythicaldash_users`.`api_key` = '" . mysqli_real_escape_string($conn, $_COOKIE['token']) . "'");
                    header('location: /dashboard?s=Thanks for buying from ' . SettingsManager::getSetting("name"));
                    $conn->close();
                    die();
                } else {
                    header('location: /dashboard?e=Hmm? Are you sure you paid for this? XLO');
                    $conn->close();
                    die();
                }
            } else {
                header('location: /dashboard?e=Hmm? Are you sure you paid for this? XLO');
                $conn->close();
                die();
            }
        } else {
            header('location: /dashboard?e=Hmm? Are you sure you paid for this?');
            $conn->close();
            die();
        }
    } else {
        header('location: /dashboard?e=Hmm? Are you sure you paid for this?');
        die();
    }
} catch (Exception $e) {
    header("location: /dashboard?e=Some unexpected errors occurred!");
    ErrorHandler::Error("Coins ",$e);
    die();
}
?>