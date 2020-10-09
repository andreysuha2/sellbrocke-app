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
            Hello, {{$customer->first_name}}!
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            You are registered at the Sellbroke.com
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <strong>Your registration data</strong><br/>
            First Name: {{$customer->first_name}}<br/>
            Last Name: {{$customer->last_name}}<br/>
            E-Mail: {{$customer->email}}<br/>
            Username: {{$customer->username}}<br/>
            Password: {{$customer->password}}<br/>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 10px 20px">
            <strong>You can change your registration information in your profile at the Sellbroke.com</strong>
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