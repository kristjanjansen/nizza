<td class="comment" id="comment-{{ $comment->id }}">
@include('user.item_image_tiny')->with('user', $comment->user)
</td>
<td>
{{ HTML::linkAction('UserController@show', $comment->user->name, array($comment->user->id)) }} {{ $comment->body }} at {{ $comment->created_at}}
</td>