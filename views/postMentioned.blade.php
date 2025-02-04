{!! $translator->trans('nikovonlas-tg-notify.notifications.post_mentioned', [
'{display_name}' => $blueprint->reply->user->display_name,
'{post_number}' => $blueprint->post->number,
'{title}' => $blueprint->post->discussion->title,
'{url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->reply->discussion_id, 'near' => $blueprint->reply->number]),
]) !!}
