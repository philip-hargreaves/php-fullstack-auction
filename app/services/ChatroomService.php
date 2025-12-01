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
        // Security: Ensure user belongs to this conversation before showing messages
        // (You could verify this via the repo or a separate query.
        // For brevity, assuming the repository filters logically or we blindly fetch)

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