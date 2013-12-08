@foreach($item->destinations as $destination)
 {{ $destination->title }}, 
@endforeach

<br />
<img src="{{ asset('/files/images/' . $item->field->filename) }}" width="100%"/>
