<?php

namespace Nodeloc\Telegram\Notifications;
use Exception;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Illuminate\Contracts\View\Factory;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Illuminate\Support\Arr;

class TelegramMailer
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var TranslatorInterface&Translator
     */
    protected $translator;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    protected $view;

    protected  $telegramclient;

    protected const VIEWS = [
        'newPost' => 'nikovonlas-tg-notify::newPost',
        'postMentioned' => 'nikovonlas-tg-notify::postMentioned',
        'userMentioned' => 'nikovonlas-tg-notify::userMentioned',
        'groupMentioned' => 'nikovonlas-tg-notify::groupMentioned',
        'newDiscussionInTag' => 'nikovonlas-tg-notify::newDiscussionInTag',
        'newPostInTag' => 'nikovonlas-tg-notify::newPostInTag',
        'newDiscussionTag' => 'nikovonlas-tg-notify::newDiscussionTag'
    ];

    /**
     * @param TranslatorInterface&Translator $translator
     * @throws TelegramSDKException
     */
    public function __construct(Mailer $mailer, TranslatorInterface $translator, SettingsRepositoryInterface $settings,Factory $view)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $token = $settings->get('nikovonlas-tg-notify.botToken');

        if (!$token) {
            throw new Exception('No bot token configured for TelegramProvide');
        }
        $this->telegramclient =  new Api($token);
        $this->settings = $settings;
        $this->view = $view;
    }
    /**
     * @param User $actor
     * @return int
     */
    protected function getTelegramId(User $actor)
    {
        $query = LoginProvider::where('user_id', '=', $actor->id);
        $query->where('provider', '=', 'telegram');
        $provider = $query->first();
        return $provider->identifier;
    }
    public function send(BlueprintInterface $blueprint, array $users): void
    {
        foreach ($users as $user) {
            if ($blueprint instanceof MailableInterface) {
                $view = $this->pickBestView($blueprint->getEmailView());
                $class = get_class($blueprint);
                $type = $class::getType();
                if (array_key_exists($type, static::VIEWS)) {
                    $text = $this->view->make(static::VIEWS[$type], compact('blueprint', 'user'))->render();
                } else {
                    $text = $this->view->make($view, compact('blueprint', 'user'))->render();
                }
            } else if ($blueprint instanceof BlueprintInterface) {
                return;
            } else{
                throw new Exception('Notification not compatible with Telegram Mailer');
            }

            $telegram_id = $user->flagrow_telegram_id; // Assuming 'telegram_id' is the key for the user's Telegram ID in the array
            if(!$telegram_id){
                $telegram_id = $this->getTelegramId($user);
                if($telegram_id){
                    $user->flagrow_telegram_id = $telegram_id;
                    $user->save();
                }else{
                    $user->flagrow_telegram_error = 'User telegram id is incorrect.';
                    $user->save();
                    return;
                }
            }
            try {
                $this->telegramclient->sendMessage([
                    'chat_id' => $telegram_id,
                    'text' => $text
                ]);

                // Reset error if everything went right
                if ($user->flagrow_telegram_error) {
                    $user->flagrow_telegram_error = null;
                    $user->save();
                }
            } catch (ClientException $exception) {
                $response = $exception->getResponse();
                if ($response && $response->getStatusCode() !== 403) {
                    throw $exception;
                }
                $user->flagrow_telegram_error = 'unauthorized';
                $json = json_decode($response->getBody()->getContents(), true);

                if ($json && str_contains(Arr::get($json, 'description', ''), 'blocked by the user')) {
                    $user->flagrow_telegram_error = 'blocked';
                }

                $user->save();
            } catch (TelegramSDKException $exception) {
                $user->flagrow_telegram_error = 'unauthorized';
                $user->save();
            }
        }
    }

    /**
     * Read the same way as Illuminate\Mail\Mailer::parseView()
     * @param $view
     * @return string
     * @throws Exception
     */
    protected function pickBestView($view)
    {
        if (is_string($view)) {
            return $view;
        }

        if (is_array($view)) {
            if (isset($view[0])) {
                return $view[0];
            }

            $html = Arr::get($view, 'html');

            if ($html) {
                return $html;
            }

            $text = Arr::get($view, 'text');

            if ($text) {
                return $text;
            }

            $raw = Arr::get($view, 'raw');

            if ($raw) {
                return $raw;
            }
        }

        throw new Exception('No view found for that mailable');
    }

}
