@if ($errors->any())
    @php
      alert()->warning('message', session('error'));
    @endphp
@endif
