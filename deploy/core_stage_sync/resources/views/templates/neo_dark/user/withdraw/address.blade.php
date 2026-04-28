@extends($activeTemplate.'layouts.master')
@section('content')
<section class="pb-150 pt-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="text-end mb-3">
                    <a href="{{ route('user.withdraw') }}" class="btn btn-primary">@lang('Back to Withdraw')</a>
                </div>
                <div class="card card-bg">
                    <div class="card-body">
                        @if($savedWithdrawAddress)
                            <div class="alert alert-primary text-break">
                                <strong>@lang('Current Saved Address'):</strong> {{ $savedWithdrawAddress }}
                            </div>
                        @endif

                        @if($savedWithdrawAddress && !auth()->user()->ts)
                            <div class="alert alert-warning">
                                @lang('To change saved wallet address, please enable 2FA Security first.')
                                <a href="{{ route('user.twofactor') }}" class="text-decoration-underline">@lang('Enable 2FA')</a>
                            </div>
                        @endif

                        <form action="{{ route('user.withdraw.address.update') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('USDT BEP20 Wallet Address')</label>
                                <input type="text" name="wallet_address" value="{{ old('wallet_address', $savedWithdrawAddress) }}" class="form-control form--control" placeholder="0x..." required>
                                <small class="text-muted">@lang('Use only BEP20 network wallet address')</small>
                            </div>

                            @if($savedWithdrawAddress)
                                <div class="form-group">
                                    <label class="form-label">@lang('Google Authenticator Code')</label>
                                    <input type="text" name="authenticator_code" class="form-control form--control" placeholder="@lang('Required only if changing address')">
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-100">
                                {{ $savedWithdrawAddress ? __('Update Address') : __('Save Address') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

