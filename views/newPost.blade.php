{!! $translator->trans('nikovonlas-tg-notify.notifications.newPost', [
'{display_name}' => $blueprint->post->user->display_name,
'{title}' => $blueprint->post->discussion->title,
'{url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->post->discussion_id, 'near' => $blueprint->post->number]),
]) !!}
