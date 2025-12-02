<?php

namespace app\repositories;

use app\models\Conversation;
use app\models\Message;
use infrastructure\Database;
use infrastructure\Utilities;
use PDO;
use PDOException;

class MessageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getConversationsByUserId(int $userId): array
    {
        // This query joins conversations, auctions, items, and finds the last message
        $sql = "
            SELECT 
                c.id as conversation_id,
                c.auction_id,
                i.item_name,
                u_seller.username as seller_name,
                a.end_datetime,
                -- Subquery for last message content
                (SELECT m.message_content 
                 FROM messages m 
                 JOIN participants p2 ON m.participant_id = p2.id 
                 WHERE p2.conversation_id = c.id 
                 ORDER BY m.sent_datetime DESC LIMIT 1) as last_message,
                -- Subquery for last message time
                (SELECT m.sent_datetime 
                 FROM messages m 
                 JOIN participants p2 ON m.participant_id = p2.id 
                 WHERE p2.conversation_id = c.id 
                 ORDER BY m.sent_datetime DESC LIMIT 1) as last_message_time
            FROM conversations c
            JOIN participants p ON c.id = p.conversation_id
            JOIN auctions a ON c.auction_id = a.id
            JOIN items i ON a.item_id = i.id
            LEFT JOIN users u_seller ON i.seller_id = u_seller.id
            WHERE p.user_id = :user_id
            ORDER BY last_message_time DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Get Messages for a specific conversation
    public function getMessagesByConversationId(int $conversationId): array
    {
        $sql = "
            SELECT 
                m.id as message_id,
                m.message_content,
                m.sent_datetime,
                p.user_id,
                u.username
            FROM messages m
            JOIN participants p ON m.participant_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE p.conversation_id = :conversation_id
            ORDER BY m.sent_datetime ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['conversation_id' => $conversationId]);

        // We return raw arrays here to easily access username, but you could map to Message objects
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Get Auction Header Info (Item details, Price)
    public function getAuctionDetails(int $conversationId): ?array
    {
        $sql = "
            SELECT 
                i.item_name,
                u.username as seller_name,
                u.id as seller_id,
                a.end_datetime,
                a.starting_price, -- Assuming current bid logic is handled elsewhere or simple here
                a.id as auction_id,
                a.auction_status as auction_status,
                a.end_datetime - NOW() as remaining_time
            FROM conversations c
            JOIN auctions a ON c.auction_id = a.id
            JOIN items i ON a.item_id = i.id
            JOIN users u ON i.seller_id = u.id
            WHERE c.id = :conversation_id
        ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute(['conversation_id' => $conversationId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    // 4. Send a Message
    public function createMessage(int $conversationId, int $userId, string $content): bool
    {
        // First, find the participant ID for this user in this conversation
        $stmtPart = $this->db->prepare("SELECT id FROM participants WHERE conversation_id = :c_id AND user_id = :u_id");
        $stmtPart->execute(['c_id' => $conversationId, 'u_id' => $userId]);
        $participant = $stmtPart->fetch(PDO::FETCH_ASSOC);

        if (!$participant) {
            return false; // User is not part of this conversation
        }

        $stmt = $this->db->prepare("
            INSERT INTO messages (participant_id, message_content, sent_datetime) 
            VALUES (:pid, :content, NOW())
        ");

        return $stmt->execute([
            'pid' => $participant['id'],
            'content' => $content
        ]);
    }

    public function findOrCreateConversation(int $auctionId, int $buyerId): int
    {
        // 1. CHECK: Does the conversation already exist for this user + auction?
        $sqlCheck = "
            SELECT c.id
            FROM conversations c
            INNER JOIN participants p ON c.id = p.conversation_id
            WHERE c.auction_id = :auction_id
            AND p.user_id = :user_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sqlCheck);
        $stmt->execute([
            'auction_id' => $auctionId,
            'user_id' => $buyerId
        ]);

        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            return (int)$existingId;
        }

        // 2. CREATE: Conversation doesn't exist, so we create it.
        try {
            $this->db->connection->beginTransaction();

            // Step A: Find the Seller ID for this auction
            // We need to know who owns the item to add them as a participant
            $sqlSeller = "
                SELECT i.seller_id 
                FROM auctions a
                JOIN items i ON a.item_id = i.id
                WHERE a.id = :auction_id
            ";
            $stmtSeller = $this->db->prepare($sqlSeller);
            $stmtSeller->execute(['auction_id' => $auctionId]);
            $sellerId = $stmtSeller->fetchColumn();

            if (!$sellerId) {
                throw new \Exception("Cannot create chat: Seller not found for this auction.");
            }

            // Step B: Create the Conversation Record
            $sqlInsertConv = "INSERT INTO conversations (auction_id, started_datetime) VALUES (:auction_id, NOW())";
            $stmtConv = $this->db->prepare($sqlInsertConv);
            $stmtConv->execute(['auction_id' => $auctionId]);

            $newConversationId = (int)$this->db->connection->lastInsertId();

            // Step C: Add Participants
            // We add BOTH the Buyer (current user) and the Seller
            $sqlInsertPart = "INSERT INTO participants (conversation_id, user_id) VALUES (:cid, :uid)";
            $stmtPart = $this->db->prepare($sqlInsertPart);

            // Add Buyer
            $stmtPart->execute(['cid' => $newConversationId, 'uid' => $buyerId]);

            // Add Seller (Only if buyer is not the seller - prevents duplicate entry error or self-chat if logic allows)
            if ($buyerId != $sellerId) {
                $stmtPart->execute(['cid' => $newConversationId, 'uid' => $sellerId]);
            }

            $this->db->connection->commit();

            return $newConversationId;

        } catch (\Exception $e) {
            $this->db->connection->rollBack();
            // Log error here if you have a logger
            throw $e; // Re-throw to be
        }
    }
}