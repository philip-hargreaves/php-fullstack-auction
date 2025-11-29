<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

session_start();

header('Content-Type: application/json');

$notificationServ = DIContainer::get('notificationServ');

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //when no user is logged in, no notifications are sent.
    if (!$userId) {
        echo json_encode([]);
        exit;
    }

    //retrieves unsent notifications
    $notificationsToSend = $notificationServ -> prepareNotifications($userId);

    //send an array of notifications to be handled by relevant javascripts
    echo json_encode($notificationsToSend);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //Java script will return all notifications sent
    $data = json_decode(file_get_contents('php://input'), true);

    //Based on the ID, notifications are marked as sent
    if (isset($data['id']))
    {
        $notificationId = (int)$data['id'];
        $notificationServ -> markAsSent($notificationId);
        echo json_encode(['success' => true]);
    }
}
exit;
