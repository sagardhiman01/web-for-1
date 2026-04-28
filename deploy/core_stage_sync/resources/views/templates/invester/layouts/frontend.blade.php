@extends($activeTemplate . 'layouts.app')
@section('panel')
  

    @yield('content')

    @php
        $content = getContent('footer.content',true);
    @endphp
  
@endsection
