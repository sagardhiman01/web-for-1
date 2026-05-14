@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="dashboard-section pt-120 pb-120" style="background: url('{{ asset('assets/images/frontend/breadcrumb/bg.jpg') }}') center/cover no-repeat; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-10 text-center">
                    <h2 class="mb-3" style="color: #fff; text-shadow: 0 0 10px rgba(79, 209, 197, 0.5);">@lang('My Collection & Active Trades')</h2>
                    <p style="color: #a0aec0; font-size: 1.1rem;">Manage your reserved NFTs and monitor your active algorithmic trades.</p>
                    <a href="{{ route('user.nft.marketplace') }}" style="display: inline-block; padding: 10px 25px; border-radius: 30px; background: rgba(79, 209, 197, 0.2); color: #4FD1C5; border: 1px solid #4FD1C5; margin-top: 20px; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='#4FD1C5'; this.style.color='#fff';" onmouseout="this.style.background='rgba(79, 209, 197, 0.2)'; this.style.color='#4FD1C5';">@lang('Explore Marketplace')</a>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <h4 style="color: #fff; border-left: 4px solid #F6E05E; padding-left: 15px;">@lang('Reserved NFTs') (Pending Start)</h4>
                </div>
                
                @php
                    $reservations = App\Models\NftReservation::where('user_id', auth()->id())->where('status', 'pending')->with('nft')->get();
                    
                    // Per-user unique images for collection page
                    $imagePool = [
                        'opensea_bayc_1.png', 'opensea_bayc_2.png', 'opensea_bayc_3.png', 'opensea_bayc_4.png', 'opensea_bayc_5.png',
                        'opensea_bayc_6.png', 'opensea_bayc_7.png', 'opensea_bayc_8.png', 'opensea_bayc_9.png', 'opensea_bayc_10.png',
                        'cyber_ape.png', 'meta_samurai.png', 'neon_punk.png', 'treasure_genesis.png',
                        'nft_1.png', 'nft_2.png',
                        'unique_nft_1.jpg', 'unique_nft_2.jpg', 'unique_nft_3.jpg', 'unique_nft_4.jpg', 'unique_nft_5.jpg',
                        'unique_nft_6.jpg', 'unique_nft_7.jpg', 'unique_nft_8.jpg', 'unique_nft_9.jpg', 'unique_nft_10.jpg',
                        'unique_nft_11.jpg', 'unique_nft_12.jpg', 'unique_nft_13.jpg', 'unique_nft_14.jpg', 'unique_nft_15.jpg',
                        'unique_nft_16.jpg', 'unique_nft_17.jpg', 'unique_nft_18.jpg', 'unique_nft_19.jpg', 'unique_nft_20.jpg',
                        'pool_1.png', 'pool_2.png', 'pool_3.png', 'pool_4.png', 'pool_5.png',
                        'pool_6.png', 'pool_7.png', 'pool_8.png', 'pool_9.png', 'pool_10.png',
                        'pool_11.png', 'pool_12.png', 'pool_13.png', 'pool_14.png', 'pool_15.png'
                    ];
                    if (empty($imagePool)) $imagePool = ['nft_1.png'];
                    $userSeed = crc32('nft_seed_' . auth()->id());
                    mt_srand($userSeed);
                    for ($i = count($imagePool) - 1; $i > 0; $i--) {
                        $j = mt_rand(0, $i);
                        [$imagePool[$i], $imagePool[$j]] = [$imagePool[$j], $imagePool[$i]];
                    }
                    mt_srand();
                    
                    $collectionImages = [];
                    $allNftIds = $reservations->pluck('nft.id')->merge($myNfts->pluck('id'))->unique()->values();
                    foreach ($allNftIds as $nftId) {
                        $collectionImages[$nftId] = $imagePool[$nftId % count($imagePool)];
                    }
                @endphp
                
                @forelse($reservations as $reservation)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(246, 224, 94, 0.3); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            
                            <div class="nft-img-wrapper" style="position: relative; width: 100%; height: 200px; overflow: hidden;">
                                <img src="{{ asset('assets/images/nft/' . ($collectionImages[$reservation->nft->id] ?? $reservation->nft->image)) }}" alt="{{ $reservation->nft->name }}" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7);">
                                <div style="position: absolute; top: 15px; left: 15px;">
                                    <span style="background: rgba(0,0,0,0.6); color: #F6E05E; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #F6E05E; backdrop-filter: blur(4px);">
                                        <i class="las la-clock"></i> @lang('Reserved')
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h5 style="color: #fff; font-size: 1.1rem; font-weight: 600; margin-bottom: 5px;">{{ __($reservation->nft->name) }}</h5>
                                <p style="color: #a0aec0; font-size: 0.8rem; margin-bottom: 15px;">@lang('Expires'): <span class="js-local-time text-white" data-time-ms="{{ $reservation->expires_at->timestamp * 1000 }}">{{ $reservation->expires_at }}</span></p>
                                
                                <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 10px; margin-bottom: 20px;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span style="color: #cbd5e0; font-size: 0.85rem;">@lang('Purchase Price')</span>
                                        <span style="color: #fff; font-weight: 600;">{{ $general->cur_sym }}{{ showAmount($reservation->nft->current_price) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #cbd5e0; font-size: 0.85rem;">@lang('Est. Profit')</span>
                                        <span style="color: #68D391; font-weight: 600;">~{{ $general->cur_sym }}{{ showAmount($reservation->nft->current_price * 0.05) }}</span>
                                    </div>
                                </div>
                                
                                <form action="{{ route('user.nft.buy', $reservation->nft->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="width: 100%; padding: 12px; border-radius: 12px; background: linear-gradient(135deg, #F6E05E 0%, #D69E2E 100%); color: #1a202c; border: none; font-weight: 700; letter-spacing: 0.5px; transition: opacity 0.3s ease;">
                                        @lang('Buy & Start Cycle')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12 text-center mb-5">
                        <div class="alert" style="background: rgba(255,255,255,0.05); color: #a0aec0; border: 1px dashed rgba(255,255,255,0.2);">
                            @lang('You have no pending reservations. Explore the marketplace to reserve an NFT.')
                        </div>
                    </div>
                @endforelse
                
                <div class="col-12 mb-4 mt-4">
                    <h4 style="color: #fff; border-left: 4px solid #68D391; padding-left: 15px;">@lang('Active Trading Cycles')</h4>
                </div>

                @forelse($myNfts as $nft)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(104, 211, 145, 0.3); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            
                            <div class="nft-img-wrapper" style="position: relative; width: 100%; height: 200px; overflow: hidden;">
                                <img src="{{ asset('assets/images/nft/' . ($collectionImages[$nft->id] ?? $nft->image)) }}" alt="{{ $nft->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 15px; left: 15px;">
                                    <span style="background: rgba(0,0,0,0.6); color: #68D391; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #68D391; backdrop-filter: blur(4px);">
                                        <i class="las la-sync-alt fa-spin"></i> @lang('Trading Active')
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h5 style="color: #fff; font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;">{{ __($nft->name) }}</h5>
                                
                                <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 10px; margin-bottom: 20px;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span style="color: #cbd5e0; font-size: 0.85rem;">@lang('Deployed Capital')</span>
                                        <span style="color: #fff; font-weight: 600;">{{ $general->cur_sym }}{{ showAmount($nft->current_price) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #cbd5e0; font-size: 0.85rem;">@lang('Total Profit Earned')</span>
                                        <span style="color: #68D391; font-weight: 600;">{{ $general->cur_sym }}{{ showAmount($nft->total_profit) }}</span>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px; border-radius: 10px; background: rgba(255,255,255,0.1);">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%; background: linear-gradient(90deg, #4FD1C5, #68D391);"></div>
                                </div>
                                
                                <a href="{{ route('user.nft.details', $nft->id) }}" class="btn btn--base w-100" style="padding: 10px; border-radius: 10px; font-weight: 600;">
                                    <i class="las la-chart-bar"></i> @lang('View Profit Details')
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12 text-center">
                        <div class="alert" style="background: rgba(255,255,255,0.05); color: #a0aec0; border: 1px dashed rgba(255,255,255,0.2);">
                            @lang('You have no active trading cycles.')
                        </div>
                    </div>
                @endforelse
            </div>
            
        </div>
    </section>

    @push('style')
    <style>
        .nft-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4) !important;
        }
        button[type="submit"]:hover {
            opacity: 0.9;
        }
    </style>
    @endpush
@endsection
@push('script')
    <script>
        'use strict';
        (function($) {
            function parseTimeMs(value) {
                if (value === null || value === undefined || value === '') return null;
                var num = Number(value);
                if (!Number.isNaN(num)) return num;
                var parsed = new Date(value).getTime();
                return Number.isNaN(parsed) ? null : parsed;
            }

            function formatDateTime(ms) {
                var timestamp = parseTimeMs(ms);
                if (!timestamp) return '--';
                return new Date(timestamp).toLocaleString();
            }

            document.querySelectorAll('.js-local-time').forEach(function(el) {
                var timeMs = el.getAttribute('data-time-ms');
                var local = formatDateTime(timeMs);
                if (local !== '--') {
                    el.textContent = local;
                }
            });
        })(jQuery);
    </script>
@endpush
