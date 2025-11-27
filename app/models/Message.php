<?php

namespace app\models;

use DateTime;

class Message
{
    private int $messageId;
    private int $participantId;
    private string $messageContent;
    private DateTime $sentDatetime;

    //RELATIONSHIP PROPERTIES

    public function __construct(
        int      $messageId,
        int      $participantId,
        string   $messageContent,
        string|DateTime $sentDatetime,
    )
    {
        $this->messageId = $messageId;
        $this->participantId = $participantId;
        $this->messageContent = $messageContent;
        $this->sentDatetime = is_string($sentDatetime) ? new DateTime($sentDatetime) : $sentDatetime;    }

    // GETTER
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

    public function getSentDatetime(): DateTime {
        return $this->sentDatetime;
    }

    // SETTER
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function setParticipantId(int $participantId): void {
        $this->participantId = $participantId;
    }

    public function setMessageContent(string $messageContent): void {
        $this->messageContent = $messageContent;
    }

    public function setSentDatetime(DateTime $sentDatetime): void {
        $this->sentDatetime = $sentDatetime;
    }

    // RELATIONSHIP GETTER/SETTER
}