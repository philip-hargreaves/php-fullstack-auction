<?php

namespace app\models;

class Participant
{
    // PROPERTIES
    private int $participantId;
    private int $conversationId;
    private int $userId;

    // CONSTRUCTOR
    public function __construct(
        int $participantId,
        int $conversationId,
        int $userId,
    )
    {
        $this->participantId = $participantId;
        $this->conversationId = $conversationId;
        $this->userId = $userId;
    }

    // GETTER
    public function getParticipantId(): int
    {
        return $this->participantId;
    }

    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    //SETTER
    public function setConversationId(int $conversationId): void{
        $this->conversationId = $conversationId;
    }

    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    public function setParticipantId(int $participantId): void {
        $this->participantId = $participantId;
    }
}