import app from 'flarum/app';
import User from 'flarum/models/User';
import Model from 'flarum/Model';
import addLoginButton from './addLoginButton';
import addNotificationMethod from './addNotificationMethod';
app.initializers.add('nikovonlas-tg-notify', () => {
  User.prototype.canReceiveTelegramNotifications = Model.attribute('canReceiveTelegramNotifications');
  User.prototype.nodelocTelegramError = Model.attribute('nodelocTelegramError');
  addLoginButton();
  addNotificationMethod();
});
