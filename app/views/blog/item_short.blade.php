<td colspan="2">

↗ @if ($item->field) {{ HTML::link($item->url, $item->title) }} @endif

by {{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} 
{{ strip_tags($item->body) }}
<br />
@foreach($item->destinations as $destination)
{{ $destination->title }}, 
@endforeach

</td>