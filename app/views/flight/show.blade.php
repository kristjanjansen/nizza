{{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} at {{ $item->created_at }}
<br />
{{ nl2br($item->body) }}
<br />
@foreach($item->destinations as $destination)
{{ $destination->title }}, 
@endforeach
