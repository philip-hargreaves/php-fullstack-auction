<?php
//this will be the cron controller.
//call notification service.
//notification service calls on repo to extract.
//send notification.

use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

session_start();

header('Content-Type: application/json');

$notificationServ = DIContainer::get('notificationServ');

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //TODO need error handling here?
    //for when no user is logged in.
    if (!$userId) {
        echo json_encode([]);
        exit;
    }
    ///////////////////////////////

    $notificationsToSend = $notificationServ -> prepareNotifications($userId);

    //test
    echo json_encode($notificationsToSend);
    //////////////////////////

}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Mark a notification as sent
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']))
    {
        $notificationId = (int)$data['id'];
        $notificationServ -> markAsSent($notificationId);
        echo json_encode(['success' => true]);
    }
}
exit;
