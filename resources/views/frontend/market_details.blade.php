@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Polymarket - Market Details</title>
@endsection
@section('content')
    <main>
        <div class="main-layout">
            <div class="main-content">
                <livewire:market-details.chart :event="$event" />
                <livewire:market-details.markets :event="$event" />
                <div class="tab-container">
                    <div class="tab-nav">
                        <div class="tab-item active" data-tab="comments">Comments (214)</div>
                        <div class="tab-item" data-tab="holders">Top Holders</div>
                        <div class="tab-item" data-tab="activity">Activity</div>
                    </div>

                    <div class="tab-content active" id="comments">
                        <div class="comments-section">
                            <div class="comment-input-container">
                                <div class="comment-input-wrapper">
                                    <input class="comment-input" type="text" placeholder="Write a comment...">
                                    <button class="comment-submit-btn">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Post</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="comment-section">
                            <div class="comment-header">
                                <a href="">
                                    <div class="comment-avatar">
                                        <img class="comment-avatar-img" src="assets/images/user.webp" alt="Zyns">
                                    </div>
                                </a>
                                <div class="comment-meta">
                                    <div class="comment-author"><a href="">Zyns</a></div>
                                    <div class="comment-date">November 12, 2025 at 11:45 PM</div>
                                </div>
                            </div>
                            <div class="comment-body">
                                If all goes smoothly, we could see the government reopen by the end of the day.
                            </div>
                            <div class="comment-actions">
                                <div class="comment-action">
                                    <i class="far fa-thumbs-up"></i> 42
                                </div>
                                <div class="comment-action reply-btn">
                                    <i class="far fa-comment-dots"></i> Reply
                                </div>
                            </div>
                            <div class="comment-reply-wrapper" style="display: none;">
                                <div class="comment-reply-input-wrapper">
                                    <input class="comment-reply-input" type="text" placeholder="Write a reply...">
                                    <button class="comment-reply-submit-btn">Post</button>
                                    <button class="comment-reply-cancel-btn">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <div class="comment-section">
                            <div class="comment-header">
                                <div class="comment-avatar">M</div>
                                <div class="comment-meta">
                                    <div class="comment-author">MarketWatcher</div>
                                    <div class="comment-date">November 12, 2025 at 10:30 PM</div>
                                </div>
                            </div>
                            <div class="comment-body">
                                I'm skeptical about a resolution by tomorrow. The sticking points haven't been
                                resolved
                                yet,
                                and both sides seem entrenched. My money is on November 13 or later.
                            </div>
                            <div class="comment-actions">
                                <div class="comment-action">
                                    <i class="far fa-thumbs-up"></i> 28
                                </div>
                                <div class="comment-action reply-btn">
                                    <i class="far fa-comment-dots"></i> Reply
                                </div>
                            </div>
                            <div class="comment-reply-wrapper" style="display: none;">
                                <div class="comment-reply-input-wrapper">
                                    <input class="comment-reply-input" type="text" placeholder="Write a reply...">
                                    <button class="comment-reply-submit-btn">Post</button>
                                    <button class="comment-reply-cancel-btn">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="holders">
                        <div class="holders-section">
                            <div class="holders-title">Top Holders</div>
                            <div class="holders-tabs">
                                <button class="holders-tab active" data-holders="yes">Yes Holders</button>
                                <button class="holders-tab" data-holders="no">No Holders</button>
                            </div>
                            <div class="holder-list" id="yes-holders">
                                <div class="holder-item">
                                    <div class="holder-rank">1</div>
                                    <div class="holder-name">Halfapound</div>
                                    <div class="holder-shares yes">197,407 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">2</div>
                                    <div class="holder-name">Melody626</div>
                                    <div class="holder-shares yes">40,604 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">3</div>
                                    <div class="holder-name">CryptoKing</div>
                                    <div class="holder-shares yes">35,821 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">4</div>
                                    <div class="holder-name">PoliticalProphet</div>
                                    <div class="holder-shares yes">28,945 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">5</div>
                                    <div class="holder-name">BetMaster</div>
                                    <div class="holder-shares yes">21,736 shares</div>
                                </div>
                            </div>
                            <div class="holder-list" id="no-holders" style="display: none;">
                                <div class="holder-item">
                                    <div class="holder-rank">1</div>
                                    <div class="holder-name">ltotheog</div>
                                    <div class="holder-shares no">36,080 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">2</div>
                                    <div class="holder-name">baderflyyyy</div>
                                    <div class="holder-shares no">15,612 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">3</div>
                                    <div class="holder-name">SkepticSam</div>
                                    <div class="holder-shares no">12,445 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">4</div>
                                    <div class="holder-name">DoubterDave</div>
                                    <div class="holder-shares no">9,876 shares</div>
                                </div>
                                <div class="holder-item">
                                    <div class="holder-rank">5</div>
                                    <div class="holder-name">NoWayJose</div>
                                    <div class="holder-shares no">7,654 shares</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="activity">
                        <div class="activity-section">
                            <div class="activity-title">Recent Activity</div>
                            <div class="activity-filters">
                                <button class="activity-filter active">All</button>
                                <button class="activity-filter">Min amount</button>
                            </div>
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-avatar">S</div>
                                    <div class="activity-details">
                                        <div class="activity-action">stompychan sold 20 No for November 12</div>
                                        <div class="activity-meta">2 minutes ago</div>
                                    </div>
                                    <div class="activity-value sell">22.0¢ ($4)</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar">C</div>
                                    <div class="activity-details">
                                        <div class="activity-action">coinfun sold 1,000 Yes for November 17</div>
                                        <div class="activity-meta">15 minutes ago</div>
                                    </div>
                                    <div class="activity-value sell">0.3¢ ($3)</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar">P</div>
                                    <div class="activity-details">
                                        <div class="activity-action">Prophet99 bought 500 Yes for November 12</div>
                                        <div class="activity-meta">32 minutes ago</div>
                                    </div>
                                    <div class="activity-value buy">78.0¢ ($390)</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar">B</div>
                                    <div class="activity-details">
                                        <div class="activity-action">BigBets bought 100 No for November 13</div>
                                        <div class="activity-meta">1 hour ago</div>
                                    </div>
                                    <div class="activity-value buy">21.0¢ ($21)</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-avatar">T</div>
                                    <div class="activity-details">
                                        <div class="activity-action">TraderJoe sold 50 Yes for November 12</div>
                                        <div class="activity-meta">2 hours ago</div>
                                    </div>
                                    <div class="activity-value sell">75.0¢ ($37.5)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Panel Overlay -->
            <div class="mobile-panel-overlay" id="mobilePanelOverlay"></div>
            <livewire:market-details.trading-panel :event="$event" />
        </div>
    </main>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"
            integrity="sha512-Ih4vqKQylvR5pDYxJ3H3OXHAMvNjl54hYDo6Ur5cDIrS+Fft+QrbVGnL3e2vBwpu7VQqGQDjCYXyCEhPLrM1EA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('chart-container');
                if (!container) {
                    console.warn('Chart container element not found.');
                    return;
                }
                if (typeof echarts === 'undefined') {
                    console.error('ECharts library failed to load.');
                    return;
                }

                const chart = echarts.init(container, null, {
                    renderer: 'canvas',
                    useDirtyRect: false
                });
                const DATA_URL =
                    'https://fastly.jsdelivr.net/gh/apache/echarts-website@gh-pages/data/asset/data/life-expectancy-table.json';

                fetch(DATA_URL)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Unable to load chart data');
                        }
                        return response.json();
                    })
                    .then((rawData) => {
                        const option = {
                            dataset: [{
                                    id: 'dataset_raw',
                                    source: rawData
                                },
                                {
                                    id: 'dataset_since_1950_of_germany',
                                    fromDatasetId: 'dataset_raw',
                                    transform: {
                                        type: 'filter',
                                        config: {
                                            and: [{
                                                    dimension: 'Year',
                                                    gte: 1950
                                                },
                                                {
                                                    dimension: 'Country',
                                                    '=': 'Germany'
                                                }
                                            ]
                                        }
                                    }
                                },
                                {
                                    id: 'dataset_since_1950_of_france',
                                    fromDatasetId: 'dataset_raw',
                                    transform: {
                                        type: 'filter',
                                        config: {
                                            and: [{
                                                    dimension: 'Year',
                                                    gte: 1950
                                                },
                                                {
                                                    dimension: 'Country',
                                                    '=': 'France'
                                                }
                                            ]
                                        }
                                    }
                                }
                            ],
                            title: {
                                text: 'Income of Germany and France since 1950'
                            },
                            tooltip: {
                                trigger: 'axis'
                            },
                            xAxis: {
                                type: 'category',
                                nameLocation: 'middle'
                            },
                            yAxis: {
                                name: 'Income'
                            },
                            series: [{
                                    type: 'line',
                                    datasetId: 'dataset_since_1950_of_germany',
                                    showSymbol: false,
                                    encode: {
                                        x: 'Year',
                                        y: 'Income',
                                        itemName: 'Year',
                                        tooltip: ['Income']
                                    }
                                },
                                {
                                    type: 'line',
                                    datasetId: 'dataset_since_1950_of_france',
                                    showSymbol: false,
                                    encode: {
                                        x: 'Year',
                                        y: 'Income',
                                        itemName: 'Year',
                                        tooltip: ['Income']
                                    }
                                }
                            ]
                        };

                        chart.setOption(option);
                    })
                    .catch((error) => {
                        console.error('Chart init failed:', error);
                    });

                window.addEventListener('resize', function() {
                    chart.resize();
                });
            });
        </script>
    @endpush
@endsection
