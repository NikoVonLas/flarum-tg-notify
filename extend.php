<?php

namespace Nodeloc\Telegram;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Extend;
use Nodeloc\Telegram\Controllers\TelegramAuthController;
use Nodeloc\Telegram\Driver\TelegramNotificationDriver;
use Nodeloc\Telegram\Listeners\AddUserAttributes;
use Nodeloc\Telegram\Provider\TelegramNotificationServiceProvider;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Locales(__DIR__ . '/resources/locale')),
    (new Extend\Event)
        ->subscribe(Driver\TelegramNotificationDriver::class)
        ->subscribe(Listeners\InjectSettings::class),
    (new Extend\ApiSerializer(UserSerializer::class))->attributes(AddUserAttributes::class),
    (new Extend\Notification())
        ->driver('telegram', TelegramNotificationDriver::class),
    (new Extend\Settings)
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.botUsername')
        ->serializeToForum('nodeloc-telegram.botToken', 'nodeloc-telegram.botToken')
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.enableWidget', 'boolval', true)
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.widgetSize')
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.widgetRadius', 'intval', '10')
        ->serializeToForum('nodeloc-telegram.botUsername', 'nodeloc-telegram.widgetUserPic', 'boolval', false)
        ->serializeToForum('nodeloc-telegram.enableNotifications', 'nodeloc-telegram.enableNotifications', 'boolval', true),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    (new Extend\Routes('forum'))
        ->get('/auth/telegram', 'auth.telegram', TelegramAuthController::class),
];


