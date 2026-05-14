@extends($activeTemplate.'layouts.master')
@section('content')
<div class="pt-150 pb-150">
    <div class="container">
        <div class="card card-bg">
            <div class="card-body">
                @if(auth()->user()->referrer)
                    <h4 class="mb-2">@lang('You are referred by') {{ auth()->user()->referrer->fullname }}</h4>
                @endif
                <div class="form-group mb-4">
                    <label>@lang('Referral Link') <span>★</span></label>
                    <div class="input-group">
                        <input type="text" name="text" class="form-control form--control referralURL" value="{{ route('home') }}?ref={{ auth()->user()->referral_code }}" readonly="">
                        <button class="input-group-text copytext copyBoard" id="copyBoard"> <i class="fa fa-copy"></i> </button>
                    </div>
                </div>
                @if($user->allReferrals->count() > 0 && $maxLevel > 0)
                <div class="treeview-container">
                    <ul class="treeview">
                      <li class="items-expanded"> {{ $user->fullname }} ( {{ $user->username }} )
                            @include($activeTemplate.'partials.under_tree',['user'=>$user,'layer'=>0,'isFirst'=>true])
                        </li>
                    </ul>
                </div>
                @endif

                <div class="row mt-4 mb-4">
                    <div class="col-md-12">
                        <div class="card" style="background: rgba(104, 211, 145, 0.1); border: 1px solid rgba(104, 211, 145, 0.3); color: #fff;">
                            <h5 style="color: #68D391; margin-bottom: 10px;"><i class="las la-info-circle"></i> @lang('Referral Earnings & Code Income')</h5>
                            <p style="margin-bottom: 5px;"><strong>@lang('Direct Referrals (Level 1):')</strong> @lang('When your direct referral makes a deposit, you earn a') <span style="color: #F6E05E; font-weight: bold;">10% Commission</span>. @lang('Plus, they receive a') <span style="color: #68D391; font-weight: bold;">5% Self-Bonus</span> @lang('on their deposit amount!')</p>
                            <p class="mb-0"><strong>@lang('Indirect Referrals (Level 2+) - Code Income:')</strong> @lang('When users referred by your direct referrals make a deposit, you earn a') <span style="color: #F6E05E; font-weight: bold;">2% Code Income Commission</span>.</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-md-12">
                        <div class="card" style="background: rgba(0,0,0,0.3); border: 1px solid #4FD1C5;">
                            <div class="card-body">
                                <h5 style="color: #4FD1C5; margin-bottom: 15px;"><i class="las la-wallet"></i> @lang('Referral Bonus Transfer')</h5>
                                <p>@lang('Your Current Referral Bonus Balance:'): <strong style="color: #F6E05E; font-size: 1.2rem;">{{ showAmount(auth()->user()->referral_bonus) }} {{ gs()->cur_text }}</strong></p>
                                
                                <form action="{{ route('user.referral.transfer') }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5 form-group">
                                            <label>@lang('Amount to Transfer')</label>
                                            <div class="input-group">
                                                <input type="number" step="any" name="amount" class="form-control form--control" placeholder="0.00" required>
                                                <span class="input-group-text">{{ gs()->cur_text }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>@lang('Transfer To')</label>
                                            <select name="wallet" class="form-control form--control" required>
                                                <option value="deposit_wallet">@lang('Deposit Wallet')</option>
                                                <option value="interest_wallet">@lang('Interest Wallet')</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group d-flex align-items-end">
                                            <button type="submit" class="btn btn--base w-100">@lang('Transfer')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mt-4">
                    <h5 class="mb-3" style="color: #F6E05E;">@lang('Direct Referrals (Level 1)')</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" style="background: rgba(0,0,0,0.2);">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Referral Code')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($directReferrals as $referralUser)
                                    @php $fullName = trim(($referralUser->firstname ?? '').' '.($referralUser->lastname ?? '')); @endphp
                                    <tr>
                                        <td>{{ $fullName !== '' ? $fullName : __('N/A') }}</td>
                                        <td>{{ $referralUser->username }}</td>
                                        <td>{{ $referralUser->referral_code }}</td>
                                        <td>{{ showDateTime($referralUser->created_at) }}</td>
                                        <td>
                                            @if($referralUser->status)
                                                <span class="badge bg-success">@lang('Active')</span>
                                            @else
                                                <span class="badge bg-danger">@lang('Banned')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('No direct referrals found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ paginateLinks($directReferrals) }}
                </div>

                <div class="mt-5">
                    <h5 class="mb-3" style="color: #68D391;">@lang('Indirect Referrals (Level 2+)')</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" style="background: rgba(0,0,0,0.2);">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Referral Code')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($indirectReferrals as $referralUser)
                                    @php $fullName = trim(($referralUser->firstname ?? '').' '.($referralUser->lastname ?? '')); @endphp
                                    <tr>
                                        <td>{{ $fullName !== '' ? $fullName : __('N/A') }}</td>
                                        <td>{{ $referralUser->username }}</td>
                                        <td>{{ $referralUser->referral_code }}</td>
                                        <td>{{ showDateTime($referralUser->created_at) }}</td>
                                        <td>
                                            @if($referralUser->status)
                                                <span class="badge bg-success">@lang('Active')</span>
                                            @else
                                                <span class="badge bg-danger">@lang('Banned')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('No indirect referrals found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ paginateLinks($indirectReferrals) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>

</style>
@endpush


@push('style-lib')
    <link href="{{ asset('assets/global/css/jquery.treeView.css') }}" rel="stylesheet" type="text/css">
@endpush


@push('script')
<script src="{{ asset('assets/global/js/jquery.treeView.js') }}"></script>
<script>
    (function($){
    "use strict"
        $('.treeview').treeView();
        $('.copyBoard').click(function(){
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);

                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
        });
    })(jQuery);
</script>
@endpush
