@foreach($items as $item)
<hr />
{{ $item }}
@endforeach

@if (isset($pager))
{{ $pager }}
@endif