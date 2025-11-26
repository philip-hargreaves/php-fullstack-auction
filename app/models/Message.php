<?php

namespace app\models;

use DateTime;

class Message
{
    private int $messageId;
    private int $participantId;
    private string $messageContent;
    private DateTime $sentDateTime;

    //RELATIONSHIP PROPERTIES
    private Participant $participant;

    public function __construct(
        int $messageId,
        int $participantId,
        string $messageContent,
        DateTime $sentDateTime,
    )
    {
        $this->messageId = $messageId;
        $this->participantId = $participantId;
        $this->messageContent = $messageContent;
        $this->sentDateTime = $sentDateTime;
    }

    //GETTER
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getParticipantId(): int
    {
        return $this->participantId;
    }

    public function getMessageContent(): string
    {
        return $this->messageContent;
    }

    //SETTER
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    //RELATIONSHIP GETTER/SETTER
    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function setParticipant(Participant $participant): void
    {
        $this->participant = $participant;
    }
}