<table border="0" cellpadding="1" cellspacing="1" style="height:208px; width:728px">
    <tbody>
        <tr>
            <td><img alt="" src="https://fms-net.org/wp-content/uploads/2018/06/Header-Home.jpg" style="height:142px; width:720px" /></td>
        </tr>
        <tr>
            <td style="padding:10px;font-size:12px;font-family:arial,verdana,tahoma;color:#444;line-height:180%">
                <h2 style="padding:10px 0;font-size:24px;color:#29347b">Signing up completed!</h2>
                Dear {{ $dataUser['name'] }},
                <table style="margin:20px 15px" width="650" cellspacing="1" cellpadding="10" border="0" bgcolor="#cccccc">
                    <tbody><tr>
                            <td width="15%" bgcolor="#e7e7e7"><strong>ID(Username)</strong></td>
                            <td width="40%" bgcolor="#ffffff">{{ $dataUser['email'] }}</td>
                            <td width="15%" bgcolor="#e7e7e7"><strong>Password</strong></td>
                            <td width="30%" bgcolor="#ffffff">{{ $dataUser['password'] }}</td>
                        </tr>
                    </tbody></table>
                <br><br>
                Thank you for signing up for the Website of <strong>ICFMS {{ date('Y') }}</strong>.<br>
                <br>
                Please remember your ID(Email) and password, which you can see above.<br>
                You can check the status of your paper submission, registration, and personal information by logging in with the ID and password you registered.<br>
                <br>
                We thank you very much for your participation in ICFMS 2020 and hope you visit our official website <a href="{{ URL::to('/') }}">{{ URL::to('/') }}</a> constantly for updated information.<br>
                Should you have further inquiries, please feel free to contact the secretariat below. <br>
                <br>
                We look forward to meeting you in Indonesia. <br>
                <br>
                Best regards,<br>
                <br>
                <br>
                <strong>ICFMS {{ date('Y') }} Organizing Committee</strong><br>
                <br>
            </td>
        </tr>
    </tbody>
</table>
