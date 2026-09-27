@if (session('status'))
    @php
        Flux::toast(
            variant: session('status.variant', 'success'),
            text: session('status.message') ?? session('status')
        );
    @endphp
@endif