<div>
  <h2>{{ HTML::linkAction('OfferController@show', $item->title, array($item->id)) }}</h2>  
  {{ HTML::linkAction('UserController@show', $item->user->name, array($item->user->id)) }}
</div>