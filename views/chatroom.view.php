<?php
use infrastructure\DIContainer;
/**
 * @var $conversations
 * @var $auctionDetails
 * @var $activeChat
 * @var $bubbleClass
 * @var $activeConversationId
 */
?>

<?php require \infrastructure\Utilities::basePath('views/partials/header.php'); ?>

<div class="chat-container">
    <div class="sidebar">
        <div class="sidebar-header">
            My Conversations
        </div>
        <div class="conversation-list">
            <?php if (empty($conversations)): ?>
                <div style="padding: 30px 20px; text-align: center; color: var(--color-text-secondary);">
                    <small>No conversations yet.</small>
                </div>
            <?php else: ?>
                <?php foreach($conversations as $conv): ?>
                    <?php
                    // 1. Get the requested ID safely
                    $currentId = $_GET['id'] ?? null;

                    // 2. Fallback: If no ID in URL, the controller likely selected the first one by default
                    if (!$currentId && isset($activeConversationId)) {
                        $currentId = $activeConversationId;
                    }

                    $isActive = ($currentId == $conv['conversation_id']) ? 'active' : '';
                    $time = date('M d', strtotime($conv['last_message_time'] ?? "now"));
                    ?>

                    <a href="/chatroom?conversation_id=<?= $conv['conversation_id'] ?>" style="text-decoration: none;">
                        <div class="conversation-item <?= $isActive ?>">
                            <div class="item-title">
                                <span><?= htmlspecialchars(substr($conv['item_name'], 0, 22)) . (strlen($conv['item_name']) > 22 ? '...' : '') ?></span>
                                <span class="msg-time"><?= $time ?></span>
                            </div>
                            <div class="seller-info">
                                @<?= htmlspecialchars($conv['seller_name']) ?>
                            </div>
                            <div class="last-msg">
                                <?= htmlspecialchars($conv['last_message'] ?? 'Start chatting...') ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-area">
        <?php if ($auctionDetails): ?>
            <div class="chat-header">
                <div class="header-item-info">
                    <h2><?= htmlspecialchars($auctionDetails['item_name']) ?></h2>
                    <div class="header-item-details">
                        Seller: <?= htmlspecialchars($auctionDetails['seller_name']) ?> |
                        Ends: <?= date('M d, Y', strtotime($auctionDetails['end_datetime'])) ?>
                    </div>
                </div>
                <div class="header-price">
                    Current: $<?= number_format($auctionDetails['starting_price'], 2) ?>
                </div>
            </div>

            <div class="messages-box" id="messageBox">
                <?php if (empty($activeChat)): ?>
                    <p style="text-align:center; color:#999; margin-top: 20px;">Start the conversation!</p>
                <?php else: ?>
                    <?php foreach($activeChat as $msg): ?>
                        <?php
                        $isMe = ($msg['user_id'] == $_SESSION['user_id']);
                        $bubbleClass = $isMe ? 'message-sent' : 'message-received';
                        ?>
                        <div class="message-bubble <?= $bubbleClass ?>">
                            <?= nl2br(htmlspecialchars($msg['message_content'])) ?>
                            <div class="message-meta">
                                <?= date('H:i', strtotime($msg['sent_datetime'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="input-area">
                <form action="/send-message" method="POST" class="input-form">
                    <input type="hidden" name="conversation_id" value="<?= $activeConversationId ?>">
                    <input type="text" name="message" placeholder="Type a message..." required autocomplete="off">
                    <button type="submit">Send</button>
                </form>
            </div>
        <?php else: ?>
            <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#999;">
                Select a conversation to start chatting
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Auto-scroll to bottom of chat
    const messageBox = document.getElementById('messageBox');
    if(messageBox) {
        messageBox.scrollTop = messageBox.scrollHeight;
    }
</script>