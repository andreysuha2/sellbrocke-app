<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Test Mail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="margin-top: 15px; margin-bottom: 15px;">
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            Hello, {{$order->customer->first_name}}!
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            Your check in the amount of <strong>${{$order->prices['discounted']}}</strong> was sent to the following address:
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <strong>{{$order->customer->first_name}} {{$order->customer->last_name}}</strong><br/>
            <strong>{{$order->customer->address}}</strong><br/>
            <strong>{{$order->customer->city}}, {{$order->customer->state}} {{$order->customer->zip}}</strong>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            Checks through the postal mail can take up to 7-10 business days to be delivered. If you still don't see your check after 10 business days, please contact us.
            <div style="margin-top: 10px; margin-bottom: 20px;">
                <a href="https://www.facebook.com/SellLaptopBack/" target="_blank" rel="noopener"><img src="https://selllaptopback.com/wp-content/uploads/2016/03/like_SellBroke_on_facebook.png" alt="Like SellBroke on Facebook!" /></a>
            </div>
            <div>
                <a href="https://www.facebook.com/SellLaptopBack/" target="_blank" rel="noopener">Like us on Facebook</a> and tell others about your experience with <a href="https://selllaptopback.com" target="_blank" rel="noopener">SellLaptopBack.com</a>!
            </div>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            <a href="https://www.google.com/search?q=sellLaptopBack" target="_blank" rel="noopener">Leave us a review on Google!</a>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            Ready to sell us another device? Head to SellLaptopBack.com and get a quote today!
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            Thanks!
            <br/>
            - SellLaptopBack.com
            <br/>
            <br/>
            @env(['local', 'staging'])
                <small>Environment: {{env('APP_ENV')}}</small>
            @endenv
        </td>
    </tr>
</table>
</body>
</html>