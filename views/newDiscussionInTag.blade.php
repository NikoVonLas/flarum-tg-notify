{!! $translator->trans('nikovonlas-tg-notify.notifications.newDiscussionInTag', [
    'display_name' => $blueprint->discussion->user->display_name,
    'title' => $blueprint->discussion->title,
    'url' => $url->to('forum')->route('discussion', ['id' => $blueprint->discussion->id]),
]) !!}
