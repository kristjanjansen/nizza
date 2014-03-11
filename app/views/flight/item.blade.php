<td>
<h2>@if (isset($item->carrier)) {{ $item->carrier->title }} @endif</h2>
</td>
<td><h2>{{ HTML::linkAction('FlightController@show', $item->title, array($item->id)) }}</h2>

 {{ $item->created_at }}
 <br />
 @foreach($item->destinations as $destination)
 {{ $destination->title }}, 
 @endforeach  
</td>