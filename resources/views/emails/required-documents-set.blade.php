@component('mail::message')

# Required Documents Updated

Dear {{ $application->full_name }},

The required documents for your application (#{{ $application->application_number }}) have been updated.

{{ $message }}

Please upload the required documents at your earliest convenience.

@component('mail::button', ['url' => $actionUrl])
Upload Documents
@endcomponent

Thank you,
{{ config('app.name') }} Team

@endcomponent