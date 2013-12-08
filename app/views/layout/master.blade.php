<!doctype html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <title>@if ($title) {{ $title }} @endif</title>
    <style>
    body {
      font-family: sans-serif; 
      line-height: 1.4em; 
      max-width: 700px; 
      margin: 0 auto;
      padding: 2em;
    }
    hr {
      border: 0;
      border-top: 1px solid #aaa;
      height: 1px;
      color: black;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    td {
      vertical-align: top;
      padding: 0 0.5em 0 0;
    }
    .grid > div {
      display: inline-block;
      width: 145px;
      margin-right: 1em;
    }
    </style>
</head>
<body class="{{ strtolower(explode(' ', $title)[0]) }}">
  @include('layout.menu')
  <h1>{{ $title }}</h1>
  {{ $content }}
</body>
</html>
