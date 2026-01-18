# Chart Market Selector Feature

## Overview
Polymarket-style market selector modal that allows users to select which markets to display on the chart (maximum 4 markets).

## Features Implemented

### 1. Filter Icon Button
- **Location**: Right side of chart time filter buttons (1H, 6H, 1D, 1W, 1M, ALL)
- **Icon**: Sliders/filter icon (adjustable lines)
- **Hover Effect**: Blue border and background highlight
- **Action**: Opens market selection modal

### 2. Market Selection Modal

#### Modal Design
- **Dark Theme**: Matches Polymarket's design (#1a2332 background)
- **Centered**: Fixed position overlay with centered modal
- **Responsive**: Works on mobile and desktop
- **Max Height**: 80vh with scrollable content
- **Close Options**: 
  - X button in header
  - Click outside modal
  - Press Escape key

#### Modal Header
- **Title**: "Show on chart"
- **Subtitle**: "Select a maximum of 4"
- **Close Button**: X icon with hover effect

#### Market List Items
Each market item displays:
- **Checkbox**: Custom styled with blue checkmark
- **Color Bar**: Vertical colored line matching chart series color
- **Market Name**: Truncated with ellipsis if too long
- **Market Label**: "Market 1", "Market 2", etc.

#### Selection Logic
- **Maximum 4 markets**: Can select up to 4 markets simultaneously
- **Disabled State**: When 4 markets are selected, unchecked items become disabled
- **Initial State**: All markets selected by default
- **Real-time Update**: Chart updates immediately when selection changes

### 3. Chart Integration

#### Dynamic Chart Updates
When markets are selected/deselected:
- Chart datasets update in real-time
- Smooth animation transition
- Price direction indicators preserved
- Chart summary updates with first selected market
- Legend updates automatically

#### Data Persistence
- Selected markets stored in `window.selectedMarketIds`
- All available markets stored in `window.allAvailableMarkets`
- Selection persists during time filter changes

## Technical Implementation

### HTML Structure

```html
<!-- Filter Button -->
<button class="chart-filter-btn" id="chartFilterBtn">
   <svg><!-- Filter icon --></svg>
</button>

<!-- Modal -->
<div class="chart-modal-overlay" id="chartModalOverlay">
   <div class="chart-modal">
      <div class="chart-modal-header">
         <h3>Show on chart</h3>
         <button class="chart-modal-close">×</button>
      </div>
      <div class="chart-modal-body">
         <!-- Market items populated dynamically -->
      </div>
   </div>
</div>
```

### CSS Classes

#### Button Styles
```css
.chart-filter-btn {
   /* Positioned at end of chart controls */
   margin-left: auto;
   /* Icon styling with hover effects */
}
```

#### Modal Styles
```css
.chart-modal-overlay {
   /* Fixed overlay with backdrop */
   z-index: 9999;
}

.chart-modal {
   /* Dark themed modal container */
   max-width: 450px;
   max-height: 80vh;
}

.chart-market-item {
   /* Market list item with checkbox */
   /* Hover and disabled states */
}
```

### JavaScript Functions

#### `initializeMarketSelection()`
- Initializes market data from chart series
- Populates modal with market items
- Sets up event listeners

#### `populateMarketModal()`
- Dynamically creates market list items
- Applies checked/disabled states
- Adds event listeners to checkboxes

#### `handleMarketSelection(marketId, isSelected)`
- Updates selected markets array
- Enforces 4-market limit
- Refreshes modal UI
- Updates chart

#### `updateChartWithSelectedMarkets()`
- Filters series data by selected markets
- Recreates chart datasets
- Updates chart with animation
- Updates summary statistics

#### `setupModalEventListeners()`
- Filter button click → open modal
- Close button click → close modal
- Outside click → close modal
- Escape key → close modal

## User Flow

### Opening Modal
1. User clicks filter icon button
2. Modal opens with fade-in animation
3. All markets shown with checkboxes
4. Currently selected markets are checked

### Selecting Markets
1. User clicks on a market item
2. Checkbox toggles on/off
3. Chart updates immediately
4. If 4 markets selected, others become disabled
5. Modal remains open for further changes

### Closing Modal
1. User clicks X button, outside modal, or presses Escape
2. Modal closes with fade-out
3. Selected markets remain on chart

## Data Structure

### Market Object
```javascript
{
   id: 123,                    // Market ID
   name: "Market Name 75%",    // Display name with price
   color: "#ff7b2c",          // Chart series color
   data: [45.2, 48.1, ...],   // Price data points
   directions: ["up", "down", ...], // Price movement
   selected: true             // Selection state
}
```

### Global Variables
```javascript
window.selectedMarketIds = [1, 2, 3, 4];  // Array of selected IDs
window.allAvailableMarkets = [...];       // All market objects
```

## Styling Details

### Colors
- **Primary Blue**: #4c8df5 (buttons, checkboxes)
- **Background**: #1a2332 (modal)
- **Border**: rgba(255, 255, 255, 0.1)
- **Text**: rgba(255, 255, 255, 0.95)
- **Hover**: rgba(76, 141, 245, 0.1)

### Spacing
- **Modal Padding**: 1.25rem header, 1rem body
- **Item Gap**: 0.75rem between elements
- **Item Margin**: 0.625rem bottom spacing

### Responsive Breakpoints
- **Desktop**: Full size (450px max-width)
- **Mobile (< 480px)**: 95% width, adjusted padding

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile touch support
- Smooth animations
- Keyboard navigation (Escape key)

## Performance Considerations
- Efficient DOM manipulation
- Debounced chart updates
- Minimal reflows
- Optimized event listeners

## Future Enhancements
Potential improvements:
- Drag to reorder markets
- Search/filter markets by name
- Save selection preferences
- Bulk select/deselect all
- Market grouping/categories
- Color customization per market

## Usage Example

### Initial State
```
Chart shows: Market 1, Market 2, Market 3, Market 4
Selected: [1, 2, 3, 4]
```

### User Action
```
User clicks filter icon
→ Modal opens
User unchecks Market 2
→ Chart updates to show: Market 1, Market 3, Market 4
→ Selected: [1, 3, 4]
User checks Market 5
→ Chart updates to show: Market 1, Market 3, Market 4, Market 5
→ Selected: [1, 3, 4, 5]
→ Market 2 becomes disabled (4 limit reached)
```

## Troubleshooting

### Modal not opening
- Check if `chartFilterBtn` element exists
- Verify event listener is attached
- Check console for JavaScript errors

### Markets not updating
- Verify `window.allAvailableMarkets` is populated
- Check `updateChartWithSelectedMarkets()` is called
- Ensure chart instance exists

### Checkbox not working
- Check event listener on checkbox
- Verify `handleMarketSelection()` is called
- Check `selectedMarketIds` array updates

## Integration with Time Filters

The market selector works seamlessly with time filters:
1. User selects markets (e.g., Market 1, 3, 5)
2. User clicks "1M" time filter
3. AJAX fetches new data for ALL markets
4. Chart updates with only selected markets (1, 3, 5)
5. Selection persists across time filter changes

## Code Location

### Files Modified
- `resources/views/frontend/market_details.blade.php`
  - Added filter button HTML
  - Added modal HTML structure
  - Added CSS styles
  - Added JavaScript functions

### Key Sections
- **CSS**: Lines ~246-440 (modal styles)
- **HTML Button**: Line ~506 (filter button)
- **HTML Modal**: Lines ~590-610 (modal structure)
- **JavaScript**: Lines ~960-1150 (market selection logic)

## Testing Checklist

- [ ] Filter button appears and is clickable
- [ ] Modal opens on button click
- [ ] Modal closes on X, outside click, and Escape
- [ ] Checkboxes toggle correctly
- [ ] Maximum 4 markets enforced
- [ ] Chart updates when selection changes
- [ ] Selection persists across time filters
- [ ] Responsive on mobile devices
- [ ] Works with different number of markets (1-8)
- [ ] Smooth animations and transitions

