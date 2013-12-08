<td colspan="2">

↗ @if ($item->field) {{ HTML::link($item->field->url, $item->title) }} @endif

by {{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }} 
{{ strip_tags($item->body) }}
{{-- substr(strip_tags($item->body), 0, 100) --}}

</td>