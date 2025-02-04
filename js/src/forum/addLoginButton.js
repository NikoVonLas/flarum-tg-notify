import { extend } from 'flarum/extend';
import LogInButtons from 'flarum/components/LogInButtons';
import app from 'flarum/app';

export default function () {
  const enableWidget = app.forum.attribute('nikovonlas-tg-notify.enableWidget');
  if (!enableWidget) {
    return;
  }
  const authUrl = app.forum.attribute('baseUrl') + '/auth/telegram';
  const botUsername = app.forum.attribute('nikovonlas-tg-notify.addWidget');
  const size = app.forum.attribute('nikovonlas-tg-notify.widgetSize');
  const radius = app.forum.attribute('nikovonlas-tg-notify.widgetRadius');
  const userPic = app.forum.attribute('nikovonlas-tg-notify.widgetUserPic');
  extend(LogInButtons.prototype, 'items', function (items) {
    // Replace the TelegramProvide widget script
    items.add(
      'nikovonlas-tg-notify',
      m('script', {
        async: true,
        src: 'https://telegram.org/js/telegram-widget.js?22',
        'data-telegram-login': botUsername,
        'data-size': size,
        'data-radius': radius,
        'data-userpic': userPic,
        'data-auth-url': authUrl,
        'data-request-access': 'write',
      })
    );
  });
}
