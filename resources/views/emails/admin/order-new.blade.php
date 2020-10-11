<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Test Mail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            New order from: {{$order->customer->first_name}} {{$order->customer->last_name}}
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <a href="{{env('DASHBOARD_URL') . "/orders/{$order->id}"}}">View order</a>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <div style="margin-bottom: 10px"><strong>Devices:</strong></div>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Name</th>
                        <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Condition</th>
                        <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->devices as $item)
                        <tr>
                            <td align="left" style="color: #7a7a7a">{{$item->device->name}}</td>
                            <td align="left" style="color: #7a7a7a">{{$item->condition->label}}</td>
                            <td align="left" style="color: #7a7a7a">${{$item->discountedPrice}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFA483" style="padding: 10px 20px">
            <strong>Total: ${{$order->prices['discounted']}}</strong>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            Thanks!
            <br/>
            - SellLaptopBack.com
            <br/>
            <br/>
            @env(['local', 'testing'])
                <small>Environment: {{env('APP_ENV')}}</small>
            @endenv
        </td>
    </tr>
</table>
</body>
</html>