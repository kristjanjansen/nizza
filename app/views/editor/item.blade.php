<td> 
  @include('user.item_image_small')->with('user', $item->user)
</td>
<td>

<h2>{{ HTML::linkAction('EditorController@show', $item->title, array($item->id)) }}</h2>

@if ($item->user)
{{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} at {{ $item->created_at }}
@endif

</td>


