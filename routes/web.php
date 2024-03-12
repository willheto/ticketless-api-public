<?php

/** @var \Laravel\Lumen\Routing\Router $router */


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Public endpoints
$router->post('admin/user/login', 'User\AdminAuthController@login');
$router->post('admin/user/auth', 'User\AdminAuthController@authenticate');

$router->post('user/login', 'User\UserAuthController@login');
$router->post('user/auth', 'User\UserAuthController@authenticate');
$router->get('users/{userID}', 'User\UsersController@getSingleUserPublicData');
$router->post('users', 'User\UsersController@createUser');
$router->post('users/forgot-password', 'User\UserAuthController@forgotPassword');
$router->post('users/check-code', 'User\UserAuthController@checkCode');

$router->get('events', 'Event\EventsController@getActivePublicEvents');
$router->get('events/{eventID}', 'Event\EventsController@getSingleEvent');
$router->get('tickets/{ticketID}/event', 'Event\EventsController@getEventByTicketID');
$router->get('organizations/{organizationID}/events', 'Event\EventsController@getOrganizationPublicEvents');

$router->get('tickets', 'Ticket\TicketsController@getAllTickets');
$router->get('tickets/{ticketID}', 'Ticket\TicketsController@getSingleTicket');
$router->get('events/{eventID}/tickets', 'Ticket\TicketsController@getTicketsByEventID');
$router->get('users/{userID}/tickets', 'Ticket\TicketsController@getTicketsByUserID');
$router->get('organizations/{organizationID}/tickets', 'Ticket\TicketsController@getTicketsByOrganizationID');

$router->get('advertisements/active', 'Advertisement\AdvertisementsController@getActiveAdvertisements');
$router->post('advertisements/{advertisementID}/view', 'Advertisement\AdvertisementsController@viewAdvertisement');
$router->post('advertisements/{advertisementID}/click', 'Advertisement\AdvertisementsController@clickAdvertisement');

$router->post('push-api/register', 'Push\PushController@register');
$router->get('push-api/vapid-public-key', 'Push\PushController@getVapidPublicKey');
$router->post('push-api/send-notification', 'Push\PushController@sendPushNotification');

$router->get('announcements', 'Announcement\AnnouncementsController@getActiveAnnouncements');

// These endpoints require user authentication
$router->group(['middleware' => App\Http\Middleware\AuthenticateMiddleware::class], function ($router) {
    $router->patch('users', 'User\UsersController@updateUser');
    $router->post('users/check-password', 'User\UserAuthController@checkPassword');
    $router->post('users/report', 'User\UsersController@reportUser');

    $router->post('events', 'Event\EventsController@createEvent');

    $router->patch('tickets', 'Ticket\TicketsController@updateTicket');
    $router->post('tickets', 'Ticket\TicketsController@createTicket');
    $router->delete('tickets', 'Ticket\TicketsController@deleteTicket');

    $router->get('chats/{chatID}', 'Chat\ChatsController@getSingleChat');
    $router->get('users/{userID}/chats', 'Chat\ChatsController@getChatsByUserID');
    $router->post('chats', 'Chat\ChatsController@createChat');
    $router->patch('chats', 'Chat\ChatsController@updateChat');

    $router->get('messages/unread-messages', 'Message\MessagesController@userHasUnreadMessages');
    $router->get('chats/{chatID}/messages', 'Message\MessagesController@getAllMessagesByChatID');
    $router->patch('chats/{chatID}/messages', 'Message\MessagesController@markMessagesAsRead');
    $router->post('messages', 'Message\MessagesController@createMessage');
});

// These endpoints require admin authentication
$router->group(['middleware' => App\Http\Middleware\AdminAuthenticateMiddleware::class], function ($router) {
    $router->patch('events', 'Event\EventsController@updateEvent');
    $router->get('admin/events', 'Event\EventsController@getOrganizationEvents');
    $router->delete('events', 'Event\EventsController@deleteEvent');
});

// These endpoint require superadmin authentication
$router->group(['middleware' => App\Http\Middleware\SuperadminAuthenticateMiddleware::class], function ($router) {
    $router->get('superadmin/meta', 'Meta\MetaController@getSuperadminMeta');

    $router->get('superadmin/events', 'Event\EventsController@getAllEvents');

    $router->get('superadmin/organizations', 'Organization\OrganizationsController@getAllOrganizations');
    $router->post('superadmin/organizations', 'Organization\OrganizationsController@createOrganization');
    $router->patch('superadmin/organizations', 'Organization\OrganizationsController@updateOrganization');
    $router->delete('superadmin/organizations', 'Organization\OrganizationsController@deleteOrganization');

    $router->get('superadmin/users', 'User\UsersController@getAllUsers');
    $router->patch('superadmin/users', 'User\UsersController@superadminUpdateUser');
    $router->delete('superadmin/users', 'User\UsersController@deleteUser');

    $router->get('superadmin/advertisements', 'Advertisement\AdvertisementsController@getAllAdvertisements');
    $router->post('superadmin/advertisements', 'Advertisement\AdvertisementsController@createAdvertisement');
    $router->patch('superadmin/advertisements', 'Advertisement\AdvertisementsController@updateAdvertisement');
    $router->delete('superadmin/advertisements', 'Advertisement\AdvertisementsController@deleteAdvertisement');

    $router->get('superadmin/announcements', 'Announcement\AnnouncementsController@getAllAnnouncements');
    $router->post('superadmin/announcements', 'Announcement\AnnouncementsController@createAnnouncement');
    $router->patch('superadmin/announcements', 'Announcement\AnnouncementsController@updateAnnouncement');
    $router->delete('superadmin/announcements', 'Announcement\AnnouncementsController@deleteAnnouncement');

    $router->get('superadmin/files', 'File\FilesController@getAllFiles');
    $router->get('superadmin/files/{fileID}', 'File\FilesController@getSingleFile');
    $router->post('superadmin/files', 'File\FilesController@createFile');
    $router->patch('superadmin/files', 'File\FilesController@updateFile');
    $router->delete('superadmin/files', 'File\FilesController@deleteFile');
});
