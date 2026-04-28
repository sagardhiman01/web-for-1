@extends($activeTemplate.'layouts.master')
@section('content')



<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
<style>
    * {
        font-family: Montserrat;
    }

    .main-heading {
        font-size: 16px;
        font-weight: 500;
        color: #000000;
        font-family: Gotham;
    }

    .header-img-container {
        position: relative;
        text-align: center;
    }

    .header-img-text-invite {
        position: absolute;
        top: 13%;
        /* left: 15%;  */
        color: #FFFFFF;
        font-weight: 400;
        font-size: 12px;
        padding: 10px;
    }

    .header-img-text {
        position: absolute;
        top: 32%;
        /* left: 10%; */
        font-weight: 600;
        font-size: 20px;
        padding: 10px;
    }

    .header-img-text2 {
        position: absolute;
        top: 63%;
        /* left: 10%; */

        font-weight: 500;
        font-size: 18px;
        padding: 10px;
    }

    .cash-back,
    .team-level,
    .rewards-card {
        box-shadow: 0px 0px 10px 0px #0000001A;
        border-radius: 15px;
    }

    .style-line {
        border: 0.7px solid #000000;
        opacity: 0.5;
    }

    .team-sub-heading {
        font-size: 12px;
        font-weight: 500;
        color: #000000;
    }

    .team-sub-heading-count {
        font-size: 11px;
        font-weight: 500;
        color: #546370;
    }

    .team-card-sub-heading {
        color: #000000;
        font-size: 11px;
        font-weight: 500;
    }

    .plan-card {
        width: 175px;
        height: 100px;
        background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/linear-bg.png');
        /* Set the background image URL */
        background-size: cover;
        /* Scale the image to cover the entire div */
        position: relative;
        /* Needed for absolute positioning of text */
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

    .team-page-heading-white {
        font-size: 13.46px;
        font-weight: 600;
        color: #FFFFFF;
    }

    .team-page-sub-heading-white {
        font-size: 13.46px;
        color: #353030;
        font-weight: 600;
        margin-top: 5px;
    }
</style>



@php
  $authUser = Auth::user();
  $userCount = App\Models\User::where('ref_by', $authUser->id)->count();
@endphp



@php
    use App\Models\Transaction;
    $authUserId = Auth::id();
    $referralCommission = Transaction::where('user_id', $authUserId)
                                    ->where('remark', 'referral_commission')
                                    ->sum('amount');
    $formattedReferralCommission = number_format($referralCommission, 0, '.', '');
@endphp

<div class="mx-auto text-center">
    <h4 id="snackbar_error"></h4>
</div>
<div class="mx-auto col-sm-10">
    <h4 id="snackbar"></h4>
</div>
<div class="mb-4">
    
    <div class="text-center mt-3 p-1">
        <span href="/" class="main-heading">Team</span>
    </div>
    <div class="d-flex justify-content-evenly p-0 m-0">
        <div class="plan-card">
            <div class="text-overlay">
                <h5 class="team-page-heading-white">   {{ $userCount }}</h5>
                <h5 class="team-page-sub-heading-white">Total People</h5>
            </div>
        </div>
        <div class=" plan-card ">
            <div class="text-overlay">
                <h5 class="team-page-heading-white">
                    
                   {{ $general->cur_text }} {{ $referralCommission }}
                    
                </h5>
                <h5 class="team-page-sub-heading-white">Total Commission</h5>
            </div>
        </div>
    </div>
    
    
    	<style>
.tooltip {
  position: relative;
  display: inline-block;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 140px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px;
  position: absolute;
  z-index: 1;
  bottom: 150%;
  left: 50%;
  margin-left: -75px;
  opacity: 0;
  transition: opacity 0.3s;
}

.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
</style>
	<div style="display:none;">
<input type="text" value="{{ route('home') }}?refcode={{ auth()->user()->username }}" id="myInput">
</div>

	
		<div style="display:none;">
<input type="text" value="{{ auth()->user()->username }}" id="myInpu">
</div>

	
		
<script>
function myFunctio() {
  var copyText = document.getElementById("myInpu");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  
  var tooltip = document.getElementById("myToolti");
  tooltip.innerHTML = "Copied " ;
}

function outFun() {
  var tooltip = document.getElementById("myToolti");
  tooltip.innerHTML = "Copy to clipboard";
}
</script>


	
<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copied " ;
}

function outFunc() {
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copy to clipboard";
}
</script>


    
    
    
    
    <div class="header-img-container m-2 mt-4">
        <img class="w-100" src="{{asset ('core/img/team-background.png')}}" alt="Image" />
        <div class="header-img-text-invite row m-0 p-0 w-100">
            <span style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Invitation Code </span>
        </div>
        <div class="header-img-text ps-5 row m-0 p-0 w-100  justify-content-center">
            <!-- <div class="col-2 align-self-end pb-1" style="font-size: 12px; font-weight: 800; color: #FFFFFF;cursor: pointer;">
                    Code
                </div> -->
            <div class="col-6 p-0 pt-2">
                <h5 class="w-100 p-2 col-8" id="referal_code"
                    style="font-size: 12px; font-weight: 500; color: #2D2C2CB2; background-color: #FFFFFF; border-radius: 5px;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis">
                   {{ auth()->user()->username }}
                </h5>
            </div>
            <div class="col-2">
                <span class="p-1 px-3"        id="myToolti"    onclick="myFunctio()" onmouseout="outFun()"
                    style="border-radius: 25px; font-weight: 500; font-size: 9.5px;">
                    <img src="{{asset ('core/img/copy-icon.png')}}" style="width: 15px;"> </span>
            </div>
        </div>
        <div class="header-img-text2 ps-5 row m-0 p-0 w-100 justify-content-center">
            <!-- <div class="col-2 align-self-end pb-1" style="font-size: 12px; font-weight: 800; color: #FFFFFF;">
                    Link
                </div> -->
            <div class="col-6 p-0 pt-2">
                <h5 class="w-100 p-2 px-2" id="referal_link"
                    style="font-size: 12px; font-weight: 500; color: #2D2C2CB2; background-color: #FFFFFF; border-radius: 5px;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis">
                 
                    
                    {{ route('home') }}?refcode={{ auth()->user()->username }}
                </h5>
            </div>
            <div class="col-2 pt-1">
                <span class="p-1 px-3" style="border-radius: 25px; font-weight: 500; font-size: 9.5px;"
                 id="myTooltip"      onclick="myFunction()" onmouseout="outFunc()"   >
                    <img src="{{asset ('core/img/copy-icon.png')}}" style="width: 15px;">
                </span>
            </div>
        </div>
    </div>

    <!-- <div class="cash-back mx-2 p-3 mt-5">
    <div style="margin-top: -25px;">
        <span class="bg-black p-1 rounded-2" style="font-size: 9.5px; font-weight: 700; color: #FFFFFF; margin-top: -20px;"><i class="fa-solid fa-check-to-slot text-white"></i>  Cash Back</span>
    </div>
            <h5 class="mt-2" style="font-size: 11.04px; font-weight: 500; color: #363535;">Invite friends to invest and receive 25%</h5>
            <h5 style="font-size: 11.04px; font-weight: 500; color: #363535;">cashback gift on first level.</h5>
            <div class="d-flex justify-content-between">
                <div class="withdraw-label">
                    <span class="team-sub-heading">Total people</span>
                </div>
                <div>
                    <span class="team-rs-sub-heading">0</span>
                </div>
            </div>

            <div class="underline"></div>

            <div class="d-flex justify-content-between">
                <div class="recieve-label">
                    <span class="team-sub-heading">Total Cashback</span>
                </div>
                <div>
                    <span class="team-rs-sub-heading">RS 0.00</span>
                </div>
            </div>

</div> -->

    <div class="team-level mx-2 p-3 mt-4">
        <div class="d-flex justify-content-between">
            <span style="font-size: 15px; font-weight: 700; color: #000000;"><img
                    src="{{asset ('core/img/level-one.png')}}" style="width: 20px;"> Level 1/2/3</span>
            <span style="font-size: 13px; font-weight: 700; color: #000000;">12%/5%/3%</span>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <div>
                <div class=" text-center">
                    <span class="team-card-sub-heading">{{ $userCount }}</span>
                </div>
                <div class="withdraw-label">
                    <span class="team-sub-heading">Total people</span>
                </div>
            </div>
            <div class="style-line">

            </div>
            <div>
                <div class=" text-center">
                    <span class="team-card-sub-heading">{{ $general->cur_text }} {{ $referralCommission }}</span>
                </div>
                <div class="withdraw-label">
                    <span class="team-sub-heading">Commission</span>
                </div>
            </div>
        </div>
        <!-- <div class="d-flex justify-content-between">
                <div class="recieve-label">
                    <span class="team-sub-heading">Total Invest</span>
                </div>
                <div>
                    <span class="team-card-sub-heading">RS 
                        
                        0
                        
                    </span>
                </div>
            </div> -->

    </div>


            <!-- <div class="d-flex justify-content-between">
                <div class="recieve-label">
                    <span class="team-sub-heading">Total Invest</span>
                </div>
                <div>
                    <span class="team-card-sub-heading">RS 
                        
                        0
                        
                    </span>
                </div>
            </div> -->


    <!-- reward - div ended -->
    <!-- Toast message  -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body  text-white">
                Reference code copy.
                <button type="button" class="btn-close" style="float: right;" data-bs-dismiss="toast"
                    aria-label="Close"></button>
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








         
@endsection

@push('style')
    <link href="{{ asset('assets/global/css/jquery.treeView.css') }}" rel="stylesheet" type="text/css">
@endpush
@push('script')
<script src="{{ asset('assets/global/js/jquery.treeView.js') }}"></script>
<script>
    (function($){
    "use strict"
        $('.treeview').treeView();
        $('.copyBoard').click(function(){
                var copyText = document.getElementsByClassName("copyURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);

                /*For mobile devices*/
                document.execCommand("copy");
                $('.copyText').text('Copied');
                setTimeout(() => {
                    $('.copyText').text('Copy');
                }, 2000);
        });
    })(jQuery);
</script>
@endpush
