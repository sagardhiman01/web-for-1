@extends($activeTemplate . 'layouts.master')
@section('content')
    

<head>
    <style>
         /* Custom CSS to position the image */
        .center-right {
          display: flex;
          justify-content: flex-end;
          align-items: center;
        }
        .heading_label {
            border-radius: 0px 0px 0px 50px;

        }

        .tabs {
            text-align: center;
            background: rgba(217, 217, 217, 1);
            border-radius: 25px;
            width: 245px;
        }

        .tab {
            cursor: pointer;
            font-weight: 600;
            padding: 7px 15px;
            color: #3F3F3F91;
            font-size: 8.44px;
        }
        .tab:hover {
            background: #3F3F3F;
            color: white;
            border-radius: 25px;
        }

        .plans-label {
            font-size: 16px;
            font-weight: 500;
            color: #000000;
            font-family: Gotham;
        }

        #header-card {
            background: #3F3F3F;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            border-radius: 0px 0px 0px 47px;
            /* margin-top: -20px; */
        }

        .income-label {
            /* font-family: Gotham; */
            font-size: 6.67px;
            font-weight: 700;
            color: #FFFFFF;
        }
        .income-sub-label{
            font-size: 6.67px;
            font-weight: 300;
            color: #FFFFFF;
        }
        .progress-container {
            position: relative;
            width: 100%;
            /* height: 30px; */
        }

        .custom-icon {
            position: absolute;
            top: 0;
            width: 100%;
            background-color: #1E02C5;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }


      
    </style>
</head>

<body>
    <div class="col-12 m-0 p-0">
        <div class="px-3">
            
    <!-- graf -->
    <div class="mx-auto text-center">
        <h4 id="snackbar_error"></h4>
    </div>
    <div class="mx-auto col-sm-10">
        <h4 id="snackbar"></h4>
    </div>
    <div class="mt-3">
        <span>  Device</span></span>   
    </div>
    <!-- <div class="ps-3 pt-3 pe-3 pb-2 row m-0 p-0 " id="header-card"> -->
        <!-- <div class="col-8"> -->
        <!-- </div> -->
        <!-- <div class="text-end col-4">
            <a href="/income/">

            </a>
        </div> -->
        <!-- <div class="d-flex justify-content-center">
            <div class="tabs d-flex justify-content-between p-1 mt-5">
                <div class="tab active " onclick="showTab('in-profit')">In Profit</div>
                <div class="tab" onclick="showTab('finished')">Finished</div>
            </div>
        </div> -->
        <!-- </div> -->
        <div class="mt-2">
            <div class="position-relative w-100 d-flex justify-content-evenly" style="top: 88px;">
                <div>
                    <h1 class="m-0 p-0 text-center" style="font-size: 12px; font-weight: 700; color: #FFFFFF;">
                       {{ $general->cur_text }} {{ showAmount(auth()->user()->deposit_wallet) }}
                    </h1>
            
                    <h1 class="mt-1 p-0 text-center" style="font-size: 12px; font-weight: 500; color: #FFFFFF;">
                Deposited
                     </h1>
                </div>
                <div>
                    <h1 class="m-0 p-0 text-center ps-5" style="font-size: 12px; font-weight: 700; color: #FFFFFF;">
                       {{ $general->cur_text }} {{ showAmount(auth()->user()->interest_wallet) }}
                    </h1>
            <a href="">
            <h1 class="mt-1 p-0 text-center ps-5" style="font-size: 12px; font-weight: 500; color: #FFFFFF;"> My income   <span><img src="{{asset ('core/img/arrow-right.png')}}" style="width: 6px;"></span>           
            </h1>
        </a>
                </div>
            </div>
        <img src="{{asset ('core/img/active-plan.png')}}" alt="Image" style="height: 100%; width: 100%;" />

    </div>
    <div class="card  graf_content m-0 p-0 mt-4" id="">
        <div class="tab-pane fade show padi" id="tab1" role="tabpanel" aria-labelledby="home-tab-md">
            
            <div class="mt-2">
                
                     <div class="mt-5 text-center">
                           
                            </div>
                
            </div>
        </div>
    </div>
    


                @forelse($invests as $invest)
                    @php
                        if ($invest->last_time) {
                            $start = $invest->last_time;
                        } else {
                            $start = $invest->created_at;
                        }
                    @endphp
                    <div class="plan-item-two">
                        <div class="plan-info plan-inner-div">
                            <div class="d-flex align-items-center gap-3">
                                @if ($invest->status == 1)
                                    <svg class="custom-progress">
                                        <circle class="progress-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: calc(100 - (({{ diffDatePercent($start, $invest->next_time) }} * 100)/100))" ; />
                                        <circle class="bg-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: 0"; />
                                    </svg>
                                @else
                                    <span class="closed-invest">
                                        <i class="las la-ban text-danger"></i>
                                        <span>@lang('Closed')</span>
                                    </span>
                                @endif

                                <div class="plan-name-data">
                                    <div class="plan-name fw-bold">{{ __($invest->plan->name) }} - @lang('Every') {{ __($invest->time_name) }} {{ $invest->plan->interest_type != 1 ? $general->cur_sym : '' }}{{ showAmount($invest->plan->interest) }}{{ $invest->plan->interest_type == 1 ? '%' : '' }}
                                        @lang('for') @if ($invest->plan->lifetime == 0)
                                            {{ __($invest->plan->repeat_time) }} {{ __($invest->plan->time_name) }}
                                        @else
                                            @lang('LIFETIME')
                                        @endif
                                    </div>
                                    <div class="plan-desc">@lang('Invested'): <span class="fw-bold">{{ showAmount($invest->amount) }} {{ $general->cur_text }} @if ($invest->capital_status)
                                                <small class="capital-back"><i>(@lang('Capital will be back'))</i></small>
                                            @endif </span></div>
                                </div>
                            </div>
                        </div>
                        <div class="plan-start plan-inner-div">
                            <p class="plan-label">@lang('Start Date')</p>
                            <p class="plan-value date">{{ showDateTime($invest->created_at, 'M d, Y h:i A') }}</p>
                        </div>
                        <div class="plan-inner-div">
                            <p class="plan-label">@lang('Next Return')</p>
                            <p class="plan-value">{{ showDateTime($invest->next_time, 'M d, Y h:i A') }}</p>
                        </div>
                        <div class="plan-inner-div text-end">
                            <p class="plan-label">@lang('Total Return')</p>
                            <p class="plan-value amount"> {{ $general->cur_sym }}{{ showAmount($invest->interest) }} x {{ $invest->return_rec_time }} = {{ showAmount($invest->paid) }} {{ $general->cur_text }}</p>
                        </div>
                    </div>
                @empty
                    <div class="accordion-body text-center bg-white p-4">
                <img src="{{asset ('core/img/no-record.png')}}" alt="" width="230px">
                    </div>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $invests->links() }}
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .custom-progress {
            max-width: 40px !important;
            max-height: 40px;
            transform: rotate(-90deg);
        }

        .custom-progress .bg-circle {
            stroke: #00000011;
            fill: none;
            stroke-width: 4px;
            position: relative;
            z-index: -1;
        }

        .custom-progress .progress-circle {
            fill: none;
            stroke: hsl(var(--base));
            stroke-width: 4px;
            z-index: 11;
            position: absolute;
        }

        .expired-time-circle {
            position: relative;
            border: none !important;
            height: 38px;
            width: 38px;
            margin-right: 7px;
        }

        .expired-time-circle::before {
            position: absolute;
            content: '';
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid #dbdce1;
        }

        .expired-time-circle.danger-border .animation-circle {
            border-color: hsl(var(--base)) !important;
        }

        .animation-circle {
            position: absolute;
            top: 0;
            left: 0;
            border: 4px solid hsl(var(--base));
            height: 100%;
            width: 100%;
            border-radius: 150px;
            transform: rotateY(180deg);
            animation-name: clipCircle;
            animation-iteration-count: 1;
            animation-timing-function: cubic-bezier(0, 0, 1, 1);
            z-index: 1;
        }

        .account-wrapper .left .top {
            margin-top: 0;
        }

        .account-wrapper .left,
        .account-wrapper .right {
            width: 100%;
        }

        .account-wrapper .right {
            padding-left: 0;
            margin-top: 35px;
        }

        @keyframes clipCircle {
            0% {
                clip-path: polygon(50% 50%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%);
                /* center, top-center*/
            }

            12.5% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%);
                /* center, top-center, top-left*/
            }

            25% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%, 0% 50%);
                /* center, top-center, top-left, left-center*/
            }

            37.5% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 0% 100%, 0% 100%, 0% 100%, 0% 100%, 0% 100%);
                /* center, top-center, top-left, left-center, bottom-left*/
            }

            50% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%);
                /* center, top-center, top-left, left-center, bottom-left, bottom-center*/
            }

            62.5% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 100%, 100% 100%, 100% 100%);
                /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right*/
            }

            75% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 50%, 100% 50%);
                /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center*/
            }

            87.5% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 0%, 100% 0%);
                /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center top-right*/
            }

            100% {
                clip-path: polygon(50% 50%, 50% 0%, 0% 0%, 0% 50%, 0% 100%, 50% 100%, 100% 100%, 100% 50%, 100% 0%, 50% 0%);
                /* center, top-center, top-left, left-center, bottom-left, bottom-center, bottom-right, right-center top-right, top-center*/
            }
        }

        .capital-back {
            font-size: 10px;
        }

        .closed-invest {
            max-width: 40px !important;
            max-height: 40px;
            text-align: center;
        }
    </style>
@endpush

@push('script')
    <script>
        let animationCircle = $('.animation-circle');
        animationCircle.css('animation-duration', function() {
            let duration = ($(this).data('duration'));
            return duration;
        });
    </script>
@endpush
