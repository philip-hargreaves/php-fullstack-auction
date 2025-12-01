<?php
use infrastructure\DIContainer;
use infrastructure\Request;

session_start();

$authService = DIContainer::get('authServ');
$chatService = DIContainer::get('chatServ');
$userId = $authService->getUserId();

// Only accept POST requests for registration attempts
if (!Request::isPost() && $userId) {
    header('Location: /register');
    exit;
}

$conversationId = Request::post('conversation_id', '');
$message = Request::post('message', '');

$chatService->postMessage($conversationId, $userId, $message);

// Redirect back to the conversation
header("Location: /chatroom?id=" . $conversationId);

exit;