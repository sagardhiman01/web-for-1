@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        $selectedData = $selectedData ?? [];
    @endphp
    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card card-bg mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">@lang('Currency List')</h5>
                                    <small class="text-muted">@lang('Live crypto prices with candlestick chart')</small>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="text" id="currencySearch" class="form-control form--control" placeholder="@lang('Search by name or symbol')">
                                    <button type="button" class="btn btn-primary btn-small" id="refreshCurrencyBtn">
                                        <i class="las la-sync"></i> @lang('Refresh')
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                @lang('Last updated'):
                                <span id="lastUpdatedAt">--</span>
                                |
                                @lang('Live time'):
                                <span id="liveClock">--</span>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-4">
                    <div class="card card-bg h-100">
                        <div class="card-body p-0">
                            <div class="table-responsive--sm currency-table-wrapper">
                                <table class="table text-white mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('Currency')</th>
                                            <th>@lang('Price')</th>
                                            <th>@lang('24h')</th>
                                            <th>@lang('Live Time')</th>
                                        </tr>
                                    </thead>
                                    <tbody id="currencyTableBody">
                                        @forelse($currencies as $coin)
                                            @php
                                                $isActive = ($selectedData['id'] ?? '') === $coin['id'];
                                                $price = (float) ($coin['current_price'] ?? 0);
                                                $change = (float) ($coin['change_24h'] ?? 0);
                                            @endphp
                                            <tr class="currency-row {{ $isActive ? 'active' : '' }}"
                                                data-coin-id="{{ $coin['id'] }}"
                                                data-coin-name="{{ $coin['name'] }}"
                                                data-coin-symbol="{{ $coin['symbol'] }}"
                                                data-coin-image="{{ $coin['image'] }}">
                                                <td>{{ $coin['rank'] ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $coin['image'] }}" alt="{{ $coin['symbol'] }}" class="coin-icon">
                                                        <div>
                                                            <strong>{{ $coin['name'] }}</strong><br>
                                                            <small>{{ $coin['symbol'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>${{ $price >= 1 ? number_format($price, 2) : number_format($price, 8) }}</td>
                                                <td class="{{ $change >= 0 ? 'text--success' : 'text--danger' }}">
                                                    {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }}%
                                                </td>
                                                <td>
                                                    @if(!empty($coin['last_updated_ms']))
                                                        {{ \Carbon\Carbon::createFromTimestampMs($coin['last_updated_ms'])->format('H:i:s') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center text-muted py-4">
                                                    @lang('Unable to load live currency data')
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div class="card card-bg h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $selectedData['image'] ?? '' }}" id="selectedCoinImage" class="coin-icon chart-coin-icon" alt="coin">
                                    <div>
                                        <h5 class="mb-0" id="selectedCoinName">{{ $selectedData['name'] ?? __('Currency') }}</h5>
                                        <small id="selectedCoinSymbol">{{ $selectedData['symbol'] ?? '' }}</small>
                                        <small class="d-block text-muted" id="selectedCoinTime">--</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0" id="selectedCoinPrice">
                                        @php
                                            $selectedPrice = (float) ($selectedData['current_price'] ?? 0);
                                        @endphp
                                        ${{ $selectedPrice >= 1 ? number_format($selectedPrice, 2) : number_format($selectedPrice, 8) }}
                                    </h5>
                                    <small id="selectedCoinChange" class="{{ (float) ($selectedData['change_24h'] ?? 0) >= 0 ? 'text--success' : 'text--danger' }}">
                                        @php
                                            $selectedChange = (float) ($selectedData['change_24h'] ?? 0);
                                        @endphp
                                        {{ $selectedChange >= 0 ? '+' : '' }}{{ number_format($selectedChange, 2) }}%
                                    </small>
                                </div>
                            </div>

                            <div class="chart-wrapper">
                                <div id="currencyChart"></div>
                            </div>
                            <small class="text-muted d-block mt-3">
                                @lang('Chart window'): @lang('Last 24 hours (candlestick)')
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .currency-table-wrapper {
            max-height: 620px;
            overflow: auto;
        }

        .currency-row {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .currency-row.active {
            background: rgba(255, 255, 255, 0.08);
        }

        .coin-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chart-coin-icon {
            width: 34px;
            height: 34px;
        }

        .chart-wrapper {
            min-height: 360px;
            position: relative;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function() {
            "use strict";

            const pricesUrl = "{{ route('user.currency.prices') }}";
            const chartUrlTemplate = "{{ route('user.currency.chart', ['coinId' => '__COIN_ID__']) }}";
            const tableBody = document.getElementById('currencyTableBody');
            const searchInput = document.getElementById('currencySearch');
            const refreshBtn = document.getElementById('refreshCurrencyBtn');
            const lastUpdatedAt = document.getElementById('lastUpdatedAt');
            const liveClock = document.getElementById('liveClock');
            let selectedCoinId = @json($selectedData['id'] ?? 'bitcoin');

            let pricePayload = @json($currencies);
            let chartPayload = {
                candles: @json($chartData['candles'] ?? []),
            };

            let candleChart = null;

            function formatPrice(value) {
                const num = Number(value || 0);
                if (num >= 1) {
                    return '$' + num.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                return '$' + num.toLocaleString(undefined, {
                    minimumFractionDigits: 4,
                    maximumFractionDigits: 8
                });
            }

            function formatChange(value) {
                const num = Number(value || 0);
                const sign = num >= 0 ? '+' : '';
                return sign + num.toFixed(2) + '%';
            }

            function parseDateInput(value) {
                if (value === null || value === undefined || value === '') {
                    return null;
                }

                const numericValue = Number(value);
                const date = Number.isFinite(numericValue) ? new Date(numericValue) : new Date(value);
                return Number.isNaN(date.getTime()) ? null : date;
            }

            function formatLocalDateTime(ms) {
                const date = parseDateInput(ms);
                if (!date) {
                    return '--';
                }
                return date.toLocaleString();
            }

            function formatLocalTime(ms) {
                const date = parseDateInput(ms);
                if (!date) {
                    return '--';
                }
                return date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
            }

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(match) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    };
                    return map[match];
                });
            }

            function renderRows(currencies) {
                const keyword = searchInput.value.trim().toLowerCase();
                const filtered = currencies.filter((coin) => {
                    if (!keyword) {
                        return true;
                    }
                    const name = String(coin.name || '').toLowerCase();
                    const symbol = String(coin.symbol || '').toLowerCase();
                    return name.includes(keyword) || symbol.includes(keyword);
                });

                if (!filtered.length) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="100%" class="text-center text-muted py-4">No currency found</td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = filtered.map((coin) => {
                    const change = Number(coin.change_24h || 0);
                    const isActive = coin.id === selectedCoinId ? 'active' : '';
                    const changeClass = change >= 0 ? 'text--success' : 'text--danger';
                    const liveTime = formatLocalTime(coin.last_updated_ms);

                    return `
                        <tr class="currency-row ${isActive}"
                            data-coin-id="${escapeHtml(coin.id)}"
                            data-coin-name="${escapeHtml(coin.name)}"
                            data-coin-symbol="${escapeHtml(coin.symbol)}"
                            data-coin-image="${escapeHtml(coin.image)}"
                            data-last-updated-ms="${escapeHtml(coin.last_updated_ms ?? '')}">
                            <td>${escapeHtml(coin.rank ?? '-')}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="${escapeHtml(coin.image)}" alt="${escapeHtml(coin.symbol)}" class="coin-icon">
                                    <div>
                                        <strong>${escapeHtml(coin.name)}</strong><br>
                                        <small>${escapeHtml(coin.symbol)}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${formatPrice(coin.current_price)}</td>
                            <td class="${changeClass}">${formatChange(change)}</td>
                            <td>${escapeHtml(liveTime)}</td>
                        </tr>
                    `;
                }).join('');
            }

            function renderChart(candles) {
                const chartElement = document.getElementById('currencyChart');
                if (!chartElement || typeof ApexCharts === 'undefined') {
                    return;
                }

                if (candleChart) {
                    candleChart.destroy();
                }

                candleChart = new ApexCharts(chartElement, {
                    chart: {
                        type: 'candlestick',
                        height: 360,
                        toolbar: {
                            show: true,
                        },
                        animations: {
                            enabled: true,
                        },
                    },
                    series: [{
                        data: candles || []
                    }],
                    plotOptions: {
                        candlestick: {
                            colors: {
                                upward: '#00E396',
                                downward: '#FF4560'
                            }
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            datetimeUTC: false,
                            formatter: function(value, timestamp) {
                                const timeValue = timestamp ?? value;
                                return formatLocalTime(timeValue);
                            }
                        }
                    },
                    yaxis: {
                        tooltip: {
                            enabled: true
                        },
                        labels: {
                            formatter: function(value) {
                                const num = Number(value || 0);
                                return '$' + num.toLocaleString(undefined, {
                                    maximumFractionDigits: num >= 1 ? 2 : 8
                                });
                            }
                        }
                    },
                    tooltip: {
                        x: {
                            formatter: function(value) {
                                return formatLocalDateTime(value);
                            }
                        }
                    },
                    noData: {
                        text: 'Chart data unavailable',
                    }
                });

                candleChart.render();
            }

            function updateLiveClock() {
                if (liveClock) {
                    liveClock.textContent = formatLocalDateTime(Date.now());
                }
            }

            function setCoinHeader(coin) {
                document.getElementById('selectedCoinName').textContent = coin.name || 'Currency';
                document.getElementById('selectedCoinSymbol').textContent = coin.symbol || '';
                document.getElementById('selectedCoinPrice').textContent = formatPrice(coin.current_price || 0);

                const changeElement = document.getElementById('selectedCoinChange');
                const changeValue = Number(coin.change_24h || 0);
                changeElement.textContent = formatChange(changeValue);
                changeElement.classList.remove('text--success', 'text--danger');
                changeElement.classList.add(changeValue >= 0 ? 'text--success' : 'text--danger');

                const coinTime = document.getElementById('selectedCoinTime');
                if (coinTime) {
                    coinTime.textContent = 'Updated: ' + formatLocalDateTime(coin.last_updated_ms);
                }

                const imageElement = document.getElementById('selectedCoinImage');
                if (coin.image) {
                    imageElement.src = coin.image;
                }
            }

            function findCoinById(coinId) {
                return pricePayload.find((coin) => coin.id === coinId);
            }

            function fetchChart(coinId) {
                const url = chartUrlTemplate.replace('__COIN_ID__', encodeURIComponent(coinId));

                return fetch(url)
                    .then((response) => response.json())
                    .then((response) => {
                        if (response.status !== 'success') {
                            throw new Error(response.message || 'Chart load failed');
                        }

                        chartPayload = response.chart;
                        renderChart(chartPayload.candles || []);
                        setCoinHeader(response.coin || {});
                    })
                    .catch(() => {
                        // Keep previous chart if network/API is temporarily unavailable.
                    });
            }

            function fetchPrices() {
                return fetch(pricesUrl)
                    .then((response) => response.json())
                    .then((response) => {
                        if (response.status !== 'success' || !Array.isArray(response.currencies)) {
                            return;
                        }

                        pricePayload = response.currencies;
                        renderRows(pricePayload);

                        const activeCoin = findCoinById(selectedCoinId);
                        if (activeCoin) {
                            setCoinHeader(activeCoin);
                        }

                        if (response.updated_at_ms) {
                            lastUpdatedAt.textContent = formatLocalDateTime(response.updated_at_ms);
                        } else {
                            lastUpdatedAt.textContent = formatLocalDateTime(Date.now());
                        }
                    })
                    .catch(() => {
                        // Keep current state if refresh fails.
                    });
            }

            tableBody.addEventListener('click', function(event) {
                const row = event.target.closest('.currency-row');
                if (!row) {
                    return;
                }

                selectedCoinId = row.dataset.coinId;
                tableBody.querySelectorAll('.currency-row').forEach((item) => item.classList.remove('active'));
                row.classList.add('active');

                const coin = findCoinById(selectedCoinId);
                if (coin) {
                    setCoinHeader(coin);
                }
                fetchChart(selectedCoinId);
            });

            searchInput.addEventListener('input', function() {
                renderRows(pricePayload);
            });

            refreshBtn.addEventListener('click', function() {
                fetchPrices().then(() => fetchChart(selectedCoinId));
            });

            renderRows(pricePayload);
            renderChart(chartPayload.candles || []);
            const initialCoin = findCoinById(selectedCoinId);
            if (initialCoin) {
                setCoinHeader(initialCoin);
            }
            lastUpdatedAt.textContent = formatLocalDateTime(Date.now());
            updateLiveClock();

            setInterval(function() {
                fetchPrices().then(() => fetchChart(selectedCoinId));
            }, 30000);

            setInterval(function() {
                updateLiveClock();
            }, 1000);
        })();
    </script>
@endpush
