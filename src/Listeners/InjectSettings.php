<?php

namespace Nodeloc\Telegram\Listeners;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\Event\Serializing;
use Flarum\Settings\SettingsRepositoryInterface;

class InjectSettings
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function subscribe($events)
    {
        $events->listen(Serializing::class, [$this, 'settings']);
    }

    public function settings(Serializing $event)
    {
        if ($event->serializer instanceof ForumSerializer) {
            $event->attributes['nikovonlas-tg-notify.enableNotifications'] = (bool)$this->settings->get('nikovonlas-tg-notify.enableNotifications');
            $event->attributes['nikovonlas-tg-notify.botUsername'] = $this->settings->get('nikovonlas-tg-notify.botUsername');
        }
    }
}
