<?php
use infrastructure\DIContainer;
use infrastructure\SmtpConfig;
use infrastructure\Mailer;

session_start();

header('Content-Type: application/json');

$notificationServ = DIContainer::get('notificationServ');

// Rate-limited email sending - only try once per minute to avoid blocking every request
$lockFile = sys_get_temp_dir() . '/auction_email_lock.txt';
$now = time();
$lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;

if ($now - $lastRun >= 60) {
    // Update lock file first to prevent other requests from also sending
    file_put_contents($lockFile, $now);
    
    // Create batch notifications
    $notificationServ->createBatchNotification();
    
    // Send up to 5 pending emails (uses optimized single-query fetch)
    $smtpConfig = new SmtpConfig();
    $mailer = new Mailer($smtpConfig->getSmtpConfig());
    
    $emailNotifications = $notificationServ->prepareEmailNotifications();
    $emailLimit = 5;
    $sent = 0;
    
    foreach ($emailNotifications as $email) {
        if ($sent >= $emailLimit) break;
        
        try {
            $mailer->send($email['recipientUserEmail'], $email['messageSubject'], $email['message']);
            $notificationServ->markAsSent($email['notificationId']);
            $sent++;
        } catch (Exception $e) {
            error_log("Failed to send email: " . $e->getMessage());
        }
    }
}

// Handle popup notifications (fast - uses optimized single query)
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
