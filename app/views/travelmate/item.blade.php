<td>
  
  @include('user.item_image_medium')->with('user', $item->user)

</td>
<td>

<h2>{{ HTML::linkAction('TravelmateController@show', $item->title, array($item->id)) }}</h2>

{{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} 

at {{ $item->created_at }} 

@foreach($item->destinations as $destination)
{{ HTML::linkAction('DestinationController@show', $destination->title, array($destination->id)) }}, 
@endforeach

<br />

{{ substr($item->body, 0, 150) }}




</td>