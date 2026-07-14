@php
    $manager = app(\App\Services\BaseColorGuestManager::class);
@endphp
<style>
    :root {
        @foreach ($manager->cssVariables() as $var => $value)
            {{ $var }}: {{ $value }};
        @endforeach
    }
</style>
