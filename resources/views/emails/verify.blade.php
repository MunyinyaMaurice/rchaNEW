@component('mail::message')
# Welcome 
# Here is Your mail verification link is valid for 1 hours from now


{{-- {{ dd($id) }} --}}
@component('mail::button', ['url' => url("/verifyUserEmail/{$id}")])
Click here to verify
@endcomponent


Thanks,<br>
RCHA
@endcomponent
