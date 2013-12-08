<table>
@foreach($items as $item)
<tr><td colspan="10"><hr /></td></tr>
<tr>
{{ $item }}
</tr>
@endforeach
</table>

@if (isset($pager))
{{ $pager }}
@endif