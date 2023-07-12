<!DOCTYPE html>
<html>

<body>
    <h1>{{ $content['header'] }}</h1>
    <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>
    <p>{{ $content['message'] }}</p>
    <p>{{ $content['title'] }}</p>
    @if ($content['reason'])
        <p style="color: red">Note: {{ $content['reason'] }}</p>
    @endif
    @if (!$content['toAdmin'])
        <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
    @endif

    <p>Best regards,</p>
    <p>Dream Records</p>
</body>

</html>
