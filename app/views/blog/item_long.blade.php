<td>
  @include('user.item_image_medium')->with('user', $item->user)'
  
  {{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }}
  
</td>
<td>

<h2>{{ HTML::linkAction('BlogController@show', $item->title, array($item->id)) }}</h2>

{{ substr(strip_tags($item->body), 0, 500) }}

<br />
@foreach($item->destinations as $destination)
{{ $destination->title }}, 
@endforeach

</td>
