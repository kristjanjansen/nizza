<table>
  <tr>
<td>
  @include('user.item_image_small')->with('user', $item->user) 
  </td>
  <td>
{{-- HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) --}} at {{ $item->created_at }}
<br />
{{ $item->body }}
<br />
@foreach($item->topics as $topic)
{{ $topic->title }}, 
@endforeach
<br />
@include('flag.item')->with('flags', $item->flags)
</td>
</tr>
</table>