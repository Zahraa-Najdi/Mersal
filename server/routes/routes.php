<?php
$routes = [
    '/users' => ['controllers' => 'UserController', 'method' => 'getUsers'],
    '/users/byemail' => ['controllers' => 'UserController', 'method' => 'getUserByEmail'],
    '/users/create' => ['controllers' => 'UserController', 'method' => 'createUser'],
    '/users/update' => ['controllers' => 'UserController', 'method' => 'updateUser'],
    '/users/delete' => ['controllers' => 'UserController', 'method' => 'deleteUser'],


    '/chats' => ['controllers' => 'ChatController', 'method' => 'getChats'],
    '/chats/create' => ['controllers' => 'ChatController', 'method' => 'createChat'],
    '/chats/update' => ['controllers' => 'ChatController', 'method' => 'updateChat'],
    '/chats/delete' => ['controllers' => 'ChatController', 'method' => 'deleteChat'],


    '/chatMembers' => ['controllers' => 'ChatMemberController', 'method' => 'getChatMembers'],
    '/chatMembers/create' => ['controllers' => 'ChatMemberController', 'method' => 'createChatMember'],
    '/chatMembers/update' => ['controllers' => 'ChatMemberController', 'method' => 'updateChatMember'],
    '/chatMembers/delete' => ['controllers' => 'ChatMemberController', 'method' => 'deleteChatMember'],
    '/chatMembers/getChats' => ['controllers' => 'ChatMemberController', 'method' => 'getAllChats'],

    '/messages' => ['controllers' => 'MessageController', 'method' => 'getMessages'],
    '/messages/create' => ['controllers' => 'MessageController', 'method' => 'createMessage'],
    '/messages/update' => ['controllers' => 'MessageController', 'method' => 'updateMessage'],
    '/messages/delete' => ['controllers' => 'MessageController', 'method' => 'deleteMessage'],
    '/messages/turnMessagesToRead' => ['controllers' => 'MessageController', 'method' => 'turnMessagesToRead'],


    '/AiAnalyze' => ['controllers' => 'AiAnalyze.php', 'method' => 'analyze'],
];