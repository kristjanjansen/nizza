<div class="grid">
@foreach($items as $item)
{{ $item }}
@endforeach
</div>

@if (isset($pager))
{{ $pager }}
@endif