<?php

namespace app\models;

class Participant
{
    private int $participantId;
    private int $conversationId;
    private int $userId;

    //RELATIONSHIP PROPERTIES
    private Conversation $conversation;

    private User $user;

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

    //GETTER
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

    // RELATIONSHIP GETTERS/SETTERS
    public function setConversation(Conversation $conversation): void
    {
        $this->conversation = $conversation;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}