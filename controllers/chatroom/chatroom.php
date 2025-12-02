<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

session_start();
$chatService = DIContainer::get('chatServ');
$authService = DIContainer::get('authServ');

$userId = $authService->getUserId();
if (!$userId) {
    header('Location: /register');
    exit;
}

$conversations = $chatService->getConversationsByUserId($userId);
$activeConversationId = Request::get('conversation_id');

$activeChat = null;
$auctionDetails = null;

// If no specific chat selected, but list exists, select the first one
if (!$activeConversationId && !empty($conversations)) {
    $activeConversationId = $conversations[0]['conversation_id'];
}

if ($activeConversationId) {
    $data = $chatService->getConversationHistory($activeConversationId, $userId);
    $activeChat = $data['messages'];
    $auctionDetails = $data['details'];
}

require Utilities::basePath('views/chatroom.view.php');