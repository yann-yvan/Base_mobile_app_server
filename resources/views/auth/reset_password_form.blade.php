<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Resend verification mail</title>


    <style>

        body {
            background-image: linear-gradient(120deg, rgb(209, 219, 233), rgb(240, 250, 243));
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, 'Helvetica Neue', Arial, sans-serif;
            height: auto;
        }

        .wrapper {
            width: 50%;
            max-width: 600px;
            margin: 20% auto;
        }

        .box {
            box-shadow: 0px 0px 10px #afc0d9;
            background-color: #fff;
            padding: 35px;
            border-radius: .25rem;
            text-align: center;
        }

        h1 {
            color: rgb(53, 114, 210);
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 6px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin-top: 10px;
            margin-bottom: 25px;
            font-size: 20px;
            font-weight: 300;
        }

        input {
            border: 1px solid rgb(220, 219, 235);
            border-radius: 4px;
            font-size: 13px;
            padding: 10px;
            color: #000;
            transition: all .15s ease-in;
        }

        input[type=email] {
            width: 60%;
        }

        input[type=submit] {
            background-color: rgb(53, 114, 210);
            color: #fff;
            font-weight: bold;
            border: 1px solid transparent;
        }

        input[type=submit]::focus {
            border: 1px solid #fff;
        }

        input:focus {
            border-color: rgb(53, 114, 210);
            box-shadow: 0px 0px 8px 2px rgba(53, 114, 210, .5);
            outline: none;
        }

        input::placeholder {
            color: #999;
        }

        #subscribe-result p {
            margin-top: 35px;
        }
    </style>


</head>

<body>


<div class="wrapper">
    <h1>Reset your password</h1>
    <div class="box">
        <form action={{url('api/password/reset')}}
                method="POST" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate">
            {{csrf_token()}}
            <input type="password" value="" name="password" class="email" id="mce-password"
                   placeholder="Password" required>
            <p>
                <input type="password" value="" name="password_confirmation" class="email" id="mce-password-confirm"
                       placeholder="Confirm" required>
            <p>
                <input type="hidden" name="token" value="{{$token}}">
                <input type="submit" value="Reset Password" name="subscribe" id="mc-embedded-subscribe"
                       class="mc-button">
            <div id="subscribe-result">
            </div>
        </form>
    </div>
</div>


<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>


<script>
    $(document).ready(function () {
        var $form = $('#mc-embedded-subscribe-form');
        if ($form.length > 0) {
            $('form input[type="submit"]').bind('click', function (event) {
                if (event) event.preventDefault();
                register($form)
            })
        }
    });

    function register($form) {
        $('#mc-embedded-subscribe').val('Resetting...');

        $.ajax({
            type: 'POST',
            url: '{{url('api/password/reset')}}',
            data: $('#mc-embedded-subscribe-form').serialize(),
            async: true,
            dataType: 'json',
            error: function (err) {
                alert('Could not connect to the registration server. Please try again later.')
            },
            success: function (data) {
                $('#mc-embedded-subscribe').val('Reset Password');

                if (data.status === 'success') {
                    // Yeahhhh Success
                    console.log(data.msg);
                    $('#mce-password').css('borderColor', '#ffffff');
                    $('#subscribe-result').css('color', 'rgb(53, 114, 210)');
                    $('#subscribe-result').html('<p>Thank you for. We have reset your password.</p>');
                    $('#mce-EMAIL').val('')
                } else {
                    // Something went wrong, do something to notify the user.
                    console.log(data.msg);
                    $('#mce-EMAIL').css('borderColor', '#ff8282');
                    $('#subscribe-result').css('color', '#ff8282');
                    $('#subscribe-result').html('<p>' + data.msg + '</p>')
                }
            }
        })
    }
</script>


</body>

</html>
