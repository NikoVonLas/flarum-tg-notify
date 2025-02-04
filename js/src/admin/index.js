import app from 'flarum/admin/app';

app.initializers.add('nikovonlas-tg-notify', () => {
  app.extensionData
    .for('nikovonlas-tg-notify')
    .registerSetting(
      {
        setting: 'nikovonlas-tg-notify.botUsername',
        type: 'text',
        label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.botUsername'),
      },
      15
    )
    .registerSetting(
      {
        setting: 'nikovonlas-tg-notify.botToken',
        type: 'text',
        label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.botToken'),
      },
      15
    )
    .registerSetting({
      setting: 'nikovonlas-tg-notify.enableWidget',
      type: 'boolean',
      label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.enableWidget'),
    })
    .registerSetting({
      setting: 'nikovonlas-tg-notify.widgetSize',
      type: 'select',
      label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetSize'),
      options: {
        large: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetSizeLarge'),
        medium: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetSizeMedium'),
        small: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetSizeSmall'),
      },
      default: 'large',
    })
    .registerSetting({
      setting: 'nikovonlas-tg-notify.widgetRadius',
      type: 'number',
      label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetRadius'),
      min: 0,
      max: 20,
    })
    .registerSetting({
      setting: 'nikovonlas-tg-notify.widgetUserPic',
      type: 'boolean',
      label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.widgetUserPic'),
    })
    .registerSetting({
      setting: 'nikovonlas-tg-notify.enableNotifications',
      type: 'boolean',
      label: app.translator.trans('nikovonlas-tg-notify.admin.settings.field.enableNotifications'),
    });
});
