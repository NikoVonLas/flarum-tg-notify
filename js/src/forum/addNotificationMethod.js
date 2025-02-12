import { extend } from 'flarum/extend';
import app from 'flarum/app';
import NotificationGrid from 'flarum/components/NotificationGrid';
import SettingsPage from 'flarum/components/SettingsPage';

export default function () {
  extend(NotificationGrid.prototype, 'notificationMethods', function (items) {
    if (!app.forum.attribute('nikovonlas-tg-notify.enableNotifications')) {
      return;
    }
    if (!app.session || !app.session.user) {
      return;
    }

    let user = app.session.user;
    if (!user || !user.canReceiveTelegramNotifications()) {
      return;
    }
    // Add telegram notifications method column
    items.add('telegram', {
      name: 'telegram',
      icon: 'fab fa-telegram-plane',
      label: app.translator.trans('nikovonlas-tg-notify.forum.settings.notify_by_telegram_heading'),
    });
  });
  extend(SettingsPage.prototype, 'accountItems', function (items) {
    if (!app.forum.attribute('nikovonlas-tg-notify.enableNotifications')) {
      return;
    }
    if (!app.session || !app.session.user) {
      return;
    }

    let user = app.session.user;
    if (user && !user.canReceiveTelegramNotifications()) {
      // add button to link current account with telegram
      const authUrl = app.forum.attribute('baseUrl') + '/auth/telegram';
      const botUsername = app.forum.attribute('nikovonlas-tg-notify.botUsername');

      // Replace the TelegramProvide widget script
      items.add(
        'nikovonlas-tg-notify',
        m('script', {
          async: true,
          src: 'https://telegram.org/js/telegram-widget.js?22',
          'data-telegram-login': botUsername,
          'data-size': 'large',
          'data-radius': '10',
          'data-auth-url': authUrl,
          'data-request-access': 'write',
        })
      );
    }
  });
  extend(SettingsPage.prototype, 'notificationsItems', function (items) {
    if (!app.forum.attribute('nikovonlas-tg-notify.enableNotifications')) {
      return;
    }
    if (!app.session || !app.session.user) {
      return;
    }

    let user = app.session.user;
    if (!user || !user.nodelocTelegramError()) {
      return;
    }
    const botUsername = app.forum.attribute('nikovonlas-tg-notify.botUsername');

    items.add('nodelocTelegramError', {
      view() {
        return m(
          '.Alert',
          m(
            'p',
            app.translator.trans('nikovonlas-tg-notify.forum.settings.unblock_telegram_bot', {
              a: m('a', { href: 'https://t.me/' + botUsername }),
              username: '@' + botUsername,
            })
          )
        );
      },
    });
  });
}
