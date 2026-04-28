@auth
@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp
<div class="user-sidebar style--xl">
    <button type="button" class="dashboard-side-menu-close"><i class="las la-window-close"></i></button>
    <div class="text-center mb-3 px-3">
        <img src="{{ getImage(getFilePath('userProfile').'/'.(auth()->user()->image ?: 'avatar-default-01.jpg'), getFileSize('userProfile')) }}" alt="@lang('Profile Photo')" style="width:72px;height:72px;object-fit:cover;border-radius:50%;border:2px solid rgba(255,153,51,.45);">
        <div class="text-white mt-2 fw-bold">{{ auth()->user()->fullname }}</div>
    </div>
    <ul class="user-sidebar-menu">
        <li><a href="{{route('user.home')}}" class="{{ menuActive('user.home') }}"> <i class="las la-tachometer-alt pr-1"></i>  @lang('Dashboard')</a></li>
        <li><a href="{{route('plan')}}" class="{{ menuActive(['plan', 'user.invest.log','user.invest.statistics']) }}"> <i class="las la-cubes pr-1"></i>  @lang('Investment')</a></li>
        <li><a href="{{route('user.deposit.index')}}" class="{{ menuActive(['user.deposit','user.deposit.history']) }}"><i class="las la-credit-card pr-1" aria-hidden="true"></i>
                 @lang('Deposit')</a></li>

        <li><a href="{{route('user.withdraw')}}" class="{{ menuActive('user.withdraw') }}"><i class="las la-money-bill-wave pr-1"></i>
                @lang('Withdraw')</a></li>
        <li><a href="{{route('user.withdraw.address')}}" class="{{ menuActive('user.withdraw.address') }}"><i class="las la-wallet pr-1"></i>
                @lang('Wallet Address')</a></li>

        @if($general->b_transfer)
            <li><a href="{{route('user.transfer.balance')}}" class="{{ menuActive('user.transfer.balance') }}"> <i class="las la-dollar-sign"></i>@lang('Transfer Balance')</a></li>
        @endif
        <li><a href="{{route('user.transactions')}}" class="{{ menuActive('user.transactions') }}"><i class="las la-exchange-alt pr-1"></i> @lang('Transaction Log')</a></li>
        <li><a href="{{route('user.currency.list')}}" class="{{ menuActive('user.currency.list') }}"><i class="las la-coins pr-1"></i> @lang('Currency List')</a></li>
        <li><a href="{{route('user.referrals')}}" class="{{ menuActive('user.referrals') }}"><i class="las la-users pr-1"></i> @lang('Referred Users')</a></li>
        @if($general->promotional_tool && $promotionCount)
            <li><a href="{{route('user.promotional.banner')}}" class="{{ menuActive('user.promotional.banner') }}"><i class="las la-ad pr-1"></i> @lang('Promotional Banner')</a></li>
        @endif
        <li><a href="{{route('ticket.index')}}" class="{{ menuActive('ticket') }}"><i class="las la-ticket-alt pr-1" ></i>@lang('Support Ticket')</a></li>
        <li><a href="{{route('user.twofactor')}}" class="{{ menuActive('user.twofactor') }}"><i class="las la-user-secret pr-1" ></i> @lang('2FA Security')</a></li>
        <li><a href="{{route('user.profile.setting')}}" class="{{ menuActive('user.profile.setting') }}"> <i class="las la-user pr-1"></i>@lang('Profile Setting')</a></li>
        <li><a href="{{route('user.change.password')}}" class="{{ menuActive('user.change.password') }}"><i class="las la-unlock-alt pr-1" ></i>@lang('Change Password')</a></li>
        <li><a href="{{ route('user.logout') }}" class="{{ menuActive('user.logout') }}"> <i class="las la-sign-out-alt pr-1"></i> {{ __('Logout') }}</a></li>
    </ul>
</div><!-- user-sidebar end -->
@endauth
