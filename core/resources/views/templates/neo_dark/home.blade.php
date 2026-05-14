@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $bannerCaption = getContent('banner.content',true);
@endphp
<!-- hero-section start -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="hero-content">
                    <h2 class="hero__title">{{__(@$bannerCaption->data_values->heading)}}</h2>
                    <p>{{__(strip_tags(@$bannerCaption->data_values->sub_heading))}}</p>

                    <div class="btn-area">

                        @if(@$bannerCaption->data_values->button_link)
                            <a href="{{@$bannerCaption->data_values->button_link}}" class="btn btn-primary">{{@__($bannerCaption->data_values->button_name)}}</a>
                        @endif
                        @if(@$bannerCaption->data_values->button_two_link)
                            <a href="{{@$bannerCaption->data_values->button_two_link}}" class="btn btn-primary">{{@__($bannerCaption->data_values->button_two_name)}}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-thumb pulse-animation"><img src="{{getImage('assets/images/frontend/banner/'.@$bannerCaption->data_values->image)}}" alt="image"></div>
            </div>
        </div>
    </div>
</section>
<!-- hero-section end -->

<!-- Fundamentals Section Start -->
<section class="fundamentals-section pt-120 pb-120" style="background: var(--navy-dark); position: relative; overflow: hidden; border-top: 1px solid rgba(255, 255, 255, 0.05);">
    <!-- Decorative Elements -->
    <div style="position: absolute; top: -100px; right: -100px; width: 300px; height: 300px; background: rgba(45, 212, 191, 0.05); border-radius: 50%; filter: blur(50px);"></div>
    <div style="position: absolute; bottom: -100px; left: -100px; width: 300px; height: 300px; background: rgba(246, 224, 94, 0.05); border-radius: 50%; filter: blur(50px);"></div>

    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-10 text-center">
                <span style="color: var(--gold); font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Core Architecture & Economics</span>
                <h2 class="mt-2 mb-3" style="color: #fff; font-size: 2.5rem; text-shadow: 0 0 15px rgba(246, 224, 94, 0.2);">Platform Fundamentals</h2>
                <p style="color: var(--text-white); font-size: 1.1rem; max-width: 800px; margin: 0 auto; opacity: 0.9;">
                    We believe in absolute transparency. Understanding how your capital generates yield and how it is protected is the cornerstone of sustainable wealth creation.
                </p>
            </div>
        </div>

        <div class="row gy-5">
            <!-- 1. The Algorithm -->
            <div class="col-lg-6">
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 40px; height: 100%; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(45, 212, 191, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255, 255, 255, 0.05)';">
                    <div style="width: 60px; height: 60px; background: rgba(45, 212, 191, 0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="las la-microchip" style="font-size: 2rem; color: var(--teal);"></i>
                    </div>
                    <h3 style="color: #fff; font-size: 1.5rem; margin-bottom: 15px;">High-Frequency Algorithmic Arbitrage</h3>
                    <p style="color: var(--text-white); font-size: 0.95rem; line-height: 1.7; opacity: 0.85;">
                        The ~5% daily yield is not generated out of thin air. When you acquire an NFT, your capital is deployed into our proprietary AI trading engine. The engine executes thousands of micro-transactions per second across major decentralized and centralized exchanges (DEX/CEX), exploiting minuscule price differences (arbitrage). The profits from these risk-neutral trades are aggregated and distributed to NFT holders daily.
                    </p>
                </div>
            </div>

            <!-- 2. Capital Protection (90% Refund) -->
            <div class="col-lg-6">
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(246, 224, 94, 0.2); border-radius: 20px; padding: 40px; height: 100%; position: relative; overflow: hidden; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                    <!-- Glow effect for emphasis -->
                    <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: rgba(246, 224, 94, 0.1); filter: blur(30px);"></div>
                    
                    <div style="width: 60px; height: 60px; background: rgba(246, 224, 94, 0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="las la-shield-alt" style="font-size: 2rem; color: var(--gold);"></i>
                    </div>
                    <h3 style="color: #fff; font-size: 1.5rem; margin-bottom: 15px; display: flex; align-items: center;">
                        90% Capital Protection Guarantee 
                        <span style="font-size: 0.7rem; background: var(--gold); color: #000; padding: 3px 8px; border-radius: 10px; margin-left: 10px; font-weight: 800;">VERIFIED</span>
                    </h3>
                    <p style="color: var(--text-white); font-size: 0.95rem; line-height: 1.7; opacity: 0.85;">
                        Trading carries inherent market risks, but we take the burden, not you. The company maintains a massive <strong>Treasury Reserve Fund</strong> (Cold Wallet Storage). <strong>If extreme market volatility causes a systemic failure or trading loss, the company will automatically refund 90% of your initial invested capital directly to your wallet from our reserves.</strong> You are insulated from absolute loss. Your downside is strictly capped at 10%, while your upside remains continuous.
                    </p>
                </div>
            </div>

            <!-- 3. Tokenomics & NFT Utility -->
            <div class="col-lg-6">
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 40px; height: 100%; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(45, 212, 191, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255, 255, 255, 0.05)';">
                    <div style="width: 60px; height: 60px; background: rgba(45, 212, 191, 0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="las la-cubes" style="font-size: 2rem; color: var(--teal);"></i>
                    </div>
                    <h3 style="color: #fff; font-size: 1.5rem; margin-bottom: 15px;">Smart Contract Ecosystem</h3>
                    <p style="color: var(--text-white); font-size: 0.95rem; line-height: 1.7; opacity: 0.85;">
                        NFTs on our platform aren't just art; they are smart-contract-bound <strong>Liquidity Vehicles</strong>. When you own an NFT, you hold the cryptographic key to a specific allocation of our AI bot's trading volume. The 5% reservation fee acts as an anti-bot measure to ensure fair market distribution and prevents malicious actors from locking up inventory without capital commitment.
                    </p>
                </div>
            </div>

            <!-- 4. Security & Architecture -->
            <div class="col-lg-6">
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 40px; height: 100%; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='rgba(45, 212, 191, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255, 255, 255, 0.05)';">
                    <div style="width: 60px; height: 60px; background: rgba(45, 212, 191, 0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px;">
                        <i class="las la-lock" style="font-size: 2rem; color: var(--teal);"></i>
                    </div>
                    <h3 style="color: #fff; font-size: 1.5rem; margin-bottom: 15px;">Enterprise-Grade Security</h3>
                    <p style="color: var(--text-white); font-size: 0.95rem; line-height: 1.7; opacity: 0.85;">
                        Our infrastructure is protected by a custom <strong>Web Application Firewall (WAF)</strong> that nullifies SQL injection and XSS attempts in real-time. Platform IP mapping is obscured via Cloudflare stealth routing. Server headers are stripped to prevent stack tracing, and all user data is encrypted. We prioritize your privacy and capital security above all else.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- Fundamentals Section End -->
    @include($activeTemplate.'sections.income_types')
    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
