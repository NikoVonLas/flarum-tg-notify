import app from 'flarum/admin/app';

app.initializers.add('nodeloc-telegram', () => {
  app.extensionData
    .for('nodeloc-telegram')
    .registerSetting(
      {
        setting: 'nodeloc-telegram.botUsername',
        type: 'text',
        label: app.translator.trans('nodeloc-telegram.admin.settings.field.botUsername'),
      },
      15
    )
    .registerSetting(
      {
        setting: 'nodeloc-telegram.botToken',
        type: 'text',
        label: app.translator.trans('nodeloc-telegram.admin.settings.field.botToken'),
      },
      15
    )
    .registerSetting({
      setting: 'nodeloc-telegram.enableWidget',
      type: 'boolean',
      label: app.translator.trans('nodeloc-telegram.admin.settings.field.enableWidget'),
    })
    .registerSetting({
      setting: 'nodeloc-telegram.widgetSize',
      type: 'select',
      label: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetSize'),
      options: {
        large: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetSizeLarge'),
        medium: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetSizeMedium'),
        small: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetSizeSmall'),
      },
      default: 'large',
    })
    .registerSetting({
      setting: 'nodeloc-telegram.widgetRadius',
      type: 'number',
      label: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetRadius'),
      min: 0,
      max: 20,
    })
    .registerSetting({
      setting: 'nodeloc-telegram.widgetUserPic',
      type: 'boolean',
      label: app.translator.trans('nodeloc-telegram.admin.settings.field.widgetUserPic'),
    })
    .registerSetting({
      setting: 'nodeloc-telegram.enableNotifications',
      type: 'boolean',
      label: app.translator.trans('nodeloc-telegram.admin.settings.field.enableNotifications'),
    });
});
