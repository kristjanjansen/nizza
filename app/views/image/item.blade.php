<div>  

<img src="{{ asset('/files/images/' . $item->filename) }}" />


<h3>{{ HTML::linkAction('ImageController@show', $item->title, array($item->id)) }}</h3>
@foreach($item->destinations as $destination)
{{ $destination->title }}, 
@endforeach 
</div>