{{-- @todo convert to model scope --}}
Good:
{{ count($flags->filter(function($flag) { return $flag['flag_type'] == 'good'; })) }}

Bad:
{{ count($flags->filter(function($flag) { return $flag['flag_type'] == 'bad'; })) }}
