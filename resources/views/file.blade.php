<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>#</title>
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/cover.css" rel="stylesheet">
    </head>
    <body class="text-center" style="box-shadow: none;">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column" style="min-width: 800px !important;">
      <header class="masthead mb-auto" style="margin-bottom: 0px !important;">
        <div class="inner">
          <nav class="nav nav-masthead justify-content-center">
            <a class="nav-link" href="/">Home</a>
          </nav>
        </div>
      </header>

      <main role="main" class="inner cover" style="text-align: left;">
        @if(!empty($entry))
        <h1 style="text-align: center;">Validator Results</h1>
        <h3>URL: {{$entry->url}}</h3>
        <h3>Slug: {{$entry->slug}}</h3>
        <h3>Status: {{$entry->status}}</h3>
        @if(!empty($entry->errors))
        <h3>Errors:</h3>
        <pre><code>@foreach(unserialize($entry->errors) as $error){{$error}}
@endforeach
        </code></pre>
        @endif
        <h3>Contents:</h3>
        <pre><code>{{$entry->contents}}</code></pre>
            <form method="post" action="/files">
                @csrf
                <input class="form-control" type="hidden" name="url" value="{{$entry->url}}" required="required">
                <input type="submit" class="btn btn-lg btn-primary" value="Re-Run Validation"/>
            </form>
        @else
        Invalid URL
        @endif
      </main>

      <footer class="mastfoot mt-auto">
        <div class="inner">
          <p>Cover template for <a href="https://getbootstrap.com/">Bootstrap</a>, by <a href="https://twitter.com/mdo">@mdo</a>.</p>
        </div>
      </footer>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/js/jquery-slim.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  </body>
</html>
