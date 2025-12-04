<div class="nav-items-wrapper">
    <a href="{{ route('trending') }}"
        class="nav-item {{ request()->routeIs('trending') ? 'active' : '' }}"><i
            class="fas fa-arrow-trend-up"></i> Trending</a>
    <a href="{{ route('new') }}"
        class="nav-item {{ request()->routeIs('new') ? 'active' : '' }}">New</a>
    <div class="nav-item-divider"></div>
    
    @foreach($categories as $category)
        <a href="{{ route('events.by.category', ['category' => strtolower($category)]) }}" 
           class="nav-item {{ request()->routeIs('events.by.category') && request()->route('category') === strtolower($category) ? 'active' : '' }}">
            {{ $category }}
        </a>
    @endforeach
    
    <div class="nav-item-dropdown d-lg-block d-none" id="moreNavDropdown">
        <a href="#" class="nav-item" id="moreNavBtn">
            More <i class="fas fa-chevron-down"></i>
        </a>
        <div class="more-dropdown-menu" id="moreDropdownMenu"
            style='left: 0px;right: auto;top: 33px;'>
            <!-- Overflow items will be moved here by JavaScript -->
        </div>
    </div>
</div>

