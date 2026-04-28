@extends($activeTemplate.'layouts.master')
@section('content')



                
                <body class="main-layout m-0 p-0">

    

<head>
  <link rel="stylesheet" href="{{asset ('core/recharge.css')}}">
</head>
<style>
  * {
    font-family: Montserrat;
  }

  .nav__item:hover .nav__item-icon:hover,
  .nav__item-text:hover,
  .nav__item-link:hover {
    color: #d4da0d;
  }

  .label {
    font-size: 11px;
    font-weight: bold;
    /* margin-bottom: 10px; */
    color: black;
  }
  .header-img-text {
    position: absolute;
    top: 45%;
    left: 3%; 
    font-weight: 700;
    font-size: 18px;
    padding: 10px;
    color: #000000;
  }

  .header-img-text2 {
    position: absolute;
    top: 50%;
    left: 2%; 
    font-weight: 500;
    font-size: 17px;
    padding: 10px;
    color: #000000;
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

  .register-button:hover {
    background-color: #9400FF;

  }

  .container {
    max-width: 800px;
    padding: 0px 10px;
  }

  .recharge-label {
    font-size: 16px;
    font-weight: 500;
    color: #000000;
    font-family: Gotham;

  }

  .register-button {
    display: block;
    font-size: 17px;
    background-color: #9400FF;
    /* box-shadow: 0px 0px 5px 0px #3B3B3B8F; */
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 24px;
    height: 48px;
    font-weight: 700;
  }


  #header-container {
    max-width: 100%;
    padding: 0px;
    margin: 0 auto;
  }

  #header-card {
    background: #3F3F3F;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    border-radius: 0px 0px 0px 47px;
    /* margin-top: -20px; */
  }

  @media screen and (min-width: 768px) {
    #header-container {
      max-width: 750px;
    }
  }

  @media screen and (min-width: 992px) {
    #header-container {
      max-width: 970px;
    }
  }

  @media screen and (min-width: 1200px) {
    #header-container {
      max-width: 1170px;
    }
  }

  @media screen and (max-width: 576px) {
    #header-card {
      height: 120px;
      overflow: hidden;
    }
  }

  .image-style {
    width: 50px;
  }

  .recharge-heading {
    font-size: 15px;
    color: #666467;
    font-weight: 500;
  }

  .header-img-container {
    position: relative;
    text-align: center;
    /* margin-top: -60px; */
  }

  

  .form-group {
    margin-bottom: 5px;
    margin-top: 20px;
  }

  .form-group label {
    font-size: 12px;
    display: block;
    font-weight: 700;
    color: #000000B2;
  }

  .form-group input,
  select {
    width: 100%;
    height: 40px;
    padding-left: 8px;
    border-radius: 10px;
    box-shadow: 0px 0px 6px 0px #0000001A;
    font-size: 10px;
    font-weight: 400;
    margin-top: 8px;
    border: none;
  }

  .form-group input[type="text"]:disabled {
    background-color: #f4f4f4;
  }

  .text-heading {
    font-size: 11px;
    font-weight: 400;
  }

  .status-label {
    color: #019901B2;
    font-size: 9px;
    font-weight: 700;
  }

  .status-label-not {
    color: #FF0000B2;
    font-size: 9px;
    font-weight: 700;
  }

  input::placeholder {
    color: rgba(0, 0, 0, 0.25);
    /* Change the color to your desired color */
  }

  .amount-button {
    font-size: 15px;
    font-weight: 700;
    background-color: #fff;
    box-shadow: 0px 0px 6px 0px #0000001A;
    border-radius: 10px;
    cursor: pointer;
    text-align: start;
    min-width: 100px;
    height: 36px;
  }
  ::placeholder {
  color: #00000040;
  font-size: 10px;
  font-weight: 400;
}
.header-img-container {
              /* width: 100%; */
              height: 120px;
            position: relative; 
            text-align: center; 
            background-image: url('https://perview.freelancerawais.online/cyclepro/core/img/header-bg.png');
      background-size: cover;
            /* margin-top: -60px; */
        }
        .text-overlay {
            position: absolute;
            top: 65%; /* Adjust the top position as needed */
            left: 50%; /* Adjust the left position as needed */
            transform: translate(-50%, -50%); /* Center the text horizontally and vertically */
            text-align: center; /* Center text content */
        }
        .icon-overley {
    position: absolute;
    top: 14%;
    right: 2%;
    text-align: center;
  }
        .home-page-heading-white {
      color: white;
      font-size: 20px;
      font-weight: 500;
      line-height: 11px;
  }

  .home-page-sub-heading-white {
      color: white;
      font-size: 19px;
      font-weight: 700;
  }
    
</style>

<body class="main-layout">
  <div class="mx-auto">
    <h4 id="snackbar_error"></h4>
  </div>
  <div class="mx-auto col-sm-10">
    <h4 id="snackbar"></h4>
  </div>
  <div class="">
    <!-- <div class="p-3 row m-0 p-0 " id="header-card"> -->
      <!-- <div class="col-8"> -->
        <h2 class="recharge-label m-3 ">Withdraw</h2>
      <!-- </div> -->
      <!-- <div class="text-end col-4">
        <a href="/deposit_history/">

          <img src="/static/images/Group9.png" alt="Image" style="height: 20px;" />
        </a>
      </div>
    </div> -->
    <div class="header-img-container mx-3 mt-2">
      <div class="text-overlay w-100">
       
        <div>
          <h5 class="home-page-heading-white">{{ $general->cur_text }} {{ showAmount(auth()->user()->interest_wallet) }}</h5>
          <h5 class="home-page-sub-heading-white mt-2">Account Balance</h5> 
        </div>
         
      </div>
      <div class="text-end mt-2">     
        <a href="{{route ('user.withdraw.history')}}" class="icon-overley">
          <img src="{{asset ('core/img/record-icon.png')}}" alt="Image" style="height: 24px;" />
        </a> 
      </div>
  </div>
    <div class="container mb-1" id="header-container">
      <div class="container">
    

                    <form action="{{route('user.withdraw.money')}}" method="post">
                        @csrf
                        
                        

          
      </div>
    </div>
    <div class="text-end me-3 mt-2">

    </div>
    <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content mx-1" style="background-color: transparent; border: none;">
            <img src="{{asset ('core/img/deposit-pop.png')}}" style="width: 100%; height: 100%;" />
           
        </div>
    </div>
</div>
                 
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Select Gateway')</label>
                                    <select class="form-select form-control form--control" name="method_code" required>
                                                                  @foreach($withdrawMethod as $data)
                                    <option value="{{ $data->id }}" data-resource="{{$data}}">  {{__($data->name)}}</option>
                                @endforeach
        
                                    </select>
                                </div>
                            </div>
                                </div>
                            </div>
                            
                                </div>
                            </div>
                                          <center>
                      
                                    <span>@lang('Limit')</span>
                                    <span><span class="min fw-bold">0</span> {{__($general->cur_text)}} - <span class="max fw-bold">0</span> {{__($general->cur_text)}}</span>
                         
                            
                       
                            
                     </center>
                            
    <div class="form-group mx-3">
      <div class="form-group">
        <div class="d-flex align-items-end mb-1">
          <span>
            <img src="{{asset ('core/img/recharge-label.png')}}" style="width: 16px;">
          </span>
        <label for="holder-name" class="ms-2">Enter Withdraw amount</label>
        </div>

        <input type="number" class="input-field no-arrow recharge-input" name="amount" required id="amountField"
          style="box-shadow: 0px 0px 5px 0px #47474740; color: rgba(0, 0, 0, 0.25); border: none;"
          placeholder="Enter recharge amount">
      </div>
    </div>
    <div class="amount-section row p-2 p-0 m-0 px-3 mt-2">



    </div>

    <div class="d-flex justify-content-center">

      <button class="register-button text_align_center mt-2 px-4 w-75" type="submit" >Request Now</button>
    </div>

    </form>
    <!-- <div class="m-4">
      <h3 class="ps-2" style="font-size: 15px; font-weight: 700;">Specification
      </h3>
      <div class="ps-2 pt-2">
        <span class="text-heading">
          1. On-site recharge is the only and safest payment method<br>
        </span>
      </div>
      <div class="ps-2">
        <span class="text-heading">
          2. Minimum recharge amount is 1900Rs<br>
        </span>
      </div>
      <div class="ps-2">
        <span class="text-heading">
          3. Your recharge will be approved in 30 minutes<br>
        </span>
      </div>
      <div class="ps-2">
        <span class="text-heading">
          4. After paid, please wait 30 minutes. If not show after 30
          minutes, then upload payment voucher to platform
          or contact customer service
        </span>
      </div>

    </div> -->
  </div>

  <script>
    var submitButton = document.getElementById("submitButton");
    var amountField = document.getElementById('amountField');
    var snackbar = document.getElementById("snackbar_error");

    document.getElementById('submitButton').addEventListener('click', function () {
      var amountValue = amountField.value.trim();

      if (amountValue < 1999) {
        event.preventDefault(); // Prevent form submission
        snackbar.className = "show";
        snackbar.textContent = 'Minimium Recharge Rs 2000'
        setTimeout(function () { snackbar.className = snackbar.className.replace("show", ""); }, 3000);
      }
      else {
        submitButton.disabled = true;
        // submitButton.textContent = "Continue....";
        document.getElementById('rechargeForm').submit();
      }

    });
  </script>
  <script>
    function setAmount(amount) {
      document.querySelector('.recharge-input').value = amount;
    }
  </script>

</body>
</body>

                
                
                
                
                
                
                

    
                        
                        
                        
                        
                        
               
                    </form>
                </div>
            </div>
     
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        $('select[name=method_code]').change(function(){
            if(!$('select[name=method_code]').val()){
                $('.preview-details').addClass('d-none');
                return false;
            }
            var resource = $('select[name=method_code] option:selected').data('resource');
            var fixed_charge = parseFloat(resource.fixed_charge);
            var percent_charge = parseFloat(resource.percent_charge);
            var rate = parseFloat(resource.rate)
            var toFixedDigit = 2;
            $('.min').text(parseFloat(resource.min_limit).toFixed(2));
            $('.max').text(parseFloat(resource.max_limit).toFixed(2));
            var amount = parseFloat($('input[name=amount]').val());
            if (!amount) {
                amount = 0;
            }
            if(amount <= 0){
                $('.preview-details').addClass('d-none');
                return false;
            }
            $('.preview-details').removeClass('d-none');

            var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
            $('.charge').text(charge);
            if (resource.currency != '{{ $general->cur_text }}') {
                var rateElement = `<span>@lang('Conversion Rate')</span> <span class="fw-bold">1 {{__($general->cur_text)}} = <span class="rate">${rate}</span>  <span class="base-currency">${resource.currency}</span></span>`;
                $('.rate-element').html(rateElement);
                $('.rate-element').removeClass('d-none');
                $('.in-site-cur').removeClass('d-none');
                $('.rate-element').addClass('d-flex');
                $('.in-site-cur').addClass('d-flex');
            }else{
                $('.rate-element').html('')
                $('.rate-element').addClass('d-none');
                $('.in-site-cur').addClass('d-none');
                $('.rate-element').removeClass('d-flex');
                $('.in-site-cur').removeClass('d-flex');
            }
            var receivable = parseFloat((parseFloat(amount) - parseFloat(charge))).toFixed(2);
            $('.receivable').text(receivable);
            var final_amo = parseFloat(parseFloat(receivable)*rate).toFixed(toFixedDigit);
            $('.final_amo').text(final_amo);
            $('.base-currency').text(resource.currency);
            $('.method_currency').text(resource.currency);
            $('input[name=amount]').on('input');
        });
        $('input[name=amount]').on('input',function(){
            var data = $('select[name=method_code]').change();
            $('.amount').text(parseFloat($(this).val()).toFixed(2));
        });
    })(jQuery);
</script>
@endpush
