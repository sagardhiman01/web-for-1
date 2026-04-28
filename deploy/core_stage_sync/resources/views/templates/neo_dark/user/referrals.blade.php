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

                <div class="mt-4">
                    <h5 class="mb-3">@lang('Direct Referral Details')</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Referral Code')</th>
                                    <th>@lang('Email')</th>
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
                                        <td>{{ $referralUser->email }}</td>
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
                                        <td colspan="6" class="text-center">@lang('No referred users found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ paginateLinks($directReferrals) }}
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
