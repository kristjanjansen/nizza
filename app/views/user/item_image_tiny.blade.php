@if ($user && $user->image_path)
<img src="{{ asset('/files/pictures/' . $user->image_path) }}" width="30" />
@else
<img src="{{ asset('/files/pictures/picture_none.png') }}" width="30" />
@endif