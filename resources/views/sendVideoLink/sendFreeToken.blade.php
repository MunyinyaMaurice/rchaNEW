@component('mail::message')
# Welcome 
# Here is Your Free Token valid for 24 hours from now

@component('mail::button', ['url' => url("/api/auth/videoView/{$FreeToken}")])
Click here to watch
@endcomponent

@component('mail::panel')
This is panel
@endcomponent

Thanks,<br>
RCHA
@endcomponent
