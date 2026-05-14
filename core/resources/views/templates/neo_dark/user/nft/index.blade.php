@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="dashboard-section pt-120 pb-120" style="background: url('{{ asset('assets/images/frontend/breadcrumb/bg.jpg') }}') center/cover no-repeat; min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-10 text-center">
                    <h2 class="mb-3" style="color: #fff; text-shadow: 0 0 10px rgba(79, 209, 197, 0.5); font-weight: 700;">@lang('Welcome to Treasure NFT Trading')</h2>
                    <p class="mx-auto" style="color: #a0aec0; font-size: 1.1rem; max-width: 800px;">
                        The ultimate Web3 Algorithmic Trading Platform. Reserve exclusive digital assets, deploy capital, and let our AI algorithm generate guaranteed profits within a 24-hour cycle.
                    </p>
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <a href="{{ route('user.nft.marketplace') }}" style="display: inline-block; padding: 12px 30px; border-radius: 30px; background: linear-gradient(135deg, #4FD1C5 0%, #3182CE 100%); color: white; font-weight: 600; text-decoration: none; margin: 0 10px; transition: transform 0.3s ease; box-shadow: 0 4px 15px rgba(79, 209, 197, 0.4);" onmouseover="this.style.transform='translateY(-3px)';" onmouseout="this.style.transform='translateY(0)';">@lang('Enter Marketplace')</a>
                        
                        <a href="{{ route('user.nft.collection') }}" style="display: inline-block; padding: 12px 30px; border-radius: 30px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; font-weight: 600; text-decoration: none; margin: 0 10px; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)';" onmouseout="this.style.background='rgba(255,255,255,0.05)';">@lang('My Collection')</a>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="step-card text-center p-5" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(79, 209, 197, 0.3); border-radius: 20px; backdrop-filter: blur(10px); height: 100%; transition: transform 0.3s ease;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(79, 209, 197, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="las la-mouse-pointer" style="font-size: 2.5rem; color: #4FD1C5;"></i>
                        </div>
                        <h4 style="color: #fff; margin-bottom: 15px;">1. @lang('Reserve')</h4>
                        <p style="color: #a0aec0; font-size: 0.95rem;">@lang('Pay a small 5% fee to lock in a profitable NFT strategy before others take it.')</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="step-card text-center p-5" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(246, 224, 94, 0.3); border-radius: 20px; backdrop-filter: blur(10px); height: 100%; transition: transform 0.3s ease;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(246, 224, 94, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="las la-wallet" style="font-size: 2.5rem; color: #F6E05E;"></i>
                        </div>
                        <h4 style="color: #fff; margin-bottom: 15px;">2. @lang('Deploy')</h4>
                        <p style="color: #a0aec0; font-size: 0.95rem;">@lang('Fund the NFT with the required capital. Your digital asset is now actively trading.')</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="step-card text-center p-5" style="background: rgba(17, 24, 39, 0.7); border: 1px solid rgba(104, 211, 145, 0.3); border-radius: 20px; backdrop-filter: blur(10px); height: 100%; transition: transform 0.3s ease;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(104, 211, 145, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="las la-hand-holding-usd" style="font-size: 2.5rem; color: #68D391;"></i>
                        </div>
                        <h4 style="color: #fff; margin-bottom: 15px;">3. @lang('Earn')</h4>
                        <p style="color: #a0aec0; font-size: 0.95rem;">@lang('After 24h, the AI automatically sells the NFT, returning your capital + guaranteed profit.')</p>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    @push('style')
    <style>
        .step-card:hover {
            transform: translateY(-10px);
        }
    </style>
    @endpush
@endsection

