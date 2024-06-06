@if (session('success'))
    @php
        alert()->success('message', session('success'));
    @endphp
@endif
@if (session('error'))
    @php
        alert()->warning('message', session('error'));
    @endphp
@endif
