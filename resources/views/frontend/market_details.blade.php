@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Polymarket - Market Details</title>
@endsection
@section('content')
    <main>
        <div class="main-layout">
            <div class="main-content">
                <div class="market-detail-header">
                    <div class="market-header-top">
                        <div class="market-header-left">
                            <div class="market-profile-img">
                                <img src="assets/images/user.webp" alt="Profile">
                            </div>
                            <div class="market-header-info">
                                <h1 class="market-title">Fed decision in December?</h1>
                                <div class="market-header-meta">
                                    <span class="market-volume">$97,962,719 Vol.</span>
                                    <span class="market-date">Dec 10, 2025</span>
                                </div>
                            </div>
                        </div>
                        <div class="market-header-actions">
                            <button class="market-action-btn" aria-label="Share">
                                <i class="fas fa-link"></i>
                            </button>
                            <button class="market-action-btn" aria-label="Bookmark">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                    <div class="date-selection-tabs">
                        <button class="date-tab-btn" aria-label="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="date-tab active">Dec 10</button>
                        <button class="date-tab">Jan 28, 2026</button>
                        <button class="date-tab">Mar 18, 2026</button>
                        <button class="date-tab">Apr 29, 2026</button>
                    </div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background: #f97316;"></div>
                        <span>No change 50%</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: #3b82f6;"></div>
                        <span>25 bps decrease 48%</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: #06b6d4;"></div>
                        <span>50+ bps decrease 1.9%</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: #eab308;"></div>
                        <span>25+ bps increase &lt;1%</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="marketChart"></canvas>
                    <div class="chart-controls">
                        <button class="chart-btn">1H</button>
                        <button class="chart-btn">6H</button>
                        <button class="chart-btn">1D</button>
                        <button class="chart-btn">1W</button>
                        <button class="chart-btn">1M</button>
                        <button class="chart-btn active">ALL</button>
                    </div>
                </div>
                <div class="outcome-section">
                    <div class="outcome-section-header">
                        <span class="outcome-label">OUTCOME</span>
                        <div class="outcome-chance-header">
                            <span>% CHANCE</span>
                            <button class="refresh-btn" aria-label="Refresh">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="outcome-row">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="outcome-info">
                                <div class="outcome-name">No change</div>
                                <div class="outcome-volume">$42,385,240 Vol.</div>
                            </div>
                            <div class="outcome-percent-wrapper">
                                <span class="outcome-percent">50%</span>
                                <span class="percent-change text-success">▲2%</span>
                            </div>
                        </div>
                        <div class="outcome-actions ">
                            <button class="btn-yes">Buy Yes 50.2¢</button>
                            <button class="btn-no">Buy No 49.8¢</button>
                        </div>
                    </div>
                    <div class="outcome-row">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="outcome-info">
                                <div class="outcome-name">25 bps decrease</div>
                                <div class="outcome-volume">$38,142,180 Vol.</div>
                            </div>
                            <div class="outcome-percent-wrapper">
                                <span class="outcome-percent">48%</span>
                                <span class="percent-change text-success">▲1.5%</span>
                            </div>
                        </div>
                        <div class="outcome-actions ">
                            <button class="btn-yes">Buy Yes 48.3¢</button>
                            <button class="btn-no">Buy No 51.7¢</button>
                        </div>
                    </div>
                    <div class="outcome-row">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="outcome-info">
                                <div class="outcome-name">50+ bps decrease</div>
                                <div class="outcome-volume">$1,861,299 Vol.</div>
                            </div>
                            <div class="outcome-percent-wrapper">
                                <span class="outcome-percent">1.9%</span>
                                <span class="percent-change text-danger">▼0.3%</span>
                            </div>
                        </div>
                        <div class="outcome-actions ">
                            <button class="btn-yes">Buy Yes 1.9¢</button>
                            <button class="btn-no">Buy No 98.1¢</button>
                        </div>
                    </div>
                    <div class="outcome-row">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="outcome-info">
                                <div class="outcome-name">25+ bps increase</div>
                                <div class="outcome-volume">$574,000 Vol.</div>
                            </div>
                            <div class="outcome-percent-wrapper">
                                <span class="outcome-percent">&lt;1%</span>
                                <span class="percent-change text-danger">▼0.2%</span>
                            </div>
                        </div>
                        <div class="outcome-actions ">
                            <button class="btn-yes">Buy Yes 0.8¢</button>
                            <button class="btn-no">Buy No 99.2¢</button>
                        </div>
                    </div>
                </div>
                <div class="market-info-section">
                    <div class="market-rules-section">
                        <h3 class="market-rules-title">Rules</h3>
                        <div class="market-rules-content">
                            <p>The FED interest rates are defined in this market by the upper bound of the target
                                federal funds range. The decisions on the target federal funds rate are made by the
                                Federal Open Market Committee (FOMC) during their scheduled meetings.</p>
                            <button class="show-more-btn">
                                Show more
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
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
            <div class="trading-panel" id="tradingPanel">
                <div class="panel-header">
                    <div class="market-profile-img">
                        <img src="assets/images/user.webp" alt="Profile">
                    </div>
                    <div class="panel-title-wrapper">
                        <div class="panel-market-title" id="panelMarketTitle">Fed decision in December?</div>
                        <div class="panel-title" id="panelOutcomeTitle">50+ bps decrease</div>
                    </div>
                    <button class="panel-close-btn hide-desktop" id="panelCloseBtn" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- <div class="order-type-selector">
                            <div class="action-tabs" id="actionTabs">
                                <button class="action-tab active" id="buyTab">Buy</button>
                                <button class="action-tab" id="sellTab">Sell</button>
                            </div>
                            <select class="order-type-select" id="orderType">
                                <option value="market">Market</option>
                                <option value="limit">Limit</option>
                            </select>
                        </div> -->
                <div class="limit-order-fields" id="limitOrderFields">
                    <div class="limit-input-group">
                        <label class="limit-input-label">Limit Price</label>
                        <input type="text" class="limit-input" id="limitPrice" placeholder="0.0¢">
                    </div>
                </div>
                <div class="outcome-selection">
                    <div class="outcome-buttons">
                        <button class="outcome-btn-yes active" id="yesBtn">Yes</button>
                        <button class="outcome-btn-no" id="noBtn">No</button>
                    </div>
                </div>
                <div class="shares-input-group">
                    <div class="shares-controls">
                        <label class="input-label">Shares</label>
                        <input type="number" class="shares-input" id="sharesInput" value="0" min="0"
                            placeholder="0" aria-label="Number of shares">
                    </div>
                    <div class="price-buttons">
                        <button class="shares-price" data-price="10" onclick="updateShares(10)">+10$</button>
                        <button class="shares-price" data-price="100" onclick="updateShares(100)">+100$</button>
                        <button class="shares-price" data-price="-10" onclick="updateShares(-10)">-10$</button>
                        <button class="shares-price" data-price="-100" onclick="updateShares(-100)">-100$</button>
                    </div>

                    <div class="trade-summary">
                        <div class="summary-row">
                            <span>Total</span>
                            <span class="summary-value" id="totalCost">$0</span>
                        </div>
                        <div class="summary-row">
                            <span>To Win</span>
                            <span class="summary-value" id="potentialWin">$0</span>
                        </div>
                    </div>
                    <button class="trade-btn" id="executeTrade">Bay Yes</button>
                    <!-- <div class="related-markets">
                            <div class="market-tags">
                                <div class="market-tag active">All</div>
                                <div class="market-tag">Politics</div>
                                <div class="market-tag">Trump</div>
                                <div class="market-tag">Gov Shutdown</div>
                            </div>
                            <div class="related-item">
                                <div class="related-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="related-info">
                                    <div class="related-title">Will the Government shutdown end by December 31?</div>
                                </div>
                                <div class="related-percent">100%</div>
                            </div>
                            <div class="related-item">
                                <div class="related-icon">
                                    <i class="fas fa-redo"></i>
                                </div>
                                <div class="related-info">
                                    <div class="related-title">Will there be another US government shutdown by...</div>
                                </div>
                                <div class="related-percent">2%</div>
                            </div>
                            <div class="related-item">
                                <div class="related-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="related-info">
                                    <div class="related-title">Will federal employees receive back pay after the
                                        government...
                                    </div>
                                </div>
                                <div class="related-percent">97%</div>
                            </div>
                        </div> -->
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
        <script></script>
    @endpush
@endsection
