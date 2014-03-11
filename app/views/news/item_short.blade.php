 ↗ {{ HTML::link($item->url, $item->title) }} at {{ $item->created_at }}
 <br />
 @foreach($item->destinations as $destination)
 {{ $destination->title }}, 
 @endforeach
