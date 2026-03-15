@component('mail::message')
# {{ $subject }}

{!! nl2br($body_message) !!}

@isset($action)

@component('mail::button', ['url' => $action['action_url']])
{{ $action['action_label'] }}
@endcomponent

@endisset
 
Thanks,<br>
{{ config('app.name') }}
@endcomponent
