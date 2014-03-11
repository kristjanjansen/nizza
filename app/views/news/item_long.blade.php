<img src="{{ $item->image }}" width="700"/>

<h2>{{ HTML::linkAction('NewsController@show', $item->title, array($item->id)) }}</h2>  
<div>
{{ substr(strip_tags($item->body), 0, 300) }}... at {{ $item->created_at }}
</div>
<br />
@foreach($item->destinations as $destination)
{{ $destination->title }}, 
@endforeach

