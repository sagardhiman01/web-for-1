@extends($activeTemplate.'layouts.app')
@section('panel')
 

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <!-- Font Awesome  css-->
    <link href="{{asset ('core/css/static/fontawesomefree/css/fontawesome.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset ('core/css/static/fontawesomefree/css/brands.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset ('core/css/static/fontawesomefree/css/solid.css')}}" rel="stylesheet" type="text/css">

    <!-- Custom style  -->
    <!-- <link rel="stylesheet" href="/static/css/responsives.css"> -->
    <link rel="stylesheet" href="{{asset ('core/styles.css')}}">
    <link rel="stylesheet" href="{{asset ('core/snackbar.css')}}">


    <!-- style.css -->
    <style>
        @import url('https://fonts.googleapis.com/css?family=Rajdhani:300,400,500,600,700');
        @import url('https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');
        @import url('https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');
        @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,700i,800,800i&display=swap');
        @import url("/static/css/owl.carousel.min.css");

        body {
            /* background-color: #FFF18D; */
        }

        * {
            font-family: Montserrat;
        }

        .bottom-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: whitesmoke;
            /* color: #333; */
            padding: 10px;
            border-radius: 15px 15px 0px 0px;
        }

        .scrollable-div {
            max-height: 40vh;
            overflow-y: auto;
        }

        .badge-notification {
            position: relative;
            top: -8px;
            left: -47px;
            border: 1px solid black;
            border-radius: 50%;
            font-size: 9px;
        }

        /* Styles for the preloader */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            color: black
        }

        /* Styles for the blurred background */
        #blurred-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(2px);
            z-index: 9998;
        }


        nav {
            position: fixed;
            bottom: 0;
            width: 100%;

            margin: 0 auto;
            left: 0;
            right: 0;
        }

        .nav-box {
            display: flex;
            padding: 8px;
            background-color: #9400FF;
            border-radius: 10px 10px 0px 0px;
        }

        .nav-container {
            display: flex;
            width: 100%;
            list-style: none;
            justify-content: space-around;
        }

        .nav__item {
            display: flex;
            position: relative;
            padding: 11px 0px 4px
        }
.nav__item.active{
    border-bottom: 4px solid white;
}

        .nav__item-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #FFFFFF;
            text-decoration: none;
        }

        .nav__item-icon {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6em;
            /* background-color: #fff; */
            border-radius: 50%;
            height: 20px;
            width: 20px;
            transition: margin-top 250ms ease-in-out, box-shadow 250ms ease-in-out;
        }

        .nav__item-text {
            position: absolute;
            bottom: 0;
            transition: transform 250ms ease-in-out;
            color: #FFFFFF;
            font-size: 8px;
            font-weight: 700;
            font-family: Montserrat;
        }

        .nav__item:hover .nav__item-icon:hover,
        .nav__item-text:hover,
        .nav__item-link:hover {
            color: #FFFFFF;
        }
    </style>

    <style>
        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: rgba(31, 28, 28, 0.5);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            border-radius: 10px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 50%;
            font-size: 17px;
        }

        #snackbar.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        #snackbar_error {
            visibility: hidden;
            width: 40%;
            margin-left: 5px;
            background-color: rgba(31, 28, 28, 0.5);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            border-radius: 10px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 30%;
            bottom: 50%;
            font-size: 17px;
        }

        #snackbar_error.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @-webkit-keyframes fadein {
            from {
                bottom: 50%;
                opacity: 0;
            }

            to {
                bottom: 50%;
                opacity: 1;
            }
        }

        @keyframes fadeout {
            from {
                bottom: 50%;
                opacity: 1;
            }

            to {
                bottom: 50%;
                opacity: 0;
            }
        }

        @media (min-width: 576px) and (max-width: 767px) {
            #snackbar_error {
                width: 60%;
                left: 20%;
                bottom: 70px;
                font-size: 12px;
            }

            #snackbar {
                width: 50%;
                left: 25%;
                bottom: 70px;
                font-size: 12px;
            }
        }

        @media (max-width: 575.99px) {
            #snackbar_error {
                width: 80%;
                margin-left: 5px;
                left: 10%;
                font-size: 12px;
            }

            #snackbar {
                width: 80%;
                margin-left: 5px;
                left: 10%;
                font-size: 12px;
            }
        }
    </style>

    <script>
        document.onreadystatechange = function () {
            var state = document.readyState;
            if (state == "interactive") {
                // Show the preloader when the page starts loading
                showPreloader();
            } else if (state == "complete") {
                // Hide the preloader when the page finishes loading
                hidePreloader();
            }
        };

        function showPreloader() {
            document.getElementById("preloader").style.display = "flex";
            document.getElementById("blurred-background").style.display = "block";
        }

        function hidePreloader() {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("blurred-background").style.display = "none";
        }

    </script>

    <title>
         Home
    </title>
    <script>
        document.onreadystatechange = function () {
            var state = document.readyState;
            if (state == "interactive") {
                // Show the preloader when the page starts loading
                showPreloader();
            } else if (state == "complete") {
                // Hide the preloader when the page finishes loading
                hidePreloader();
            }
        };

        function showPreloader() {
            document.getElementById("preloader").style.display = "flex";
            document.getElementById("blurred-background").style.display = "block";
        }

        function hidePreloader() {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("blurred-background").style.display = "none";
        }

    </script>
</head>

<body class="main-layout m-0 p-0">
    <!-- loader  -->
    <div id="preloader">
        <i class="fa-regular fa-loader fa-flip fa-10x"></i>
        <div class="loader"><img src="{{asset ('core/img/preview2.gif')}}" alt="" style="height: 80; width: 80px;">
        </div>
    </div>
    <!-- end loader -->

    <!-- end header -->
<style>
    * {
        font-family: Montserrat;
    }

    .aboutus_text {
        font-size: 14px;
    }

    .home-banner-text {
        font-size: 14px;
        font-weight: 700;
        color: #2502F9;
    }

    .home-banner-text-sub {
        font-size: 10px;
        font-weight: 400;
        margin-top: 2px;
    }

    .bluid h2 {
        font-family: Montserrat;
        ;
        /* font: bold; */
        font-weight: 600;
    }

    .aboutus_text {
        font-size: 16px;
    }

    @media screen and (max-width: 350px) {
        .invest_card_font {
            font-size: 11px;
        }
    }

    @media screen and (max-width: 768px) {
        .aboutus_text {
            font-size: 11px;
        }

        .home-banner {
            margin-top: 5%
        }

    }

    @media screen and (min-width: 768px) {
        .container {
            max-width: 750px;
        }
    }

    @media screen and (min-width: 992px) {
        .container {
            max-width: 970px;
        }
    }

    @media screen and (min-width: 1200px) {
        .container {
            max-width: 1170px;
        }
    }

    .header-card {
        background-image: url("../../static/images/background.png");
        border-radius: 20px;
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        width: 100%;
        /* border:2px solid #7070704a; */
    }

    .card {
        background-color: transparent;
        border-radius: 5px;
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        width: 100%;
        border: none;
    }

    .home-button {
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 30px;
        font-size: 16px;
    }

    .line {
        border-top: 1px solid #ddd;
        margin: 20px 0;
    }

    .totals {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .vertical-line {
        border-left: 1px solid #ddd;
        height: 40px;
        margin: 0 10px;
        width: 1px;
    }

    .referal-link {
        background-color: white;
        line-height: 1.5;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        border-radius: 40px;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    }

    .copy-button {
        background-color: white;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px;
        cursor: pointer;
    }

    .account-card,
    .service-card,
    .about-card,
    .logout-card {
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-height: 60px;
        padding: 15px;
        background-color: white;
        text-decoration: none;
        color: #000;
        /* border-bottom: 1px solid grey; */

    }

    .card_hover:hover {
        border-radius: 15px;
        color: blue;
    }

    .card-text {
        margin: 0 10px;
        flex-grow: 1;
        font-size: 18px;
    }

    .balance-amount {
        color: #FF6F61;
        font-size: 16px;
    }

    .home-page-heading {
        color: white;
        font-size: 16px;
    }

    .home-page-heading-white {
        color: white;
        font-size: 11.43px;
        font-weight: 700;
        line-height: 11px;
        margin-top: 10px;
    }

    .home-page-sub-heading-white {
        color: #000000;
        font-size: 14.12px;
        font-weight: 700;
        margin-top: 5px;
    }

    .home-page-sub-heading {
        color: #FC4C4E;
        font-size: 14px;
    }

    .plan-card {
        width: 175px;
        height: 100px;

    }

    .plan-card {

        background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/linear-bg.png');
        /* Set the background image URL */
        background-size: cover;
        /* Scale the image to cover the entire div */
        position: relative;
        /* Needed for absolute positioning of text */
    }

    /* new styling */
    .main-heading {
        font-size: 20px;
        font-weight: 700;
        color: #FFFFFF;
    }


    .hex3 {
        background-image: linear-gradient(to right, #474444, #000000);
        -webkit-clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
        clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
    }

    .banner-container {
        position: relative;
        /* Create a positioning context for absolute positioning */
        margin: 0 auto;
        /* Center the container horizontally */
    }

    .reward-card {
        background: rgba(0, 0, 0, 0.16);
        border-radius: 16px;
    }

    .daily-reward {
        width: 72px;
        height: 56px;
        text-align: center;
        border-radius: 10px;
        background-color: rgba(228, 225, 225, 1);
        box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);

    }

    .daily-reward-active {
        background-color: rgba(58, 58, 58, 1);
        width: 72px;
        height: 56px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);
    }

    .daily-reward-next {
        width: 156px;
        height: 56px;
        text-align: center;
        border-radius: 10px;
        background-color: white;
        box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);
    }

    .reward-icon {
        width: 18px;
        height: 18px;
        float: right;
        margin-top: -10px;
    }

    .about-us-img {
        border: none;

    }

    .heading_label {
        border-radius: 0px 0px 40px 40px;
        background-color: #9400FF;
        height: 176px;
    }



    .text-overlay {
        position: absolute;
        top: 45%;
        /* Adjust the top position as needed */
        left: 35%;
        /* Adjust the left position as needed */
        transform: translate(-50%, -50%);
        /* Center the text horizontally and vertically */
        text-align: center;
        /* Center text content */
    }

    .invest-card {
        /* min-width: 125px; */
        height: 210px;
        box-shadow: 0px 0px 6px 0px #00000040;
        border-radius: 17px;
    }

    .invest-plan-text {
        font-size: 8px;
        font-weight: 400;
        color: #000000;
    }

    .extra-label {
        color: #9400FF;
        font-size: 18px;
        font-weight: 700;
        padding-left: 10px;
        margin: 8px;
    }

    .plan-name {
        font-size: 15.32px;
        font-weight: 700;
        color: #000000;
    }
    .popup-heading {
            font-size: 11.39px;
            font-weight: 400;
            color: #FFFFFF;
        }

        .popup-price {
            font-size: 11.39px;
            font-weight: 300;
            color: #FFFFFF;
        }
</style>






            @yield('content')


<style>
  * {
      font-family: Montserrat;
  }

  .bluid h2 {
      font-family: Montserrat;
      ;
      /* font: bold; */
      font-weight: 600;
  }

  @media screen and (max-width: 600px) {
      .img_responsive {
          width: 160px;
      }

  }

  .invest_card_font {
      font-size: 10px;
  }

  @media(max-width: 320px) {
      .invest_card_font {
          font-size: 10px;
      }
  }


  @media screen and (min-width: 768px) {
      .container {
          max-width: 750px;
      }
  }

  @media screen and (min-width: 992px) {
      .container {
          max-width: 970px;
      }
  }

  @media screen and (min-width: 1200px) {
      .container {
          max-width: 1170px;
      }
  }

  .header-card {
      background-image: url("../../static/images/background.png");
      border-radius: 20px;
      /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
      box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
      width: 100%;
      /* border:2px solid #7070704a; */
  }

  .card {
      background-color: transparent;
      border-radius: 5px;
      /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
      width: 100%;
      border: none;
  }

  .home-button {
      color: white;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
      border-radius: 30px;
      font-size: 16px;
  }

  .line {
      border-top: 1px solid #ddd;
      margin: 20px 0;
  }

  .totals {
      display: flex;
      justify-content: space-between;
      align-items: center;
  }

  .vertical-line {
      border-left: 1px solid #ddd;
      height: 40px;
      margin: 0 10px;
      width: 1px;
  }

  .referal-link {
      background-color: white;
      line-height: 1.5;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      border-radius: 40px;
      box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
  }

  .copy-button {
      background-color: white;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px;
      cursor: pointer;
  }

  .account-card,
  .service-card,
  .about-card,
  .logout-card {
      box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      max-height: 60px;
      padding: 15px;
      background-color: white;
      text-decoration: none;
      color: #000;
      /* border-bottom: 1px solid grey; */

  }

  .card_hover:hover {
      border-radius: 15px;
      color: blue;
  }

  .card-text {
      margin: 0 10px;
      flex-grow: 1;
      font-size: 18px;
  }

  .balance-amount {
      color: #FF6F61;
      font-size: 16px;
  }

  .home-page-heading {
      color: white;
      font-size: 16px;
  }


  .home-page-sub-heading {
      color: #FC4C4E;
      font-size: 14px;
  }


  /* new styling */



  .hex3 {
      background-image: linear-gradient(to right, #474444, #000000);
      -webkit-clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
      clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
  }

  .banner-container {
      position: relative;
      /* Create a positioning context for absolute positioning */
      margin: 0 auto;
      /* Center the container horizontally */
  }

  .reward-card {
      background: rgba(0, 0, 0, 0.16);
      border-radius: 16px;
  }

  .daily-reward {
      width: 72px;
      height: 56px;
      text-align: center;
      border-radius: 10px;
      background-color: rgba(228, 225, 225, 1);
      box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);

  }

  .daily-reward-active {
      background-color: rgba(58, 58, 58, 1);
      width: 72px;
      height: 56px;
      text-align: center;
      border-radius: 10px;
      box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);
  }

  .daily-reward-next {
      width: 156px;
      height: 56px;
      text-align: center;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.25);
  }

  .reward-icon {
      width: 18px;
      height: 18px;
      float: right;
      margin-top: -10px;
  }

</style>
<body>
    <div class="navbar-bottom">
        <nav>
            <div class="nav-box p-0 m-0">
                <ul class="nav-container">
                    <li class="nav__item" id="home_div">
                        <a class="nav__item-link" href="{{route ('user.home')}}">
                            <div class="nav__item-icon">
                                <ion-icon><img src="{{asset ('core/img/home-nav.png')}}" style="height: 20px;" />
                                </ion-icon>
                            </div>
                            <span class="nav__item-text">Home</span>
                        </a>
                    </li>
                    <li class="nav__item " id="activeplan_div">
                        <a class="nav__item-link" href="{{route ('user.invest.log')}}">
                            <div class="nav__item-icon">
                                <ion-icon> <ion-icon><img src="{{asset ('core/img/device-nav.png')}}" style="height: 20px;" />
                                </ion-icon>                     
                               </div>
                            <span class="nav__item-text">Device</span>
                        </a>
                    </li>
                    <!-- <li class="nav__item active">
                        <a class="nav__item-link" href="/activePlan/">
                            <div class="nav__item-icon">
                                <ion-icon><img src="/static/images/new/nav-icon3.png" style="height: 20px;" />
                                </ion-icon>                      
                              </div>
                        </a>
                    </li> -->
                    <li class="nav__item" id="team_div">
                        <a class="nav__item-link" href="{{ route('user.referrals') }}">
                            <div class="nav__item-icon">
                                <ion-icon><img src="{{asset ('core/img/team-nav.png')}}" style="height: 20px;" />
                                </ion-icon>                        </div>
                            <span class="nav__item-text">Team</span>
                        </a>
                    </li>
                    <li class="nav__item" id="mine_div">
                        <a class="nav__item-link" href="{{ route('user.profile.setting') }}">
                            <div class="nav__item-icon">
                                <ion-icon><img src="{{asset ('core/img/mine-nav.png')}}" style="height: 20px;" />
                                </ion-icon>                        </div>
                            <span class="nav__item-text">Main</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</body>



@endsection
