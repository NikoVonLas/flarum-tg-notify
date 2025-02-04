<?php

namespace Nodeloc\Telegram\Controllers;

use Flarum\Forum\Auth\Registration;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Flarum\User\LoginProvider;
use Flarum\Locale\Translator;

class TelegramAuthController implements RequestHandlerInterface
{
    protected $authResponse;
    protected $settings;
    protected $url;
    protected $client;

    public function __construct(ResponseFactory $authResponse, SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->authResponse = $authResponse;
        $this->settings = $settings;
        $this->url = $url;

        $token = $settings->get('nikovonlas-tg-notify.botToken');

        if (!$token) {
            throw new Exception('No bot token configured for TelegramProvide');
        }
    }

    public function handle(Request $request): ResponseInterface
    {
        $provider = 'telegram';

        try {
            $auth = $this->checkTelegramAuthorization($_GET);
            $user = $request->getAttribute('actor');

            if ($user && $user->id) {
                $identifier = $auth['id'] ?? null;

                if ($this->checkTelegramId($provider, $identifier)) {
                    $this->processContinue(false);
                }

                $user->loginProviders()->create(compact('provider', 'identifier'));
                $this->processContinue(true);
            }

            $suggestions = [];
            if ($auth['username']) $suggestions['username'] = $auth['username'];
            if ($auth['photo_url']) $suggestions['avatar_url'] = $auth['photo_url'];


            return $this->authResponse->make(
                $provider, $auth['id'], function (Registration $registration) use ($suggestions) {
                    // 设置 TelegramProvide 提供的信息
                    $registration->provide('username', $suggestions['username']);
                    $registration->provide('avatar_url', $suggestions['avatar_url']);
                    $registration->setPayload($suggestions);
                }
            );
        } catch (Exception $e) {
            $this->processContinue(false);
            // 在异常情况下返回错误响应
            return new HtmlResponse('Error: ' . $e->getMessage(), 500);
        }
    }

    public function processContinue(bool $isSuccess): HtmlResponse
    {
        $url = resolve(UrlGenerator::class)->to('forum');
        $translator = resolve(Translator::class);
        if(!$isSuccess){
            $redirect = $url->base().'/settings';
            $href = htmlentities($redirect);
            $continue = htmlentities($translator->trans('clarkwinkelmann-auth-popup-failsafe.api.auth.continue'));
            $newBody = new Stream('php://temp', 'wb+');
            $info = "You can\'t link this telegram account to this user.";
            $newBody->write("<style>body{text-align:center;padding:20px;padding-top:40vh}p{font-family:sans-serif;font-size:2em;color:#aaa}a{color:#333}</style><p>$info</p><p><a href=\"$href\">$continue</a></p>");
            $newBody->rewind();
            return new HtmlResponse($newBody);
        }else {
            $redirect = $url->base() . '/settings';
            $href = htmlentities($redirect);
            $info = htmlentities($translator->trans('clarkwinkelmann-auth-popup-failsafe.api.auth.info'));
            $continue = htmlentities($translator->trans('clarkwinkelmann-auth-popup-failsafe.api.auth.continue'));
            $newBody = new Stream('php://temp', 'wb+');
            $newBody->write("<style>body{text-align:center;padding:20px;padding-top:40vh}p{font-family:sans-serif;font-size:2em;color:#aaa}a{color:#333}</style><p>$info</p><p><a href=\"$href\">$continue</a></p>");
            $newBody->rewind();
            return new HtmlResponse($newBody);
        }
    }
    protected function checkTelegramId($provider, $identifier)
    {
        $provider = LoginProvider::where(compact('provider', 'identifier'))->first();
        return $provider;
    }

    function checkTelegramAuthorization($auth_data) {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $this->settings->get('nikovonlas-tg-notify.botToken'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            throw new Exception('Data is NOT from TelegramProvide');
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            throw new Exception('Data is outdated');
        }
        return $auth_data;
    }
}
