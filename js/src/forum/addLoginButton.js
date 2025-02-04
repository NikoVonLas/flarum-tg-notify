import { extend } from 'flarum/extend';
import LogInButtons from 'flarum/components/LogInButtons';
import app from 'flarum/app';

export default function () {
  const enableWidget = app.forum.attribute('nodeloc-telegram.enableWidget');
  if (!enableWidget) {
    return;
  }
  const authUrl = app.forum.attribute('baseUrl') + '/auth/telegram';
  const botUsername = app.forum.attribute('nodeloc-telegram.addWidget');
  const size = app.forum.attribute('nodeloc-telegram.widgetSize');
  const radius = app.forum.attribute('nodeloc-telegram.widgetRadius');
  const userPic = app.forum.attribute('nodeloc-telegram.widgetUserPic');
  extend(LogInButtons.prototype, 'items', function (items) {
    // Replace the TelegramProvide widget script
    items.add(
      'nodeloc-telegram',
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
