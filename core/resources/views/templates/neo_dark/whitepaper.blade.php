@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="pt-60 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom--card">
                    <div class="card-header border-bottom-0 text-center py-5">
                        <img src="{{ getImage(getFilePath('logoIcon').'/logo.png') }}" alt="logo" style="max-height: 100px; margin-bottom: 25px; filter: drop-shadow(0 0 15px rgba(246, 224, 94, 0.4));">
                        <h2 class="title" style="font-size: 2.5rem; font-weight: 800;">Core Asset Whitepaper</h2>
                        <p class="text--base mt-2" style="font-size: 1.1rem; letter-spacing: 2px; text-transform: uppercase;">v2.0 - Comprehensive Platform Architecture</p>
                    </div>
                    <div class="card-body p-md-5 p-4">
                        
                        <div class="whitepaper-content" style="color: #cbd5e1; line-height: 1.9; font-size: 1.05rem;">
                            
                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">1. Introduction</h3>
                            <p class="mb-4">Founded in March 2026, Core Asset is positioned at the intersection of traditional finance (TradFi) and decentralized finance (DeFi). Our platform is engineered to dismantle the high entry barriers of premium investment vehicles, offering retail and institutional investors a secure, transparent, and highly profitable gateway to automated wealth generation.</p>
                            <p class="mb-5">By integrating state-of-the-art smart contracts with high-frequency algorithmic trading bots, Core Asset ensures that every user, regardless of their capital size, can benefit from institutional-grade investment strategies.</p>

                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">2. The Ecosystem</h3>
                            <p class="mb-3">The Core Asset ecosystem is built on a multi-layered architecture designed for maximum liquidity and minimal latency:</p>
                            <ul class="list-style-two mb-5">
                                <li><strong>Core Engine:</strong> The proprietary matching and yield-generation engine that executes trades across multiple DEXs and CEXs to capture arbitrage opportunities.</li>
                                <li><strong>Smart Contract Layer:</strong> Immutable ledgers that manage user deposits, track daily ROIs, and process withdrawals automatically without human intervention.</li>
                                <li><strong>Referral Matrix:</strong> A multi-tiered commission architecture designed to reward community builders while maintaining strict platform sustainability.</li>
                            </ul>

                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">3. Tokenomics & Economics</h3>
                            <p class="mb-3">Sustainability is the core philosophy of our tokenomics model. We employ a deflationary fee structure to protect the ecosystem against inflation:</p>
                            <ul class="list-style-two mb-5">
                                <li><strong>Deposit Allocation:</strong> 90% of all deposits are routed directly to the liquidity pools and trading engine. The remaining 10% fuels the referral matrix and platform maintenance.</li>
                                <li><strong>Withdrawal Mechanics:</strong> A dynamic fee (starting at 1%) is applied to withdrawals. This fee is automatically burned or redistributed to long-term stakers, creating a scarcity model.</li>
                                <li><strong>Yield Distribution:</strong> ROIs are calculated every 24 hours based on real-time trading metrics and distributed directly to the user's Interest Wallet.</li>
                            </ul>

                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">4. Security & Risk Management</h3>
                            <p class="mb-4">Security is not an afterthought; it is the foundation of Core Asset. We have implemented military-grade security protocols:</p>
                            <ul class="list-style-two mb-5">
                                <li><strong>Multi-Sig Cold Wallets:</strong> 95% of platform funds are stored in hardware-secured cold wallets requiring multiple executive signatures to authorize transfers.</li>
                                <li><strong>Database Hardening:</strong> Financial records are protected by database-level locking (`lockForUpdate()`) to mathematically eliminate double-spending vulnerabilities and race conditions.</li>
                                <li><strong>Anti-Bot Firewalls:</strong> Custom rate-limiting and behavior analysis prevent DDoS attacks and malicious automated scripts from interacting with the financial endpoints.</li>
                            </ul>

                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">5. Development Roadmap</h3>
                            <div class="row mt-4 mb-5">
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded" style="background: rgba(79, 209, 197, 0.05); border-left: 3px solid #4FD1C5;">
                                        <h5 class="text-white">Q1 2026: Foundation</h5>
                                        <p class="mb-0 text-sm">Launch of the core investment engine, PWA app release, and initial security audits.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded" style="background: rgba(246, 224, 94, 0.05); border-left: 3px solid #F6E05E;">
                                        <h5 class="text-white">Q3 2026: Expansion</h5>
                                        <p class="mb-0 text-sm">Integration of NFT staking, introduction of native DAO governance, and cross-chain bridges.</p>
                                    </div>
                                </div>
                            </div>

                            <h3 class="mb-3 text-white border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">6. Legal Disclaimer</h3>
                            <p class="mb-4 text-sm" style="opacity: 0.8;">The information provided in this Whitepaper does not constitute investment advice, financial advice, trading advice, or any other sort of advice. Core Asset does not guarantee any specific returns. All investments carry inherent risks. Users are advised to conduct their own due diligence before interacting with the platform.</p>

                            <div class="text-center mt-5 pt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                                <p class="text-muted"><em>Document Revision: 2.1.0 | Last Updated: May 2026</em></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
