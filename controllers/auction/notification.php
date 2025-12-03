<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\SmtpConfig;
use infrastructure\Mailer;

session_start();

header('Content-Type: application/json');

$notificationServ = DIContainer::get('notificationServ');
$auctionServ = DIContainer::get('auctionServ');
$bidServ = DIContainer::get('bidServ');
$itemServ = DIContainer::get('itemServ');
$watchlistServ = DIContainer::get('watchlistServ');

//Send emails regardless of who has logged in
$smtpConfig = new SmtpConfig();
$config = $smtpConfig->getSmtpConfig();
$mailer = new Mailer($config);

// fetch all notifications that have NOT had email sent
$emailNotifications = $notificationServ -> prepareEmailNotifications();

if(!empty($emailNotifications))
{
    foreach ($emailNotifications as $email)
    {
        try
        {
            $mailer->send($email['recipientUserEmail'], $email['messageSubject'], $email['message']);

            // mark as sent
            $notificationServ->markAsSent($email['notificationId']);
        }
        catch (Exception $e)
        {
            // Log error. Continues to next email. Ones failed to send not marked as sent
            error_log("failed to send email: " . $e->getMessage());
            continue;
        }
    }
}

$notificationServ -> createBatchNotification();

//Handles popup notifications
$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //when no user is logged in, no notifications are sent.
    if ($userId === null) {
        echo json_encode([]);
        exit;
    }

    //retrieves unsent notifications
    $notificationsToSend = $notificationServ -> preparePopUpNotifications($userId);

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
