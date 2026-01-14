# Polymarket API Data Extraction - Database Schema

## Overview
This document describes the database schema for storing Polymarket API data in a centralized trading platform.

## Tables

### 1. `events` Table

Stores event-level data from Polymarket API.

| Column | Type | Description | Source (API Field) |
|--------|------|-------------|-------------------|
| `id` | BIGINT (PK) | Auto-increment primary key | - |
| `polymarket_event_id` | STRING | Polymarket event ID | `id` |
| `title` | STRING | Event title | `title` |
| `description` | LONGTEXT | Event description | `description` |
| `slug` | STRING (UNIQUE) | URL friendly name | `slug` |
| `image` | STRING | Event image URL | `image` |
| `icon` | STRING | Event icon URL | `icon` |
| `ticker` | STRING | Event ticker | `ticker` |
| `active` | BOOLEAN | Is event active? | `active` |
| `closed` | BOOLEAN | Is event closed? | `closed` |
| `archived` | BOOLEAN | Is event archived? | `archived` |
| `new` | BOOLEAN | Is event new? | `new` |
| `featured` | BOOLEAN | Is event featured? | `featured` |
| `start_date` | TIMESTAMP | When did it start | `startDate` |
| `end_date` | TIMESTAMP | When does it end | `endDate` |
| `volume` | DECIMAL(30,8) | Total trading volume | `volume` |
| `liquidity` | DECIMAL(30,8) | Total liquidity | `liquidity` |
| `volume_24hr` | DECIMAL(30,8) | 24 hour volume | `volume24hr` |
| `volume_1wk` | DECIMAL(30,8) | 1 week volume | `volume1wk` |
| `volume_1mo` | DECIMAL(30,8) | 1 month volume | `volume1mo` |
| `volume_1yr` | DECIMAL(30,8) | 1 year volume | `volume1yr` |
| `liquidity_clob` | DECIMAL(30,8) | CLOB liquidity | `liquidityClob` |
| `competitive` | DECIMAL(10,8) | Competitive score | `competitive` |
| `comment_count` | INTEGER | Comment count | `commentCount` |
| `category` | STRING(50) | Auto-detected category | - |
| `created_at` | TIMESTAMP | Created timestamp | - |
| `updated_at` | TIMESTAMP | Updated timestamp | - |

### 2. `markets` Table

Stores market data from Polymarket API.

| Column | Type | Description | Source (API Field) |
|--------|------|-------------|-------------------|
| `id` | BIGINT (PK) | Auto-increment primary key | - |
| `event_id` | BIGINT (FK) | Foreign key to events | - |
| `question` | STRING | The actual question | `question` |
| `slug` | STRING (UNIQUE) | Market slug | `slug` |
| `description` | LONGTEXT | Market description | `description` |
| `image` | STRING | Market image URL | `image` |
| `icon` | STRING | Market icon URL | `icon` |
| `condition_id` | STRING | Condition ID (ignored for trading) | `conditionId` |
| `groupItem_title` | STRING | Group item title | `groupItemTitle` |
| `group_item_threshold` | STRING | Group item threshold | `groupItemThreshold` |
| `resolution_source` | STRING | Resolution source | `resolutionSource` |
| `active` | BOOLEAN | Is trading active? | `active` |
| `closed` | BOOLEAN | Is market closed? | `closed` |
| `archived` | BOOLEAN | Is market archived? | `archived` |
| `featured` | BOOLEAN | Is market featured? | `featured` |
| `new` | BOOLEAN | Is market new? | `new` |
| `restricted` | BOOLEAN | Is market restricted? | `restricted` |
| `approved` | BOOLEAN | Is market approved? | `approved` |
| `start_date` | DATETIME(6) | Market start date | `startDate` |
| `end_date` | DATETIME(6) | Market end date | `endDate` |
| `volume` | DECIMAL(30,8) | Market volume | `volume` or `volumeNum` |
| `liquidity` | DECIMAL(30,8) | Market liquidity | `liquidity` or `liquidityNum` |
| `liquidity_clob` | DECIMAL(30,8) | CLOB liquidity | `liquidityClob` |
| `volume24hr` | DECIMAL(30,8) | 24 hour volume | `volume24hr` or `volume24hrClob` |
| `volume1wk` | DECIMAL(30,8) | 1 week volume | `volume1wk` or `volume1wkClob` |
| `volume1mo` | DECIMAL(30,8) | 1 month volume | `volume1mo` or `volume1moClob` |
| `volume1yr` | DECIMAL(30,8) | 1 year volume | `volume1yr` or `volume1yrClob` |
| `outcomes` | JSON | Array like ["Yes", "No"] | `outcomes` |
| `outcome_prices` | JSON | Current prices like ["0.65", "0.35"] | `outcomePrices` |
| `last_trade_price` | DECIMAL(10,6) | Last trade price | `lastTradePrice` |
| `best_bid` | DECIMAL(10,6) | Best buy price | `bestBid` |
| `best_ask` | DECIMAL(10,6) | Best sell price | `bestAsk` |
| `spread` | DECIMAL(10,6) | Bid-ask spread | `spread` |
| `one_day_price_change` | DECIMAL(10,6) | 24h price change | `oneDayPriceChange` |
| `one_week_price_change` | DECIMAL(10,6) | 7d price change | `oneWeekPriceChange` |
| `one_month_price_change` | DECIMAL(10,6) | 30d price change | `oneMonthPriceChange` |
| `series_color` | STRING | Chart series color | `seriesColor` |
| `competitive` | DECIMAL(10,8) | Competitive score | `competitive` |
| `outcome_result` | STRING | Final outcome result | Calculated |
| `final_outcome` | STRING | Final outcome (YES/NO) | Calculated |
| `final_result` | STRING | Final result | Calculated |
| `result_set_at` | DATETIME | When result was set | - |
| `is_closed` | BOOLEAN | Is market closed? | Calculated |
| `settled` | BOOLEAN | Is market settled? | - |
| `created_at` | TIMESTAMP | Created timestamp | - |
| `updated_at` | TIMESTAMP | Updated timestamp | - |

### 3. `tags` Table

Stores tag information.

| Column | Type | Description | Source (API Field) |
|--------|------|-------------|-------------------|
| `id` | BIGINT (PK) | Auto-increment primary key | - |
| `label` | STRING | Tag label | `tags[].label` |
| `slug` | STRING (UNIQUE) | Tag slug | `tags[].slug` |
| `created_at` | TIMESTAMP | Created timestamp | - |
| `updated_at` | TIMESTAMP | Updated timestamp | - |

### 4. `event_tags` Table

Many-to-many relationship between events and tags.

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT (PK) | Auto-increment primary key |
| `event_id` | BIGINT (FK) | Foreign key to events |
| `tag_id` | BIGINT (FK) | Foreign key to tags |
| `created_at` | TIMESTAMP | Created timestamp |
| `updated_at` | TIMESTAMP | Updated timestamp |

## Field Extraction Status

### ✅ Events - All Required Fields Extracted
- ✅ id (as `polymarket_event_id`)
- ✅ title
- ✅ description
- ✅ slug
- ✅ image
- ✅ active
- ✅ closed
- ✅ endDate (as `end_date`)
- ✅ startDate (as `start_date`)
- ✅ volume
- ✅ liquidity
- ✅ volume24hr (as `volume_24hr`)
- ✅ tags (via `event_tags` relationship)

### ✅ Markets - All Required Fields Extracted
- ✅ id (stored in database as auto-increment)
- ✅ question
- ✅ outcomes
- ✅ outcomePrices (as `outcome_prices`)
- ✅ volume
- ✅ liquidity (NOW ADDED)
- ✅ active
- ✅ closed
- ✅ endDate (as `end_date`)
- ✅ lastTradePrice (as `last_trade_price`)
- ✅ bestBid (as `best_bid`)
- ✅ bestAsk (as `best_ask`)
- ✅ spread
- ✅ oneDayPriceChange (as `one_day_price_change`)
- ✅ oneWeekPriceChange (as `one_week_price_change`)
- ✅ oneMonthPriceChange (as `one_month_price_change`)

### ✅ Tags - All Required Fields Extracted
- ✅ label
- ✅ slug

## Ignored Fields (Not Extracted)

These fields are NOT needed for centralized trading:
- `conditionId` (stored but not used for trading)
- `marketMakerAddress`
- `clobTokenIds`
- `umaBond`
- `umaReward`
- All blockchain/smart contract related fields

## Notes

1. **Liquidity Fields**: Markets now have both `liquidity` (main value) and `liquidity_clob` (CLOB-specific value) for flexibility.

2. **Volume Fields**: Multiple fallback sources are used (e.g., `volume` or `volumeNum`, `volume24hr` or `volume24hrClob`).

3. **Price Fields**: Prices are stored as decimals with appropriate precision (10,6 for trading prices, 30,8 for volume/liquidity).

4. **JSON Fields**: `outcomes` and `outcome_prices` are stored as JSON for flexibility.

5. **Date Fields**: All dates are stored as timestamps/datetime with timezone support.

