<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\SmtpConfig;
use infrastructure\Mailer;

class NotificationController extends Controller
{
    private $notificationServ;

    public function __construct()
    {
        $this->notificationServ = DIContainer::get('notificationServ');
    }

    /** GET /notifications - Get pending popup notifications (JSON) */
    public function index(array $params = []): void
    {
        $this->processEmailQueue();

        $userId = $this->userId();

        if ($userId === null) {
            $this->json([]);
        }

        $notifications = $this->notificationServ->preparePopUpNotifications($userId);
        $this->json($notifications);
    }

    /** POST /notifications - Mark notification as sent (JSON) */
    public function markSent(array $params = []): void
    {
        $this->processEmailQueue();

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id'])) {
            $notificationId = (int)$data['id'];
            $this->notificationServ->markAsSent($notificationId);
            $this->json(['success' => true]);
        }

        $this->json(['success' => false]);
    }

    /** Process email queue (rate-limited to once per minute) */
    private function processEmailQueue(): void
    {
        $lockFile = sys_get_temp_dir() . '/auction_email_lock.txt';
        $now = time();
        $lastRun = file_exists($lockFile) ? (int)file_get_contents($lockFile) : 0;

        if ($now - $lastRun < 60) {
            return;
        }

        file_put_contents($lockFile, $now);

        // Create batch notifications
        $this->notificationServ->createBatchNotification();

        // Send up to 5 pending emails
        $smtpConfig = new SmtpConfig();
        $mailer = new Mailer($smtpConfig->getSmtpConfig());

        $emailNotifications = $this->notificationServ->prepareEmailNotifications();
        $sent = 0;

        foreach ($emailNotifications as $email) {
            if ($sent >= 5) break;

            try {
                $mailer->send($email['recipientUserEmail'], $email['messageSubject'], $email['message']);
                $this->notificationServ->markAsSent($email['notificationId']);
                $sent++;
            } catch (\Exception $e) {
                error_log("Failed to send email: " . $e->getMessage());
            }
        }
    }
}

