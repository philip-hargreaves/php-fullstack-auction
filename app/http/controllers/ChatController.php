<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;

class ChatController extends Controller
{
    private $chatServ;
    private $authServ;

    public function __construct()
    {
        $this->chatServ = DIContainer::get('chatServ');
        $this->authServ = DIContainer::get('authServ');
    }

    /** GET /conversations or GET /conversations/{id} */
    public function show(array $params = []): void
    {
        $userId = $this->authServ->getUserId();
        if (!$userId) {
            $this->redirect('/register');
        }

        $conversations = $this->chatServ->getConversationsByUserId($userId);
        $activeConversationId = $params['id'] ?? null;

        $activeChat = null;
        $auctionDetails = null;

        // Default to first conversation if none selected
        if (!$activeConversationId && !empty($conversations)) {
            $activeConversationId = $conversations[0]['conversation_id'];
        }

        if ($activeConversationId) {
            $data = $this->chatServ->getConversationHistory($activeConversationId, $userId);
            $activeChat = $data['messages'];
            $auctionDetails = $data['details'];
        }

        $this->view('chatroom', compact('conversations', 'activeConversationId', 'activeChat', 'auctionDetails'));
    }

    /** POST /conversations/{id}/messages */
    public function store(array $params = []): void
    {
        $this->ensurePost();

        $userId = $this->authServ->getUserId();
        if (!$userId) {
            $this->redirect('/register');
        }

        $conversationId = $params['id'] ?? Request::post('conversation_id', '');
        $message = Request::post('message', '');

        $this->chatServ->postMessage($conversationId, $userId, $message);

        $this->redirect('/conversations/' . $conversationId);
    }
}

