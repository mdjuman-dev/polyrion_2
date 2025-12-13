<style>
    /* Category Navigation Responsive Styles */
    .nav-items-wrapper {
        display: flex;
        gap: 24px;
        align-items: center;
        flex: 1;
        min-width: 0;
        overflow: visible;
        position: relative;
    }

    .nav-item-divider {
        width: 1px;
        height: 20px;
        background: var(--border);
        margin: 0 4px;
        flex-shrink: 0;
    }

    /* Tablet and below (768px) */
    @media (max-width: 768px) {
        .nav-items-wrapper {
            gap: 12px;
            flex-shrink: 0;
            min-width: max-content;
            white-space: nowrap;
        }

        .nav-item {
            padding: 8px 12px;
            border-radius: 4px;
            flex-shrink: 0;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .nav-item i {
            margin-right: 4px;
            font-size: 0.85rem;
        }

        .nav-item-divider {
            height: 16px;
            margin: 0 2px;
        }

        .nav-item-dropdown {
            flex-shrink: 0;
        }
    }

    /* Small mobile (480px) */
    @media (max-width: 480px) {
        .nav-items-wrapper {
            gap: 8px;
            min-width: max-content;
        }

        .nav-item {
            padding: 6px 10px;
            font-size: 0.75rem;
            min-height: 32px;
            border-radius: 6px;
            -webkit-tap-highlight-color: transparent;
        }

        .nav-item i {
            font-size: 0.7rem;
            margin-right: 3px;
        }

        .nav-item i.fa-arrow-trend-up {
            font-size: 0.75rem;
        }

        .nav-item-divider {
            height: 14px;
            margin: 0 2px;
        }

        /* Hide "More" dropdown on very small screens */
        .nav-item-dropdown {
            display: none !important;
        }
    }

    /* Extra small mobile (360px) */
    @media (max-width: 360px) {
        .nav-items-wrapper {
            gap: 6px;
        }

        .nav-item {
            padding: 5px 8px;
            font-size: 0.7rem;
        }

        .nav-item i {
            font-size: 0.65rem;
            margin-right: 2px;
        }
    }

    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        .nav-item {
            min-height: 44px;
            padding: 10px 14px;
        }

        @media (max-width: 480px) {
            .nav-item {
                min-height: 40px;
                padding: 8px 12px;
            }
        }
    }

    /* Smooth scrolling for navigation */
    @media (max-width: 768px) {
        .nav-items-wrapper {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* Ensure text doesn't break */
    .nav-item-text {
        white-space: nowrap;
        display: inline-block;
    }

    /* Better active state on mobile */
    @media (max-width: 768px) {
        .nav-item.active {
            background: rgba(255, 177, 26, 0.1);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 6px;
        }
    }

    /* Improve dropdown positioning on mobile */
    @media (max-width: 991px) {
        .more-dropdown-menu {
            position: fixed;
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) translateY(-10px);
            min-width: 250px;
            max-width: 90vw;
        }

        .more-dropdown-menu.active {
            transform: translateX(-50%) translateY(0);
        }
    }
</style>

<div class="nav-items-wrapper">
    <a href="{{ route('trending') }}"
        class="nav-item {{ request()->routeIs('trending') ? 'active' : '' }}">
        <i class="fas fa-arrow-trend-up"></i> 
        <span class="nav-item-text">Trending</span>
    </a>
    <a href="{{ route('new') }}"
        class="nav-item {{ request()->routeIs('new') ? 'active' : '' }}">
        <span class="nav-item-text">New</span>
    </a>
    <div class="nav-item-divider"></div>
    
    @foreach($categories as $category)
        <a href="{{ route('events.by.category', ['category' => strtolower($category)]) }}" 
           class="nav-item {{ request()->routeIs('events.by.category') && request()->route('category') === strtolower($category) ? 'active' : '' }}">
            <span class="nav-item-text">{{ $category }}</span>
        </a>
    @endforeach
    
    <div class="nav-item-dropdown d-lg-block d-none" id="moreNavDropdown">
        <a href="#" class="nav-item" id="moreNavBtn">
            <span class="nav-item-text">More</span> 
            <i class="fas fa-chevron-down"></i>
        </a>
        <div class="more-dropdown-menu" id="moreDropdownMenu"
            style='left: 0px;right: auto;top: 33px;'>
            <!-- Overflow items will be moved here by JavaScript -->
        </div>
    </div>
</div>

