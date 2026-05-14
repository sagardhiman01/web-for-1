@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="dashboard-section pt-120 pb-120" style="background: url('{{ asset('assets/images/frontend/breadcrumb/bg.jpg') }}') center/cover no-repeat; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-10 text-center">
                    <h2 class="mb-3" style="color: #fff; text-shadow: 0 0 10px rgba(79, 209, 197, 0.5);">@lang('Treasure Marketplace')</h2>
                    <p style="color: #a0aec0; font-size: 1.1rem;">Discover, Reserve, and Trade premium NFT collections. Algorithmic pricing guarantees liquidity.</p>
                </div>
            </div>
            <!-- MY NFTS & RESERVATIONS SECTION -->
            @if(isset($myNfts) && $myNfts->count() > 0)
            <div class="row mb-3 mt-4">
                <div class="col-12">
                    <h4 style="color: #fff; border-left: 4px solid #4FD1C5; padding-left: 15px;">@lang('My Owned NFTs')</h4>
                </div>
            </div>
            <div class="row mb-5">
                @foreach($myNfts as $myNft)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(79, 209, 197, 0.5); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px);">
                            <div class="nft-img-wrapper" style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                                @php $imgFile = $userImages[$myNft->id] ?? $myNft->image; @endphp
                                <img src="{{ asset('assets/images/nft/' . $imgFile) }}?v={{ time() }}" alt="{{ $myNft->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 15px; right: 15px;">
                                    <span style="background: rgba(104,211,145,0.8); color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #68D391;">@lang('Owned')</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">{{ __($myNft->name) }}</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p style="color: #a0aec0; font-size: 0.85rem; margin-bottom: 2px;">@lang('Value')</p>
                                        <h6 style="color: #4FD1C5; font-size: 1.1rem; margin: 0;">{{ $general->cur_sym }}{{ showAmount($myNft->current_price) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            @if(isset($myReservations) && $myReservations->count() > 0)
            <div class="row mb-3 mt-4">
                <div class="col-12">
                    <h4 style="color: #fff; border-left: 4px solid #F6E05E; padding-left: 15px;">@lang('My Reservations') (Pending)</h4>
                </div>
            </div>
            <div class="row mb-5">
                @foreach($myReservations as $reservation)
                    @php $resNft = $reservation->nft; @endphp
                    @if($resNft)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(246, 224, 94, 0.5); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px);">
                            <div class="nft-img-wrapper" style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                                @php $imgFile = $userImages[$resNft->id] ?? $resNft->image; @endphp
                                <img src="{{ asset('assets/images/nft/' . $imgFile) }}?v={{ time() }}" alt="{{ $resNft->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="position: absolute; top: 15px; right: 15px;">
                                    <span style="background: rgba(246,224,94,0.8); color: #000; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #F6E05E; font-weight: bold;">@lang('Reserved')</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">{{ __($resNft->name) }}</h5>
                                <p style="color: #a0aec0; font-size: 0.85rem; margin-bottom: 2px;">@lang('Amount Paid')</p>
                                <h6 style="color: #F6E05E; font-size: 1.1rem; margin: 0;">{{ $general->cur_sym }}{{ showAmount($reservation->amount) }}</h6>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            <div class="row mb-3 mt-4">
                <div class="col-12">
                    <h4 style="color: #fff; border-left: 4px solid #fff; padding-left: 15px;">@lang('Available For Purchase')</h4>
                </div>
            </div>

            <div class="row mb-5">
                @forelse($nfts as $nft)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="nft-card" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            
                            <!-- NFT Image -->
                            <div class="nft-img-wrapper" style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                                @php $imgFile = $userImages[$nft->id] ?? $nft->image; @endphp
                                <img src="{{ asset('assets/images/nft/' . $imgFile) }}?v={{ time() }}" alt="{{ $nft->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                <div style="position: absolute; top: 15px; left: 15px;">
                                    <span style="background: rgba(0,0,0,0.6); color: #4FD1C5; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; border: 1px solid #4FD1C5; backdrop-filter: blur(4px);">VIP {{ $nft->level_id ?? 1 }}</span>
                                </div>
                            </div>
                            
                            <!-- NFT Details -->
                            <div class="p-4">
                                <h5 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin-bottom: 15px;">{{ __($nft->name) }}</h5>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <p style="color: #a0aec0; font-size: 0.85rem; margin-bottom: 2px;">@lang('Current Price')</p>
                                        <h6 style="color: #4FD1C5; font-size: 1.1rem; margin: 0;">{{ $general->cur_sym }}{{ showAmount($nft->current_price) }}</h6>
                                    </div>
                                    <div class="text-right">
                                        <p style="color: #a0aec0; font-size: 0.85rem; margin-bottom: 2px;">@lang('Daily Yield')</p>
                                        <h6 style="color: #68D391; font-size: 1.1rem; margin: 0;">~5.0%</h6>
                                    </div>
                                </div>
                                
                                <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 10px; margin-bottom: 20px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color: #cbd5e0; font-size: 0.9rem;">@lang('Reserve Fee (5%)')</span>
                                        <span style="color: #F6E05E; font-weight: 600;">{{ $general->cur_sym }}{{ showAmount($nft->current_price * 0.05) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                @if($nft->status == 'available')
                                    <form action="{{ route('user.nft.reserve', $nft->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="width: 100%; padding: 12px; border-radius: 12px; background: linear-gradient(135deg, #4FD1C5 0%, #3182CE 100%); color: white; border: none; font-weight: 600; letter-spacing: 0.5px; transition: opacity 0.3s ease;">
                                            @lang('Reserve Now')
                                        </button>
                                    </form>
                                @else
                                    <button disabled style="width: 100%; padding: 12px; border-radius: 12px; background: rgba(255,255,255,0.1); color: #a0aec0; border: none; font-weight: 600; cursor: not-allowed;">
                                        @lang('Currently Reserved')
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12 text-center">
                        <div class="alert" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2);">
                            @lang('No NFTs available for reservation at the moment.')
                        </div>
                    </div>
                @endforelse
            </div>
            
            @if($nfts->hasPages())
                <div class="row">
                    <div class="col-md-12">
                        {{ paginateLinks($nfts) }}
                    </div>
                </div>
            @endif
        </div>
    </section>

    @push('style')
    <style>
        .nft-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(79, 209, 197, 0.2) !important;
        }
        .nft-card:hover .nft-img-wrapper img {
            transform: scale(1.05);
        }
        button[type="submit"]:hover {
            opacity: 0.9;
        }
    </style>
    @endpush
@endsection

