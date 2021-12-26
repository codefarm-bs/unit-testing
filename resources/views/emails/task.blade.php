@component('mail::message')
    <div dir="rtl">
        <strong>وظیفه: </strong> {{ $task['title'] }}
    </div>
    <div dir="rtl">
        <strong>تاریخ پایان: </strong> {{ $task['ended_at'] }}
    </div>
@endcomponent
