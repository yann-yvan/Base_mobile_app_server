<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily UI#011 | Flash Message (Error/Success)</title>


    <style>
        /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
        @import url("https://fonts.googleapis.com/css?family=Lato:400,700");

        html {
            display: grid;
            min-height: 100%;
        }

        body {
            display: grid;
            overflow: hidden;
            font-family: "Lato", sans-serif;
            text-transform: uppercase;
            text-align: center;
            background-size: cover;
            background: url("https://png.pngtree.com/element_origin_min_pic/16/06/30/175774e0aa9d91c.jpg") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
        }

        #container {
            position: relative;
            margin: auto;
            overflow: hidden;
            width: 700px;
            height: 250px;
        }

        h1 {
            font-size: 0.9em;
            font-weight: 100;
            letter-spacing: 3px;
            padding-top: 5px;
            color: #FCFCFC;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .green {
            color: #4ec07d;
        }

        .red {
            color: #e96075;
        }

        .alert {
            font-weight: 700;
            letter-spacing: 5px;
        }

        p {
            margin-top: -5px;
            font-size: 0.5em;
            font-weight: 100;
            color: #5e5e5e;
            letter-spacing: 1px;
        }

        button, .dot {
            cursor: pointer;
        }

        #success-box {
            position: absolute;
            width: 35%;
            height: 100%;
            margin: auto;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(to bottom right, #B0DB7D 40%, #99DBB4 100%);
            border-radius: 20px;
            box-shadow: 5px 5px 20px #cbcdd3;
            perspective: 40px;
        }

        #error-box {
            position: absolute;
            width: 35%;
            height: 100%;
            margin: auto;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(to bottom left, #EF8D9C 40%, #FFC39E 100%);
            border-radius: 20px;
            box-shadow: 5px 5px 20px #cbcdd3;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: #FCFCFC;
            border-radius: 50%;
            position: absolute;
            top: 4%;
            right: 6%;
        }

        .dot:hover {
            background: #c9c9c9;
        }

        .two {
            right: 12%;
            opacity: .5;
        }

        .face {
            position: absolute;
            width: 22%;
            height: 22%;
            background: #FCFCFC;
            border-radius: 50%;
            border: 1px solid #777777;
            top: 21%;
            left: 37.5%;
            z-index: 2;
            animation: bounce 1s ease-in infinite;
        }

        .face2 {
            position: absolute;
            width: 22%;
            height: 22%;
            background: #FCFCFC;
            border-radius: 50%;
            border: 1px solid #777777;
            top: 21%;
            left: 37.5%;
            z-index: 2;
            animation: roll 3s ease-in-out infinite;
        }

        .eye {
            position: absolute;
            width: 5px;
            height: 5px;
            background: #777777;
            border-radius: 50%;
            top: 40%;
            left: 20%;
        }

        .right {
            left: 68%;
        }

        .mouth {
            position: absolute;
            top: 43%;
            left: 41%;
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .happy {
            border: 2px solid;
            border-color: transparent #777777 #777777 transparent;
            transform: rotate(45deg);
        }

        .sad {
            top: 49%;
            border: 2px solid;
            border-color: #777777 transparent transparent #777777;
            transform: rotate(45deg);
        }

        .shadow {
            position: absolute;
            width: 21%;
            height: 3%;
            opacity: .5;
            background: #777777;
            left: 40%;
            top: 43%;
            border-radius: 50%;
            z-index: 1;
        }

        .scale {
            animation: scale 1s ease-in infinite;
        }

        .move {
            animation: move 3s ease-in-out infinite;
        }

        .message {
            position: absolute;
            width: 100%;
            text-align: center;
            height: 40%;
            top: 47%;
        }

        .button-box {
            position: absolute;
            background: #FCFCFC;
            width: 50%;
            height: 15%;
            border-radius: 20px;
            top: 73%;
            left: 25%;
            outline: 0;
            border: none;
            box-shadow: 2px 2px 10px rgba(119, 119, 119, 0.5);
            transition: all .5s ease-in-out;
        }

        .button-box:hover {
            background: #efefef;
            transform: scale(1.05);
            transition: all .3s ease-in-out;
        }

        @keyframes bounce {
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes scale {
            50% {
                transform: scale(0.9);
            }
        }

        @keyframes roll {
            0% {
                transform: rotate(0deg);
                left: 25%;
            }
            50% {
                left: 60%;
            }
            100% {
                transform: rotate(360deg);
                left: 25%;
            }
        }

        @keyframes move {
            0% {
                left: 25%;
            }
            50% {
                left: 60%;
            }
            100% {
                left: 25%;
            }
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

</head>

<body>

<div id="container">
    <div id="error-box">
        <div class="dot"></div>
        <div class="dot two"></div>
        <div class="face2">
            <div class="eye"></div>
            <div class="eye right"></div>
            <div class="mouth sad"></div>
        </div>
        <div class="shadow move"></div>
        <div class="message"><h1 class="alert">Error!</h1>
            <p>{{$message}}</div>
        @if($button)
            <button class="button-box"><h1 class="red">try again</h1></button>
        @endif
    </div>
</div>


</body>

</html>