<table>
<tr>
  <td>
@include('user.item_image_medium')->with('user', $item->user) 
  </td>
  <td>
{{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} at {{ $item->created_at }}
<br />
{{ $item->body }}
</td>
</tr>
</table>
