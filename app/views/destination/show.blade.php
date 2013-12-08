@foreach($forums as $forum)
    <h3>{{ HTML::linkAction('ForumController@show', $forum->title, array($forum->id)) }}</h3>
@endforeach