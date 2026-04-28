@extends($activeTemplate.'layouts.master')
@section('content')


   
     

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="modal fade show" id="myPopup" tabindex="-1" role="dialog"
aria-labelledby="popupTitle" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm justify-content-center" role="document">
      <div class="linear-bg">
      <div class="modal-content " style="background: transparent; border:none;">
     
        <div class="modal-body p-4">
          <button type="button" class="align-self-center position-absolute p-3 px-3" data-bs-dismiss="modal" aria-label="Close" onclick="intial_popup()" style="background-color: transparent;"> </button>

          <img src="{{asset ('core/img/initial-pop.png')}}" class="w-100 border-0" style="border:none;" >
         <!-- <h1 style="font-size: 12px; font-weight: 700; color: #FFFFFF;">THE LATEST LOGIN,</h1>        
         <h1 style="font-size: 12px; font-weight: 700; color: #FFFFFF;">WELCOME TO SMART ULTRA</h1>
         <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Sign up and get Rs150 now</h1>
         </span>
         <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">You will get Rs12 for check in daily</h1>
        </span>
        <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Free VIP1 earn Rs20 every day</h1>
        </span>
        <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Invest in Vip2 and earn 161.5 daily</h1>
        </span>
        <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Invest in Vip3 and earn 306 daily</h1>
        </span>
        <span class="d-flex mt-2">
          <img src="/static/images/Star.png" style="width: 8px; height: 8px;" class="align-self-center">
        <h1 style="font-size: 12px; font-weight: 400; color: #FFFFFF;">Invest in vip4 and earn 437 daily</h1>
        </span> -->

        <a href="/services/" class="align-self-center position-absolute p-5 px-5" style="background-color: transparent; right: 70px; bottom: 70px;"> </a>

        </div>

      </div>
    </div>
    </div>
    
</div>

<script>
    $(document).ready(function () {
      $('#myPopup').modal('show');
    });
  //   setTimeout(function () {
  //   $('#myPopup').modal('hide');
  // }, 4000); // 4000 milliseconds (4 seconds)
    /*
    function intial_popup() {
        $.ajax({
            url: '/popup/intial/',
            type: 'GET',

            error: function (xhr, status, error) {
                console.error('Error calling view function:', error);
                // Handle the error if necessary
            }
        });
    }
    */
    function navigateToWhatsApp() {
    window.location.href = "https://api.whatsapp.com/send/?phone=92";
  }
  function navigateToGroup() {
    window.location.href = "";
  }
</script>

<link rel="stylesheet" href="/static/css/snackbar.css">



<div class="d-flex justify-content-between heading_label m-0 p-0 px-3 pt-2">
    <div>
        <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo.png')) }}" style="width: 130px;">
    </div>
    
    <div data-bs-toggle="modal" data-bs-target="#exampleModal">
        <img src="{{asset ('core/img/home-checkin.png')}}" class="m-3" style="width: 56px;">
    </div>
    
</div>


<!-- graf -->
<div class="mx-auto" id="snackbar_error"></div>
<div id="snackbar"></div>

<!-- start slider section -->
<div id="top_section" class="banner_main" style="margin-top: -70px;">
    <div id="carouselExampleControls" class="carousel slide mx-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div id="top_section" class="banner_main">

                    <img src="{{asset ('core/img/header-one.png')}}" alt="Your Image" class=" w-100">

                </div>
            </div>
            <div class="carousel-item">
                <div id="top_section" class="banner_main">
                    <img src="{{asset ('core/img/header-two.png')}}" alt="Your Image" class=" w-100">

                </div>
            </div>
            <div class="carousel-item">
                <div id="top_section" class="banner_main">
                    <img src="{{asset ('core/img/header-three.png')}}" alt="Your Image" class=" w-100">

                </div>
            </div>
        
        </div>
    </div>

</div>
<div class="row mx-2 m-0 p-0 mt-4">
    <div class="col-3">
        <a href="{{route ('user.deposit.index')}}">
            <div class="wallet_box text_align_center">
                <img src="{{asset ('core/img/recharge.png')}}" style="width: 54px;">
                <h4 style="color: #000000; font-size: 10px; font-weight: 700;
                            ">Recharge</h4>
            </div>
        </a>
    </div>
    <div class="col-3">
        <a href="{{route ('user.withdraw')}}">
            <div class="wallet_box text_align_center">
                <img src="{{asset ('core/img/withdraw.png')}}" style="width: 54px;">
                <h4 style="color: black; font-size: 10px; font-weight: 700;
                            ">Withdraw</h4>
            </div>
        </a>
    </div>
    <div class="col-3">
        <a href="{{ route('user.profile.setting') }}">
            <div class="wallet_box text_align_center">
                <img src="{{asset ('core/img/account.png')}}" style=" width: 54px;">
                <h4 style="color: black; font-size: 10px; font-weight: 700;">
                    Account
                </h4>
            </div>
        </a>
    </div>
    <div class="col-3">
        <a href="{{ route('user.referrals') }}">
            <div class="wallet_box text_align_center">
                <img src="{{asset ('core/img/task.png')}}" style=" width: 54px;">
                <h4 style="color: black; font-size: 10px; font-weight: 700;
                            ">Team</h4>
            </div>
        </a>
    </div>
</div>

<!-- end slider section -->
<div class="banner-container m-2 mt-3" id="#aboutus">
    <a class="p-0 m-0" href="{{ route('user.referrals') }}">
        <img src="{{asset ('core/img/invite-card.png')}}" alt="Your Image" class="image w-100">
    </a>
</div>
<div>
    <h1 class="extra-label">Product</h1>
</div>
<div class="d-flex justify-content-evenly p-0 m-0">
    <div class="plan-card">
        <div class="text-overlay">
            <h5 class="home-page-heading-white">
                {{ $general->cur_text }} <span id="dailyreward_amount">
                    {{ showAmount(auth()->user()->interest_wallet) }}
               </span>
            </h5>
            <h5 class="home-page-sub-heading-white">Balance</h5>
        </div>
    </div>
    <div class=" plan-card ">
        <div class="text-overlay">
            <h5 class="home-page-heading-white">
                {{ $general->cur_text }} {{ showAmount($successfulWithdrawals) }}
            </h5>
            <h5 class="home-page-sub-heading-white">Total Withdraw</h5>
        </div>
    </div>
</div>
<div>
    <h1 class="extra-label mt-3">VIP section </h1>
    
    
    
    
    
    
    
</div>



    @php
        $plans = App\Models\Plan::where('status', 1)
            ->where('featured', 1)
            ->get();
        $gatewayCurrency = null;
        if (auth()->check()) {
            $gatewayCurrency = App\Models\GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })
                ->with('method')
                ->orderby('method_code')
                ->get();
        }
    @endphp




<div class="graf mb-5 p-0 m-0 row justify-content-center">
    
    
    
    
    @foreach ($plans as $plan)
    
    
    
    
    
    
    
    <div class="invest-card mt-3 mb-4 p-0 col-5 mx-2">
        <!-- <span class="m-2"
        style="padding: 1px 1px; font-size: 11px; font-weight: 500; background-color: rgba(39, 38, 38, 1); border-radius: 15px; color: white;"><img
            src="/static/images/vip-pass1.png" style="width: 16px; height: 15px;" />
       <span class="pe-1"> 1</span>
    </span> -->
        <div>
            <!-- <div class="position-absolute"> -->
            
            <!-- </div> -->
            <div class="text-center p-1">
                
                <img src="{{asset ('core/img/a5ef3b7f-33dc-46e9-afeb-951ed5e0f3cf.jpg')}}" alt="Image" style="height: 100px; width: 90%;" />
                
            </div>
            <div class="text-center">
                <span class="plan-name">{{ __($plan->name) }}</span>
            </div>

            <div class="row justify-content-end m-0 p-0">
                <h5 class="invest-plan-text col-6 mt-1">{{ __(strtoupper($plan->time_name)) }} Profit</h5>
                <h5 class="invest-plan-text col-5 mt-1">  {{ $plan->interest_type != 1 ? $general->cur_sym : '' }}{{ showAmount($plan->interest) }}{{ $plan->interest_type == 1 ? '%' : '' }}</h5>
            </div>
          

            <div class="row justify-content-end m-0 p-0">
                <h5 class="invest-plan-text col-6 pt-1">Cycle</h5>
                <h5 class="invest-plan-text col-5 pt-1">@if ($plan->lifetime == 0)
                            {{ __($plan->repeat_time) }} {{ __($plan->time_name) }}
                        @else
                            @lang('LIFETIME')
                        @endif </h5>

            </div>
            
                 <div class="row justify-content-end m-0 p-0">
                <h5 class="invest-plan-text col-6 pt-1">Total Income</h5>
                <h5 class="invest-plan-text col-5 pt-1"> @if ($plan->lifetime == 0)
                        @lang('')
                        {{ __($plan->interest * $plan->repeat_time) }}{{ $plan->interest_type == 1 ? '%' : ' ' . __($general->cur_text) }}
                        @lang('')
                    @else
                        @lang('Unlimited')
                    @endif </h5>

            </div>
            
            
            
            <div class="text-center m-1">
                <h1 style=" font-size: 15.32px; font-weight: 500; color: black;">
                    Price    @if ($plan->fixed_amount == 0)
                                {{ __($general->cur_sym) }}{{ showAmount($plan->minimum) }} -
                                {{ __($general->cur_sym) }}{{ showAmount($plan->maximum) }}
                            @else
                                {{ __($general->cur_sym) }}{{ showAmount($plan->fixed_amount) }}
                            @endif
                </h1>
            </div>
            <div class="text-center">
                <button class="btn p-0 m-0 p-1 px-3 investModal"    
                     data-bs-toggle="modal"
                data-plan="{{ $plan }}" data-bs-target="#investModal" 
                    style="font-size: 13.15px; font-weight: 700; cursor: pointer; background: #BE6EF8; border-radius: 20px; font-size: 15.32px; font-weight: 700; color: #FFFFFF;">Continue
                </button>
                       
           
                
                
            </div>
        </div>








        <div class="modal" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content mx-1" style="background-color: transparent; border: none;">
                    <img src="{{asset ('core/img/purchase-plan.png')}}" style="width: 100%; height: 100%;" />
                    <div class="position-absolute w-100">
                        <div class="modal-header p-0 border-0 d-flex justify-content-between">
                            <div class="text-center mt-4 ">
                                <span class=""
                                    style="font-size: 19.42px; font-weight: 700; color: #FFFFFF;">Free</span>
                                <div>
                                    <span class="px-3 pe-3"
                                        style="font-size: 14.75px; font-weight: 300; color: #FFFFFF">
                                        Rs. 140
                                    </span>
                                </div>
                            </div>
                              <button type="button" class="p-4" data-bs-dismiss="modal"
                                            aria-label="Close" style="background-color: transparent;"></button>
                            
                        </div>
                        <div class="text-center mt-3">
                            <img src="{{asset ('core/img/plan-cycle.png')}}" style=" height: 110px;" />
                        </div>

                        <div class="modal-body p-0 m-0 mt-3 px-5">
                            <div class="popup-footer">
                                <div class="invite_income">
                                    <div class="border-0 text-white">
                                        <div class="d-flex justify-content-between p-1">
                                            <div>
                                                <span class="popup-heading">Hourly income</span>
                                            </div>
                                            <div>
                                                <span class="popup-price">Rs 1.79</span>

                                            </div>
                                        </div>
                                        <div class="underline"></div>

                                        <div class="d-flex justify-content-between p-1">
                                            <div>
                                                <span class="popup-heading">Daily Income</span>
                                            </div>
                                            <div>
                                                <span class="popup-price">Rs 42.9</span>
                                            </div>
                                        </div>
                                        <div class="underline"></div>
                                        <div class="d-flex justify-content-between p-1">
                                            <div>
                                                <span class="popup-heading">Total Income</span>
                                            </div>
                                            <div>
                                                <span class="popup-price">Rs 300.00</span>
                                            </div>
                                        </div>
                                        <div class="underline"></div>
                                        <div class="d-flex justify-content-between p-1">
                                            <div>
                                                <span class="popup-heading">Cycle Days</span>
                                            </div>
                                            <div>
                                                <span class="popup-price">7 Days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    @if (auth()->check())
                        @lang('Confirm to invest on') <span class="planName"></span>
                    @else
                        @lang('At first sign in your account')
                    @endif
                    
                       <img src="{{asset ('core/img/purchase-plan.png')}}" style="width: 100%; height: 100%;" />
                    
                       @if(auth()->check())
                    <div class="modal-body">
                        <div class="form-group">
                            <h6 class="text-center investAmountRange"></h6>
                            <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>

                            <label>@lang('Select Wallet')</label>
                            <select class="form-control form--control form-select" name="wallet_type" required>
                                <option value="">@lang('Select One')</option>
                                @if(auth()->user()->deposit_wallet > 0)
                                <option value="deposit_wallet">@lang('Deposit Wallet - '.$general->cur_sym.showAmount(auth()->user()->deposit_wallet))</option>
                                @endif
                                @if(auth()->user()->interest_wallet > 0)
                                <option value="interest_wallet">@lang('Interest Wallet -'.$general->cur_sym.showAmount(auth()->user()->interest_wallet))</option>
                                @endif
                                @foreach($gatewayCurrency as $data)
                                    <option value="{{$data->id}}" @selected(old('wallet_type') == $data->method_code) data-gateway="{{ $data }}">{{$data->name}}</option>
                                @endforeach
                            </select>
                            <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ $general->cur_text }} = <span class="gateway-rate"></span> <span class="method_currency"></span></code>
                        </div>
                        <div class="form-group">
                            <label>@lang('Invest Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form--control" name="amount" required>
                                <div class="input-group-text">{{ $general->cur_text }}</div>
                            </div>
                            <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span> {{ $general->cur_text }}. @lang('Total amount'): <span class="total"></span> {{ $general->cur_text }}</code>
                        </div>
                    </div>
                @endif
                    
                    
                    
                    
                    
                                    
                                         <form action="{{ route('user.invest.submit') }}" method="post">
                @csrf
                <input type="hidden" name="plan_id">
                                    
                            
                                        <div class="mx-4">
                                            <button class="btn mb-2 p-1 px-3"
                                            style="cursor: pointer; background: #9400FF;color:white; box-shadow: 0px 4px 4px 0px #00000040;
                                            border-radius: 20px;"
                                                id="buy-btn" type="submit">Invest</button>
                                        </div>
                                        <!-- <div class="text-center"> <button type="button" class="" data-bs-dismiss="modal"
                                                aria-label="Close"
                                                style="background-color: transparent;">Cancel</button>
                                        </div> -->
                                    </form>
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    



                                    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endforeach









<div class="modal fade" id="investModal">
    
    
    
    <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content mx-1" style="background-color: transparent; border: none;">
                    <img src="{{asset ('core/img/purchase-plan.png')}}" style="width: 100%; height: 100%;" />
                    <div class="position-absolute w-100">
                        <div class="modal-header p-0 border-0 d-flex justify-content-between">
                            <div class="text-center mt-4 ">
                                <span class=""
                                    style="font-size: 19.42px; font-weight: 700; color: #FFFFFF;"> @if (auth()->check())
                        @lang('') <span class="planName"></span>
                    @else
                        @lang('At first sign in your account')
                    @endif</span>
                    
                     <form action="{{ route('user.invest.submit') }}" method="post">
                @csrf
                <input type="hidden" name="plan_id">
                
                <a style="display:none">
                     @if(auth()->check())
                                <div>
                                    <span class="px-3 pe-3"
                                        style="font-size: 14.75px; font-weight: 300; color: #FFFFFF"
                                      class="text-center investAmountRange">
                                    </span>
                                </div>
                            </div>
                            
             <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>
                             <select  name="wallet_type" required>
                             @if(auth()->user()->deposit_wallet > 0)
                                <option value="deposit_wallet">@lang('Deposit Wallet - '.$general->cur_sym.showAmount(auth()->user()->deposit_wallet))</option>
                                @endif
                                @if(auth()->user()->interest_wallet > 0)
                                <option value="interest_wallet">@lang('Interest Wallet -'.$general->cur_sym.showAmount(auth()->user()->interest_wallet))</option>
                                @endif
                                @foreach($gatewayCurrency as $data)
                                    <option value="{{$data->id}}" @selected(old('wallet_type') == $data->method_code) data-gateway="{{ $data }}">{{$data->name}}</option>
                                @endforeach
                                </select>
                                
                                    <input type="number" step="any" class="form-control form--control" name="amount" required>
                                    
                                    </a>
                                    
                              <button type="button" class="p-4" data-bs-dismiss="modal"
                              
                                            aria-label="Close" style="background-color: transparent;"></button>
                            
                        </div>
                        <div class="text-center mt-3">
                            <img src="{{asset ('core/img/plan-cycle.png')}}" style=" height: 110px;" />
                        </div>
 @endif
                        <div class="modal-body p-0 m-0 mt-3 px-5">
                            <div class="popup-footer">
                                <div class="invite_income">
                                    <div class="border-0 text-white">
                                        <div class="d-flex justify-content-between p-1">
                                            <div>
                                                <span class="popup-heading"><span class="text-center investAmountRange"></span>   </span>
                                            </div>
                                            <div>
                                                <span class="popup-price"></span>

                                            </div>
                                        </div>
                                               
                            <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>
                                        
                                        <div class="underline"></div>

                        
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    
                                    <button class="btn mb-2 p-1 px-3" onclick="validate9()"
                                        style="cursor: pointer; background: #9400FF;color:white; box-shadow: 0px 4px 4px 0px #00000040;
                                        border-radius: 20px;">
                                        Invest
                                    </button>
                                    <!-- <div class="text-center"> <button type="button" class="" data-bs-dismiss="modal"
                                            aria-label="Close" style="background-color: transparent;">Cancel</button>
                                    </div> -->
                                    
</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    
    

    
                    
                    
            
           


@push('script')
<script>
    (function($){
        "use strict"
        $('.investModal').click(function(){
            var symbol = '{{ $general->cur_sym }}';
            var currency = '{{ $general->cur_text }}';
            $('.gateway-info').addClass('d-none');
            var modal = $('#investModal');
            var plan = $(this).data('plan');
            modal.find('[name=plan_id]').val(plan.id);
            modal.find('.planName').text(plan.name);
            let fixedAmount = parseFloat(plan.fixed_amount).toFixed(2);
            let minimumAmount = parseFloat(plan.minimum).toFixed(2);
            let maximumAmount = parseFloat(plan.maximum).toFixed(2);
            let interestAmount = parseFloat(plan.interest);

            if (plan.fixed_amount > 0) {
                modal.find('.investAmountRange').text(`Invest: ${symbol}${fixedAmount}`);
                modal.find('[name=amount]').val(parseFloat(plan.fixed_amount).toFixed(2));
                modal.find('[name=amount]').attr('readonly',true);
            }else{
                modal.find('.investAmountRange').text(`Invest: ${symbol}${minimumAmount} - ${symbol}${maximumAmount}`);
                modal.find('[name=amount]').val('');
                modal.find('[name=amount]').removeAttr('readonly');
            }

            if (plan.interest_type == '1') {
                modal.find('.interestDetails').html(`<strong> Interest: ${interestAmount}% </strong>`);
            } else {
                modal.find('.interestDetails').html(`<strong> Interest: ${interestAmount} ${currency}  </strong>`);
            }

            if (plan.lifetime == '0') {
                modal.find('.interestValidity').html(`<strong>  Every ${plan.time_name} for ${plan.repeat_time} times</strong>`);
            } else {
                modal.find('.interestValidity').html(`<strong>  Every ${plan.time_name} for life time </strong>`);
            }

        });

        $('[name=amount]').on('input',function(){
            $('[name=wallet_type]').trigger('change');
        })

        $('[name=wallet_type]').change(function () {
            var amount = $('[name=amount]').val();
            if($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' && amount){
                var resource = $('select[name=wallet_type] option:selected').data('gateway');
                var fixed_charge = parseFloat(resource.fixed_charge);
                var percent_charge = parseFloat(resource.percent_charge);
                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                $('.charge').text(charge);
                $('.gateway-rate').text(parseFloat(resource.rate));
                $('.gateway-info').removeClass('d-none');
                if (resource.currency == '{{ $general->cur_text }}') {
                    $('.rate-info').addClass('d-none');
                }else{
                    $('.rate-info').removeClass('d-none');
                }
                $('.method_currency').text(resource.currency);
                $('.total').text(parseFloat(charge) + parseFloat(amount));
            }else{
                $('.gateway-info').addClass('d-none');
            }
        });
    })(jQuery);
</script>
@endpush
















    <script>
        function validate1() {
            // Hide the modal popup
            $('#exampleModal1').modal('hide');
            var snackbar = document.getElementById("snackbar");
            snackbar.className = "show";
            snackbar.textContent = 'Balance Insiffience'
            setTimeout(function () {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }

        function validatelimit1() {
            // Hide the modal popup
            $('#exampleModal1').modal('hide');
            var snackbar = document.getElementById("snackbar");
            snackbar.className = "show";
                snackbar.textContent = "Buy maximum " + 1 + " time"
            setTimeout(function () {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000);
        }
    </script>
    

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
                    <img src="{{asset ('core/img/income-detail.png')}}" style="width: 34px; height: 34px; margin-right: 5px;">
                    <h1 style="color: #020202; font-size: 25px; ">{{ $general->cur_text }}<strong>{{ showAmount(auth()->user()->deposit_wallet) }} </strong></h1>
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
                        Collected
                    </button>
                    
                    
                </div>

            </div>

        </div>
    </div>
</div>



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
        snackbar.textContent = 'Received'
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
        snackbar.textContent = 'Already Received'
        setTimeout(function () {
            snackbar.className = snackbar.className.replace("show", "");
        }, 3000);
    }
</script>
<script>
    const targetDiv = document.getElementById("home_div");
    targetDiv.classList.add("active");
</script>

<!-- <script src="/static/js/jquery.min.js"></script> -->


    <br><br><br>
    <script src="{{asset ('core/js/jquery-3.0.0.min.js')}}"></script>
    <script src="{{asset ('core/js/custom.js')}}"></script>


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

@push('script')
<script src="{{ asset($activeTemplateTrue.'/js/lib/apexcharts.min.js') }}"></script>

<script>

    // apex-line chart
    var options = {
        chart: {
            height: 350,
            type: "area",
            toolbar: {
                show: false
            },
            dropShadow: {
                enabled: true,
                enabledSeries: [0],
                top: -2,
                left: 0,
                blur: 10,
                opacity: 0.08,
            },
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: {
                    speed: 1000
                }
            },
        },
        dataLabels: {
            enabled: false
        },
        series: [
            {
                name: "Price",
                data: [
                    @foreach($chartData as $cData)
                        {{ getAmount($cData->amount) }},
                    @endforeach

                ]
            }
        ],
        fill: {
            type: "gradient",
            colors: ['#4c7de6', '#4c7de6', '#4c7de6'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.6,
                opacityTo: 0.9,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            title: "Value",
            categories: [
                @foreach($chartData as $cData)
                "{{ Carbon\Carbon::parse($cData->date)->format('d F') }}",
                @endforeach
            ]
        },
        grid: {
            padding: {
                left: 5,
                right: 5
            },
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: false
                }
            },
        },
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);

    chart.render();

    @if($isHoliday)
        function createCountDown(elementId, sec) {
            var tms = sec;
            var x = setInterval(function () {
                var distance = tms * 1000;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                var days = `<span>${days}d</span>`;
                var hours = `<span>${hours}h</span>`;
                var minutes = `<span>${minutes}m</span>`;
                var seconds = `<span>${seconds}s</span>`;
                document.getElementById(elementId).innerHTML = days +' '+ hours + " " + minutes + " " + seconds;
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById(elementId).innerHTML = "COMPLETE";
                }
                tms--;
            }, 1000);
        }

        createCountDown('counter', {{\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()}});
    @endif

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

</script>
@endpush
