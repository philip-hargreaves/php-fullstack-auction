<?php

namespace app\services;

use app\repositories\MessageRepository;

class ChatroomService
{
    private MessageRepository $messageRepo;

    public function __construct(MessageRepository $messageRepo)
    {
        $this->messageRepo = $messageRepo;
    }

    public function getConversationsByUserId(int $userId): array
    {
        return $this->messageRepo->getConversationsByUserId($userId);
    }

    public function getConversationHistory(int $conversationId, int $userId): array
    {
        $details = $this->messageRepo->getAuctionDetails($conversationId);
        $messages = $this->messageRepo->getMessagesByConversationId($conversationId);

        return [
            'details' => $details,
            'messages' => $messages
        ];
    }

    public function postMessage(int $conversationId, int $userId, string $content): bool
    {
        $cleanContent = trim(htmlspecialchars($content));

        if (empty($cleanContent)) {
            return false;
        }

        return $this->messageRepo->createMessage($conversationId, $userId, $cleanContent);
    }
}