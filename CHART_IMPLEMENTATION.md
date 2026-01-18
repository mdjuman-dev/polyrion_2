# Dynamic Market Chart System - Implementation Guide

## Overview
This system provides a Polymarket-style chart with dynamic time-based filters that show market price movements with directional indicators.

## Features Implemented

### 1. Time-Based Filters
- **1H** - Last 1 hour with 5-minute intervals (12 points)
- **6H** - Last 6 hours with 30-minute intervals (12 points)
- **1D** - Last 24 hours with hourly intervals (24 points)
- **1W** - Last 7 days with daily intervals (7 points)
- **1M** - Last 30 days with daily intervals (30 points)
- **ALL** - From market creation date to now (up to 30 points)

### 2. Dynamic Behavior

#### ALL Filter
- **Start Time**: Market creation date (`event->created_at`)
- **End Time**: Current time (`now()`)
- **X-Axis**: Shows dates from creation to now
- **Format**: "M d" (e.g., "Jan 15")

#### Time-Based Filters (1H, 6H, 1D, 1W, 1M)
- **Start Time**: Current time minus the period (e.g., `now() - 1 month`)
- **End Time**: Current time (`now()`)
- **X-Axis**: Shows time/date intervals within the period
- **Format**: 
  - 1H, 6H, 1D: "H:i" (e.g., "14:30")
  - 1W, 1M: "M d" (e.g., "Jan 15")

### 3. Price Movement Indicators

Each data point shows whether the price moved **up** or **down**:
- **Green points (↑)**: Price increased from previous point
- **Red points (↓)**: Price decreased from previous point
- **Neutral**: No change or first point

### 4. Visual Enhancements

#### Chart Summary Box
Displays for each period:
- **Period Change**: Total price change with percentage and arrow
- **Average Price**: Mean price across the period
- **High**: Maximum price in the period
- **Low**: Minimum price in the period

#### Tooltips
Show detailed information on hover:
- Market name and current price
- Direction arrow (↑ or ↓)
- Absolute change from previous point
- Percentage change from previous point

#### Loading States
- Spinner overlay during data fetch
- Disabled buttons during loading
- Smooth transitions between periods

## Technical Implementation

### Backend (Laravel)

#### New API Endpoint
```php
Route::get('/api/market/{slug}/chart-data', [HomeController::class, 'getChartDataByPeriod'])
```

**Parameters:**
- `slug`: Market/Event slug
- `period`: Time filter (1h, 6h, 1d, 1w, 1m, all)

**Response:**
```json
{
  "success": true,
  "data": {
    "labels": ["Jan 15", "Jan 16", ...],
    "series": [
      {
        "name": "Market Name 75%",
        "color": "#ff7b2c",
        "data": [45.2, 48.1, 52.3, ...],
        "directions": ["neutral", "up", "up", ...],
        "market_id": 123
      }
    ],
    "period": "1m",
    "startTime": "2024-12-18T00:00:00Z",
    "endTime": "2025-01-18T00:00:00Z"
  }
}
```

#### Key Methods

**`getChartDataByPeriod($slug, Request $request)`**
- Handles AJAX requests for chart data
- Validates period parameter
- Returns JSON response

**`generateChartDataForPeriod($event, $period)`**
- Calculates time range based on period
- Generates appropriate timestamps and labels
- Creates data points with price movements
- Calculates direction indicators
- Returns formatted chart data

### Frontend (JavaScript)

#### Main Functions

**`filterChartByPeriod(period)`**
- Triggered when user clicks a time filter button
- Makes AJAX request to backend API
- Shows loading overlay
- Updates chart with new data
- Falls back to legacy mode if API fails

**`updateChartWithNewData(chartData)`**
- Processes API response
- Creates Chart.js datasets with direction indicators
- Applies color coding for up/down movements
- Updates chart instance

**`updateChartSummary(series)`**
- Calculates statistics (change, avg, high, low)
- Updates summary display box
- Applies color coding (green/red)

**`priceDirectionPlugin`**
- Custom Chart.js plugin
- Draws arrow symbols (▲/▼) on data points
- Color-coded based on direction

### CSS Enhancements

```css
/* Loading overlay */
.chart-loading-overlay { ... }

/* Price change summary */
.chart-summary { ... }
.chart-summary-value.positive { color: #10b981; }
.chart-summary-value.negative { color: #ef4444; }

/* Enhanced buttons */
.chart-btn.active { background: #4c8df5; }
```

## Usage Example

### User Flow

1. **User opens market details page**
   - Chart loads with "ALL" period by default
   - Shows data from market creation to now

2. **User clicks "1M" button**
   - Button becomes active (blue)
   - Loading spinner appears
   - AJAX request: `/api/market/event-slug/chart-data?period=1m`
   - Chart updates to show last 30 days
   - X-axis shows daily dates
   - Summary box updates with 30-day statistics

3. **User clicks "1H" button**
   - Chart updates to show last 1 hour
   - X-axis shows time in HH:MM format
   - More granular data points (5-minute intervals)
   - Summary shows hourly statistics

4. **User hovers over data point**
   - Tooltip shows:
     - Market name: "Will X happen? 75%"
     - Direction: "↑"
     - Change: "+2.5% (+3.45%)"

## Time Range Calculations

### Example: 1M Filter
```
Current Time: 2025-01-18 14:30:00
Start Time: 2024-12-18 14:30:00 (1 month ago)
Interval: 1 day
Points: 30

Labels: ["Dec 18", "Dec 19", ..., "Jan 18"]
```

### Example: ALL Filter
```
Market Created: 2024-12-15 10:00:00
Current Time: 2025-01-18 14:30:00
Days Difference: 34 days
Max Points: 30
Interval: ceil(34/30) = 2 days

Labels: ["Dec 15", "Dec 17", "Dec 19", ..., "Jan 18"]
```

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile responsive (480px - desktop)
- Touch-friendly controls
- Optimized for performance

## Performance Considerations

- Maximum 30 data points per chart
- Lazy loading with AJAX
- Cached calculations
- Optimized rendering with Chart.js
- Debounced updates

## Future Enhancements

Potential improvements:
- Real-time updates via WebSocket
- Custom date range picker
- Export chart as image
- Compare multiple periods
- Historical data storage
- Advanced technical indicators

## Troubleshooting

### Chart not updating
- Check browser console for errors
- Verify API endpoint is accessible
- Check network tab for failed requests

### Wrong time range
- Verify server timezone settings
- Check `event->created_at` is set correctly
- Ensure Carbon is properly configured

### Missing direction indicators
- Verify `directions` array in API response
- Check `priceDirectionPlugin` is registered
- Ensure data has at least 2 points

## Support

For issues or questions, check:
- Laravel logs: `storage/logs/laravel.log`
- Browser console for JavaScript errors
- Network tab for API responses

