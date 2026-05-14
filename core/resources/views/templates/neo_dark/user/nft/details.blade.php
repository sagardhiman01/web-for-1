@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="dashboard-section pt-120 pb-120" style="background: url('{{ asset('assets/images/frontend/breadcrumb/bg.jpg') }}') center/cover no-repeat; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('user.nft.collection') }}" class="btn btn-sm btn--base outline me-3" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-arrow-left"></i>
                        </a>
                        <h3 class="text-white mb-0">@lang('Performance Analytics'): {{ __($nft->name) }}</h3>
                    </div>

                    <div class="row">
                        <!-- Left side: NFT Card & Stats -->
                        <div class="col-lg-4 mb-4">
                            <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(79, 209, 197, 0.3); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px);">
                                <div style="width: 100%; height: 250px; overflow: hidden;">
                                    <img src="{{ asset('assets/images/nft/' . $nft->image) }}" alt="{{ $nft->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="p-4">
                                    <h4 class="text-white mb-3">{{ __($nft->name) }}</h4>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">@lang('Level')</span>
                                        <span class="badge badge--primary" style="background: #4FD1C5;">Tier {{ $nft->level_id }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">@lang('Current Value')</span>
                                        <span class="text-white fw-bold">{{ $general->cur_sym }}{{ showAmount($nft->current_price) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">@lang('Status')</span>
                                        <span class="text--success small"><i class="las la-sync-alt fa-spin"></i> @lang('Active Trading')</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-4" style="background: rgba(79, 209, 197, 0.1); border: 1px solid rgba(79, 209, 197, 0.2); border-radius: 20px;">
                                <h6 class="text-white mb-3"><i class="las la-calculator"></i> @lang('Profit Configuration')</h6>
                                <p class="text-muted small mb-3">Your NFT is part of our Quantitative AI trading pool. It generates daily returns based on market volatility.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">@lang('Estimated Daily Rate')</span>
                                    @php
                                        $rate = 2.0;
                                        if ($nft->level_id == 2) $rate = 2.5;
                                        elseif ($nft->level_id == 3) $rate = 3.0;
                                        elseif ($nft->level_id == 4) $rate = 3.5;
                                        elseif ($nft->level_id >= 5) $rate = 4.0;
                                    @endphp
                                    <span class="text--success fw-bold">{{ $rate }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right side: Charts & Logs -->
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="stat-item" style="background: rgba(104, 211, 145, 0.1); border: 1px solid rgba(104, 211, 145, 0.2); padding: 25px; border-radius: 20px; height: 100%;">
                                        <span class="text-muted d-block mb-1">@lang('Total Profit Earned')</span>
                                        <h2 class="text--success mb-0">{{ $general->cur_sym }}{{ showAmount($totalProfit) }}</h2>
                                        <div class="mt-2 small text-muted">
                                            <i class="las la-arrow-up text--success"></i> @lang('Growing daily')
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="stat-item" style="background: rgba(246, 224, 94, 0.1); border: 1px solid rgba(246, 224, 94, 0.2); padding: 25px; border-radius: 20px; height: 100%;">
                                        <span class="text-muted d-block mb-1">@lang('Next Payout')</span>
                                        <h2 class="text--warning mb-0">{{ $general->cur_sym }}{{ showAmount(($nft->current_price * $rate) / 100) }}</h2>
                                        <div class="mt-2 small text-muted">
                                            <i class="las la-clock"></i> @lang('Processing in AI Pool')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profit History Table -->
                            <div class="card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; backdrop-filter: blur(10px);">
                                <div class="card-header" style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 20px;">
                                    <h5 class="text-white mb-0">@lang('Recent Profit Logs')</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table style--two" style="margin-bottom: 0;">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Date')</th>
                                                    <th>@lang('Transaction ID')</th>
                                                    <th>@lang('Profit Amount')</th>
                                                    <th>@lang('Wallet')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentProfits as $profit)
                                                    <tr>
                                                        <td>{{ showDateTime($profit->created_at) }}</td>
                                                        <td><span class="text--base">{{ $profit->trx }}</span></td>
                                                        <td class="fw-bold text--success">+{{ $general->cur_sym }}{{ showAmount($profit->amount) }}</td>
                                                        <td><span class="badge badge--success">@lang('Interest Wallet')</span></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="100%" class="text-center p-4">
                                                            <div class="text-muted">
                                                                <i class="las la-info-circle" style="font-size: 2rem;"></i>
                                                                <p class="mt-2">@lang('No profit logs found yet. Profits are generated every 24 hours.')</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
