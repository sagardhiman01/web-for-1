@extends($activeTemplate.'layouts.master')
@section('content')




<style>
         .heading_label {
            background-color: #3F3F3F;
            border-radius: 0px 0px 0px 47px;
        }
       .recharge-plan {
        position: absolute;
        width: -webkit-fill-available;
        }
        .plan-main-heading{
            font-size: 12px;
            font-weight: 700;
            color: #3F3F3F;
        }
        .sub-heading{
            font-size: 12px;
            font-weight: 300;
            color: #3F3F3FB2;
        }
        .underline {
            border-bottom: dashed;
            opacity: 0.5; 
            padding: 3px;
        }
        .desposit_history_plan{
            box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.18);
            border-radius: 10px;
        }
</style>
<body>
    <div class="text-center pt-4">
        <span style="font-size:16px; font-weight: 500; color: #000000; font-family: Gotham;">Withdraw Record</span>
    </div>
    <div class="container">
                    @forelse($withdraws as $withdraw)
            
                <div class="recharge-plan mt-3 px-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="plan-main-heading">withdraw Status</span>
                        </div>
                        <div class="pe-2">
                            
                                      @php echo $withdraw->statusBadge @endphp
                            
                        </div>
                    </div>
                    <div class="underline me-2"></div>
                    <div class="d-flex justify-content-between">
                        <div class="withdraw-label">
                            <span class="sub-heading">Withdraw Amount </span >
                        </div>
                        <div>
                            <span class="sub-heading pe-2">{{ showAmount($withdraw->amount ) }} {{ $general->cur_text }}</span>
                        </div>
                    </div>
                
                    <div class="underline me-2"></div>
                
                    <div class="d-flex justify-content-between">
                        <div class="order-label">
                            <span class="sub-heading">Order Time</span>
                        </div>
                        <div class="date_time ">
                            <span class="sub-heading pe-2">{{showDateTime($withdraw->created_at,'M d Y @g:i:a')}}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <img src="{{asset ('core/img/re-record-div-bg.png')}}" style="width: 100%;" alt="">
                </div>
            
        
            @empty
                <div class="accordion-body text-center bg-white">
                    <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                </div>
            @endforelse


    </div>










@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.detailBtn').on('click', function () {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if(element.type != 'file'){
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if($(this).data('admin_feedback') != undefined){
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                }else{
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush
