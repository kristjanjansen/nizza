<td> 
  @include('user.item_image_small')->with('user', $item->user)
</td>
<td>

<h2>{{ HTML::linkAction('ForumController@show', $item->title, array($item->id)) }}</h2>

@if ($item->user)
{{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} at {{ $item->created_at }}
@endif

@foreach($item->destinations as $destination)
{{ $destination->title }} ({{ $destination->id }}), 
@endforeach
<br />
@foreach($item->topics as $topic)
{{ $topic->title }} ({{ $topic->id }}), 
@endforeach
<br />
@include('flag.item')->with('flags', $item->flags)

</td>


