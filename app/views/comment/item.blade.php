<td class="comment" id="comment-{{ $comment->id }}">
@include('user.item_image_small')->with('user', $comment->user)
  </td>
  <td>
{{ HTML::linkAction('UserController@show', $comment->user->name, array($comment->user->id)) }} at {{ $comment->created_at}} 
<br />
{{ nl2br($comment->body) }}
<br />
@include('flag.item')->with('flags', $comment->flags)
</td>

