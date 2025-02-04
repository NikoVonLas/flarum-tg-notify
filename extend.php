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
    (new Extend\View)
        ->namespace('nikovonlas-tg-notify', __DIR__.'/views'),
    (new Extend\Event)
        ->subscribe(Driver\TelegramNotificationDriver::class)
        ->subscribe(Listeners\InjectSettings::class),
    (new Extend\ApiSerializer(UserSerializer::class))->attributes(AddUserAttributes::class),
    (new Extend\Notification())
        ->driver('telegram', TelegramNotificationDriver::class),
    (new Extend\Settings)
        ->serializeToForum('nikovonlas-tg-notify.botUsername', 'nikovonlas-tg-notify.botUsername')
        ->serializeToForum('nikovonlas-tg-notify.botToken', 'nikovonlas-tg-notify.botToken')
        ->serializeToForum('nikovonlas-tg-notify.botUsername', 'nikovonlas-tg-notify.enableWidget', 'boolval', true)
        ->serializeToForum('nikovonlas-tg-notify.botUsername', 'nikovonlas-tg-notify.widgetSize')
        ->serializeToForum('nikovonlas-tg-notify.botUsername', 'nikovonlas-tg-notify.widgetRadius', 'intval', '10')
        ->serializeToForum('nikovonlas-tg-notify.botUsername', 'nikovonlas-tg-notify.widgetUserPic', 'boolval', false)
        ->serializeToForum('nikovonlas-tg-notify.enableNotifications', 'nikovonlas-tg-notify.enableNotifications', 'boolval', true),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    (new Extend\Routes('forum'))
        ->get('/auth/telegram', 'auth.telegram', TelegramAuthController::class),
];


