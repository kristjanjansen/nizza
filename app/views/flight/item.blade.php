<td>
<h2>@if (isset($item->field->carrier)) {{ $item->field->carrier->title }} @endif</h2>
</td>
<td><h2>{{ HTML::linkAction('FlightController@show', $item->title, array($item->id)) }}</h2>

 {{ $item->created_at }}
  
</td>