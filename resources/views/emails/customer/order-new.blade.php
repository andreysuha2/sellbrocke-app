<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Your order details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            Attached is the shipping label for the following order:
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Device</th>
                    <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Quote</th>
                    <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Quantity</th>
                    <th align="left" style="color: #606060; border-bottom: 1px solid #606060;">Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->devices as $item)
                    <tr>
                        <td align="left" style="color: #7a7a7a">{{$item->device->name}}</td>
                        <td align="left" style="color: #7a7a7a">${{$item->discountedPrice}}</td>
                        <td align="left" style="color: #7a7a7a">1</td>
                        <td align="left" style="color: #7a7a7a">${{$item->discountedPrice}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 5px 20px; text-align: right">
            <strong>Total: ${{$order->prices['discounted']}}</strong>
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            Please include the order summary/packing slip in the box with your laptop.
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            To ensure that your device(s) arrive safely to our facility, we recommend 2+" of bubble wrap around
            the device(s) and securely taping the box. Please ensure that the shipping label is also securely
            taped to the box and clearly visible.
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFFFFF" style="padding: 10px 20px">
            If you have any questions, please contact us!
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