@extends($activeTemplate.'layouts.master')
@section('content')

   
   
   <style>
    body {

        font-family: Montserrat;
    }

    #header-card {
        background: #3F3F3F;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        border-radius: 0px 0px 0px 47px;
    }

    .plans-label {
        font-size: 18px;
        font-weight: 700;
        color: white;
    }

    .banner-container {
        position: relative;
        /* Create a positioning context for absolute positioning */
        margin: 0 auto;
        /* Center the container horizontally */
    }

    .text-overlay-mine {
        position: absolute;
        left: 22%;
        top: 44px;
        transform: translate(-50%, -50%);
        padding: 10px;

    }

    .more-div-icon {
        width: 24px;
        height: 24px;
    }

    .heading_label {
        border-radius: 0px 0px 40px 40px;
        background-color: #9400FF;
        height: 220px;
    }

    .text-overlay {
        position: absolute;
        top: 66%;
        left: 34%;
        transform: translate(-50%, -50%);
    }

    .mine-page-heading-white {
        color: #000000;
        font-size: 11px;
        font-weight: 100;
        line-height: 11px;
        margin-top: 5px;
    }

    .mine-page-sub-heading-white {
        color: #000000;
        font-size: 10px;
        font-weight: 700;
        margin-top: 5px;
    }

    .plan-card {
        width: 175px;
        height: 100px;
        position: relative;
        background-size: cover;
    }
    .navigation-bar {
        box-shadow: 0px 0px 4px 0px #0000001A;
        border-radius: 10px;
        margin-top: 5px;
    }
</style>

<body>

    <!-- graf -->
    <div class="mx-auto" id="snackbar_error"></div>
    <div id="snackbar"></div>

    <div class="heading_label m-0 p-0 px-3 pt-2">
        <div class="d-flex justify-content-between ">
            <div class="d-flex">
                <div>
                    <img src="{{asset ('core/img/income-detail.png')}}" style="width: 67px;">
                </div>
<div class="align-self-center ms-1">
    <h1 style="font-family: Istok Web; font-size: 15px; font-weight: 400; color: #FFFFFF; text-align: left;">Id:Code</h1>
    <h1 style="font-family: Istok Web; font-size: 14px; font-weight: 700; color: #FFFFFF; text-align: left;">{{ auth()->user()->username }}</h1>
</div>
            </div>
            
            <div data-bs-toggle="modal" data-bs-target="#exampleModal">
                <img src="{{asset ('core/img/home-checkin.png')}}" class="m-3" style="width: 56px;">
            </div>
            
        </div>

        <div class="row m-0 p-0 mt-4">
            <div class="col-3">
                <a href="{{route ('user.deposit.index')}}">
                    <div class="wallet_box text_align_center">
                        <img src="{{asset ('core/img/mine-recharge.png')}}" style="width: 54px;">
                        <h4 style="color: #FFFFFF; font-size: 10px; font-weight: 700;
                                    ">Recharge</h4>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{route ('user.withdraw')}}">
                    <div class="wallet_box text_align_center">
                        <img src="{{asset ('core/img/mine-withdraw.png')}}" style="width: 54px;">
                        <h4 style="color: #FFFFFF; font-size: 10px; font-weight: 700;
                                    ">Withdraw</h4>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('user.invest.log') }}">
                    <div class="wallet_box text_align_center">
                        <img src="{{asset ('core/img/mine-account.png')}}" style=" width: 54px;">
                        <h4 style="color: #FFFFFF; font-size: 10px; font-weight: 700;">
                         Earn
                        </h4>
                    </div>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('user.referrals') }}">
                    <div class="wallet_box text_align_center">
                        <img src="{{asset ('core/img/mine-task.png')}}" style=" width: 54px;">
                        <h4 style="color: #FFFFFF; font-size: 10px; font-weight: 700;
                                    ">Team</h4>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    
    
@php
    use App\Models\Transaction;
    $authUserId = Auth::id();
    $referralCommission = Transaction::where('user_id', $authUserId)
                                    ->where('remark', 'referral_commission')
                                    ->sum('amount');
    $formattedReferralCommission = number_format($referralCommission, 0, '.', '');
@endphp
    @php
  $authUser = Auth::user();
  $userCount = App\Models\User::where('ref_by', $authUser->id)->count();
@endphp
    
    <div style="margin-top: -30px;">
        <div class="d-flex justify-content-evenly p-0 m-0">
            <div class="plan-card" style="background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/mine-one.png');">
                <div class="text-overlay">
                    <h5 class="mine-page-sub-heading-white">Account balance</h5>
                    <h5 class="mine-page-heading-white">
                        {{ $general->cur_text }} <span id="dailyreward_amount">
                            {{ showAmount(auth()->user()->interest_wallet) }}
                       </span>
                    </h5>
                </div>
            </div>
            <div class=" plan-card " style="background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/mine-two.png');">
                <div class="text-overlay pt-1">
                    <h5 class="mine-page-sub-heading-white">Team income</h5>
                    <h5 class="mine-page-heading-white">
                      {{ $general->cur_text }} {{ $referralCommission }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-evenly p-0 m-0">
            <div class="plan-card" style="background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/mine-three.png');">
                <div class="text-overlay pt-2">
                    <h5 class="mine-page-sub-heading-white">Total Recharge</h5>
                    <h5 class="mine-page-heading-white">{{ $general->cur_text }} {{ showAmount(auth()->user()->deposit_wallet) }}</h5>
                </div>
            </div>
            <div class=" plan-card " style="background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/mine-four.png');">
                <div class="text-overlay pt-2">
                    <h5 class="mine-page-sub-heading-white">Total People</h5>
                    <h5 class="mine-page-heading-white">
                         {{ $userCount }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3 mx-3">

            <a href="{{ route('user.change.password') }}">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/withdraw-password.png')}}" style="width: 22px"> 
                    <span style="font-size: 12px; font-weight: 400; color: #000000;margin-left: 7px; ">
                           Change Password
                    </span>
                </div>
                <div>
           
                </div>
            </div>
        </a>

            <a href="{{route ('user.invest.log')}}">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/income-record.png')}}" style="width: 18px">
                     <span style="font-size: 12px; font-weight: 400; color: #000000; margin-left: 7px;">
                        Income Record
                    </span>
                </div>
                <div>
         
                </div>
            </div>
        </a>
        <a href="{{route ('user.deposit.history')}}">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/recharge-record.png')}}" style="width: 16px">
                     <span style="font-size: 12px; font-weight: 400; color: #000000;margin-left: 7px;">
                        Recharge Record
                    </span>
                </div>
                <div>

            </div>
        </div>
    </a>
    <a href="{{route ('user.withdraw.history')}}">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/withdraw-record.png')}}" style="width: 22px"> 
                    <span style="font-size: 12px; font-weight: 400; color: #000000;margin-left: 7px;">
                        Withdraw Record
                    </span>
                </div>
                <div>
                  
                </div>
            </div>
        </a>
        <a href="{{ route('ticket.index') }}">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/custmer-service.png')}}" style="width: 22px"> 
                    <span style="font-size: 12px; font-weight: 400; color: #000000; margin-left: 7px; ">
                        Customer Service
                    </span>
                </div>
                <div>
        
                </div>
            </div>
        </a>

        <a href = "" Download = "" aria-disabled="true">
        <div class="d-flex justify-content-between navigation-bar p-2">
            <div>
                    <img src="{{asset ('core/img/download-APK.png')}}" style="width: 22px"> 
                    <span style="font-size: 12px; font-weight: 400; color: #000000;margin-left: 7px; ">
                           Download APK
                        </span>
                    </div>
                    <div>

                    </div>
                </div>
            </a>
    </div>
    <div class="text-center m-2 mb-4">
        <a href="{{route ('user.logout')}}">
        <button class="p-3" style="background: #9400FF; border-radius: 10px; width: 162px;">
                <h6 class="align-self-center" style="font-size: 12.12px; font-weight: 600; color: #FFFFFF;">Log Out</h6>
            </button>
        </a>
    </div>
    <!-- Daily reward div start -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content mx-4" style="border-radius: 25px;">
                <div class="modal-header d-flex justify-content-center"
                    style="background: #BE6EF8; border-radius: 25px 25px 0px 0px;">
                    <span class=" text-center text-capitalize text-white" id="staticBackdropLabel"
                        style="font-size: 12px; font-weight: 800;">Signup Bonus 
                    </span>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <img src="{{asset ('core/img/income-detail.png')}}"
                            style="width: 34px; height: 34px; margin-right: 5px;">
                        <h1 style="color: #020202; font-size: 25px; ">{{ $general->cur_text }}<strong>{{ showAmount(auth()->user()->deposit_wallet) }}</strong></h1>
                    </div>
                    <div class="text-center mt-2">
                        
                        <button class="px-5 p-2"  type="button"
                            style="background-color: #BE6EF8; color: white; border-radius: 25px; font-size: 12px;"
                            id="collect_btn">
                           Already Collected
                        </button>
                        <button class="px-4 btn text-center" type="button" onclick="collectedReward()"
                            style="background-color: #BE6EF8; color: white; border-radius: 25px; font-size: 12px;display:none"
                            id="collect_btn2">
                           Already Collected
                        </button>
                        

                    </div>

                </div>

            </div>
        </div>
    </div>

    
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

</body>

    <script>
        var dailyreward_amount = document.getElementById('dailyreward_amount')
        var amount = 140.0
        // Function to show the snackbar message
        function showSnackbar() {
            var snackbar = document.getElementById("snackbar");
            var collect_btn = document.getElementById("collect_btn");
    
            amount = amount + 15;
            dailyreward_amount.textContent = amount.toFixed(2);
            //collect_btn.disabled = true;
            //collect_btn.textContent= "Already Collected";
            document.getElementById('collect_btn2').style.display= 'block';
           document.getElementById('collect_btn2').style.marginLeft = "15%"
           document.getElementById('collect_btn2').style.width = "70%"
            collect_btn.style.display= 'none';
            snackbar.className = "show";
            snackbar.textContent = 'Successfully'
            setTimeout(function () {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000); // Hide the snackbar after 3 seconds (adjust as needed)
        }
    
        // Function to handle the "Collect" button click
        function collectReward() {
            // Hide the modal popup
            $('#exampleModal').modal('hide');
            $.ajax({
                url: '/popup/dailyreward/',
                type: 'GET',
                success: function (data) {
                    // The AJAX request was successful
                    showSnackbar();
                },
                error: function (xhr, status, error) {
                    console.error('Error calling view function:', error);
                    // Handle the error if necessary
                }
            });
        }
    
        function collectedReward() {
            // Hide the modal popup
            $('#exampleModal').modal('hide');
            var snackbar = document.getElementById("snackbar");
            snackbar.className = "show";
            snackbar.textContent = 'Already Collected'
            setTimeout(function () {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }
    </script>
    
    <script>
        const targetDiv = document.getElementById("mine_div");
        targetDiv.classList.add("active");
    </script>

</body>

    <br><br><br>
    <script src="/static/js/jquery-3.0.0.min.js"></script>
    <script src="/static/js/custom.js"></script>


    <script>
        //     const list = document.querySelectorAll(".nav__item");
        // list.forEach((item) => {
        //   item.addEventListener("click", () => {
        //     list.forEach((item) => item.classList.remove("active"));
        //     item.classList.add("active");
        //   });
        // });
        var notification_num = document.getElementById('notification_num')

        function NotificationFunction() {
            $.ajax({
                url: '/admin_dashboard/view_notification/',
                type: 'GET',

                error: function (xhr, status, error) {
                    console.error('Error calling view function:', error);
                    // Handle the error if necessary
                }
            });
            notification_num.style.display = 'none'
        }
        $(document).ready(function () {
            // Handle pagination link clicks
            $(document).on('click', '#pagination-plan a', function (event) {
                event.preventDefault();
                var pageUrl = $(this).attr('href');

                // Make AJAX request to fetch the new page data
                $.ajax({
                    url: pageUrl,
                    type: 'GET',
                    dataType: 'html',
                    success: function (data) {
                        var result = $('<div />').append(data);
                        var newContent = result.find('#plan-container').html();
                        var newPagination = result.find('#pagination-plan').html();

                        // Update the content and pagination
                        $('#plan-container').html(newContent);
                        $('#pagination-plan').html(newPagination);
                    },
                    error: function () {
                        alert('Error occurred while fetching page.');
                    }
                });
            });
        });
    </script>
    <script src="/static/js/jquery.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

</body>
   
   

@endsection
