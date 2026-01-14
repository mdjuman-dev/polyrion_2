$(function () {
    const $themeToggle = $("#themeToggle");
    const $html = $("html");
    const $body = $("body");

    function setHeaderThemeSwitch(theme) {
        const $switch = $(".header-menu-switch");
        if (!$switch.length) return;

        // Dark theme = toggle ON (blue), Light theme = OFF (gray)
        if (theme === "dark") {
            $switch.addClass("header-menu-switch--on");
        } else {
            $switch.removeClass("header-menu-switch--on");
        }
    }

    // Load saved theme
    function applyTheme(theme) {
        if (theme === "light") {
            $html.addClass("light-mode");
            $body.addClass("light-theme").removeClass("dark-theme");
            $themeToggle.html('<i class="fas fa-sun"></i>');
            setHeaderThemeSwitch("light");
        } else {
            $html.removeClass("light-mode");
            $body.addClass("dark-theme").removeClass("light-theme");
            $themeToggle.html('<i class="fas fa-moon"></i>');
            setHeaderThemeSwitch("dark");
        }
        localStorage.setItem("theme", theme);
    }

    function initTheme() {
        const savedTheme = localStorage.getItem("theme") || "dark";
        applyTheme(savedTheme);
    }

    initTheme();

    // Toggle theme
    $themeToggle.on("click", function (e) {
        e.preventDefault();
        if ($html.hasClass("light-mode")) {
            applyTheme("dark");
        } else {
            applyTheme("light");
        }
    });

    // Dark mode row in header dropdown also toggles theme + switch
    $(document).on("click", ".header-menu-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        // Reuse main header toggle logic so everything stays in sync
        $("#themeToggle").trigger("click");
    });

    // Filter icon button functionality
    $(document).ready(function () {
        // Filter toggle button - show/hide filter options
        $("#filterToggleBtn").on("click", function () {
            $(this).toggleClass("active");
            $(".filter-options-row").toggleClass("active");
        });

        // Filter button functionality
        $(".filter-btn").on("click", function () {
            $(".filter-btn").removeClass("active");
            $(this).addClass("active");
        });

        // Filter section scroll functionality
        const $filtersSection = $("#filtersSection");
        const $scrollLeft = $("#filterScrollLeft");
        const $scrollRight = $("#filterScrollRight");
        const scrollAmount = 200;

        // Only initialize if elements exist
        if ($filtersSection.length > 0 && $filtersSection[0]) {
            function updateScrollButtons() {
                // Check if element still exists
                if (!$filtersSection.length || !$filtersSection[0]) {
                    return;
                }

                const scrollLeft = $filtersSection.scrollLeft();
                const scrollWidth = $filtersSection[0].scrollWidth;
                const clientWidth = $filtersSection[0].clientWidth;

                // Hide left arrow if at the start
                if (scrollLeft <= 0) {
                    $scrollLeft.addClass("disabled");
                } else {
                    $scrollLeft.removeClass("disabled");
                }

                // Hide right arrow if at the end
                if (scrollLeft + clientWidth >= scrollWidth - 1) {
                    $scrollRight.addClass("disabled");
                } else {
                    $scrollRight.removeClass("disabled");
                }
            }

            // Scroll left
            $scrollLeft.on("click", function () {
                if ($filtersSection.length > 0) {
                    $filtersSection.animate(
                        {
                            scrollLeft:
                                $filtersSection.scrollLeft() - scrollAmount,
                        },
                        {
                            duration: 300,
                            complete: function () {
                                updateScrollButtons();
                            },
                        }
                    );
                }
            });

            // Scroll right
            $scrollRight.on("click", function () {
                if ($filtersSection.length > 0) {
                    $filtersSection.animate(
                        {
                            scrollLeft:
                                $filtersSection.scrollLeft() + scrollAmount,
                        },
                        {
                            duration: 300,
                            complete: function () {
                                updateScrollButtons();
                            },
                        }
                    );
                }
            });

            // Update button states on scroll
            $filtersSection.on("scroll", updateScrollButtons);

            // Update button states on window resize
            $(window).on("resize", function () {
                setTimeout(updateScrollButtons, 100);
            });

            // Initial check
            updateScrollButtons();
        }

        // Mobile nav active state
        $(".mobile-nav-item").on("click", function (e) {
            e.preventDefault();
            $(".mobile-nav-item").removeClass("active");
            $(this).addClass("active");
        });

        // Sort by dropdown
        $(".sort-by-btn").on("click", function (e) {
            e.stopPropagation();
            const $wrapper = $(this).closest(".sort-by-wrapper");
            $(".filter-dropdown-wrapper").not($wrapper).removeClass("active");
            $wrapper.toggleClass("active");

            // Adjust dropdown position to prevent overflow
            if ($wrapper.hasClass("active")) {
                adjustDropdownPosition($wrapper.find(".sort-dropdown-menu"));
            }
        });

        // Frequency dropdown
        $(".frequency-btn").on("click", function (e) {
            e.stopPropagation();
            const $wrapper = $(this).closest(".frequency-wrapper");
            $(".filter-dropdown-wrapper").not($wrapper).removeClass("active");
            $wrapper.toggleClass("active");

            // Adjust dropdown position to prevent overflow
            if ($wrapper.hasClass("active")) {
                adjustDropdownPosition(
                    $wrapper.find(".frequency-dropdown-menu")
                );
            }
        });

        // Status dropdown
        $(".status-btn").on("click", function (e) {
            e.stopPropagation();
            const $wrapper = $(this).closest(".status-wrapper");
            $(".filter-dropdown-wrapper").not($wrapper).removeClass("active");
            $wrapper.toggleClass("active");

            // Adjust dropdown position to prevent overflow
            if ($wrapper.hasClass("active")) {
                adjustDropdownPosition($wrapper.find(".status-dropdown-menu"));
            }
        });

        // Function to adjust dropdown position to prevent overflow
        function adjustDropdownPosition($dropdown) {
            if (!$dropdown.length) return;

            // Reset position
            $dropdown.css({ left: "auto", right: "auto" });

            setTimeout(function () {
                const dropdownWidth = $dropdown.outerWidth();
                const dropdownOffset = $dropdown.offset();
                const windowWidth = $(window).width();

                // Check if dropdown overflows on the right
                if (dropdownOffset.left + dropdownWidth > windowWidth - 20) {
                    $dropdown.css({ left: "auto", right: "0" });
                } else {
                    $dropdown.css({ left: "0", right: "auto" });
                }
            }, 10);
        }

        // Sort option selection
        $(".sort-option").on("click", function (e) {
            e.stopPropagation();
            const sortText = $(this).find("span").first().text();
            $(".sort-option").removeClass("active");
            $(this).addClass("active");
            $(".sort-by-btn .filter-option-text").text(sortText);
            $(".sort-by-wrapper").removeClass("active");
        });

        // Frequency option selection
        $(".frequency-option").on("click", function (e) {
            e.stopPropagation();
            const freqText = $(this).text();
            $(".frequency-option").removeClass("active");
            $(this).addClass("active");
            $(".frequency-btn .filter-option-text").text(freqText);
            $(".frequency-wrapper").removeClass("active");
        });

        // Status option selection
        $(".status-option").on("click", function (e) {
            e.stopPropagation();
            const statusText = $(this).text();
            $(".status-option").removeClass("active");
            $(this).addClass("active");
            $(".status-btn .filter-option-text").text(statusText);
            $(".status-wrapper").removeClass("active");
        });

        // Close dropdowns when clicking outside
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".filter-dropdown-wrapper").length) {
                $(".filter-dropdown-wrapper").removeClass("active");
            }
        });

        // Reposition dropdowns on window resize
        $(window).on("resize", function () {
            $(".filter-dropdown-wrapper.active").each(function () {
                const $wrapper = $(this);
                const $dropdown = $wrapper.find(
                    ".sort-dropdown-menu, .frequency-dropdown-menu, .status-dropdown-menu"
                );
                if ($dropdown.length) {
                    adjustDropdownPosition($dropdown);
                }
            });
        });
    });

    // Yes/No button
    $(".yes-btn, .no-btn").on("click", function (e) {
        e.stopPropagation();
        const $btn = $(this);
        $btn.css("transform", "scale(0.95)");
        setTimeout(() => $btn.css("transform", "scale(1.05)"), 100);

        const isYes = $btn.hasClass("yes-btn");
        const message = isYes ? "Vote recorded: YES" : "Vote recorded: NO";
        showNotification(message, isYes ? "success" : "danger");
    });

    // Bookmark button
    $(".action-btn").on("click", function (e) {
        e.stopPropagation();
        const $icon = $(this).find("i");
        if ($icon.hasClass("fa-bookmark")) {
            const $btn = $(this);
            if ($btn.css("opacity") === "0.5") {
                $btn.css({ opacity: "1", color: "var(--accent)" });
                showNotification("Market bookmarked", "success");
            } else {
                $btn.css("opacity", "0.5");
                showNotification("Bookmark removed", "danger");
            }
        }
    });

    // Search bar
    $(".search-bar input").on("input", function () {
        const searchTerm = $(this).val().toLowerCase();
        $(".market-card").each(function () {
            const title = $(this).find(".market-title").text().toLowerCase();
            if (title.includes(searchTerm)) {
                $(this).css({ display: "flex", animation: "fadeIn 0.3s ease" });
            } else {
                $(this).hide();
            }
        });
    });

    // Nav active
    $(".nav-item").on("click", function (e) {
        // Handle More dropdown
        if (
            $(this).closest(".nav-item-dropdown").length &&
            $(this).attr("id") === "moreNavBtn"
        ) {
            e.preventDefault();
            $("#moreDropdownMenu").toggleClass("active");
            $("#profileNavDropdownMenu").removeClass("active");
            return;
        }

        // Handle Profile dropdown
        if (
            $(this).closest(".nav-item-dropdown").length &&
            $(this).attr("id") === "profileNavBtn"
        ) {
            e.preventDefault();
            $("#profileNavDropdownMenu").toggleClass("active");
            $("#moreDropdownMenu").removeClass("active");
            return;
        }

        // Check if link has valid href (not empty and not just '#')
        const href = $(this).attr("href");
        const hasValidLink = href && href !== "" && href !== "#";

        if (
            !$(this).closest(".more-dropdown-menu, .profile-nav-dropdown-menu")
                .length
        ) {
            // Only prevent default if it's not a valid link
            if (!hasValidLink) {
                e.preventDefault();
            }
            $(".nav-item").removeClass("active");
            $(this).addClass("active");
            $("#moreDropdownMenu").removeClass("active");
            $("#profileNavDropdownMenu").removeClass("active");
        }
    });

    // Handle Profile dropdown item clicks
    $(".profile-nav-dropdown-menu .dropdown-item").on("click", function (e) {
        const href = $(this).attr("href");
        if (href && href.includes("user dashboard.html")) {
            // Update active state
            $(".profile-nav-dropdown-menu .dropdown-item").removeClass(
                "active"
            );
            $(this).addClass("active");

            // Close dropdown
            $("#profileNavDropdownMenu").removeClass("active");

            // Navigate to the page - the hash will be handled by the dashboard page JS
        }
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".nav-item-dropdown").length) {
            $("#moreDropdownMenu").removeClass("active");
            $("#profileNavDropdownMenu").removeClass("active");
        }
    });

    // Notification system
    function showNotification(message, type = "info") {
        const bg =
            type === "success"
                ? "#00C853"
                : type === "danger"
                ? "#FF4757"
                : "#ffb11a";
        const color = type === "success" || type === "danger" ? "#fff" : "#000";

        const $notif = $("<div></div>").text(message).css({
            position: "fixed",
            top: "100px",
            right: "20px",
            padding: "12px 20px",
            background: bg,
            color: color,
            borderRadius: "6px",
            fontWeight: "600",
            zIndex: "1000",
            boxShadow: "0 4px 12px rgba(0,0,0,0.2)",
            animation: "slideIn 0.3s ease",
        });

        $("body").append($notif);
        setTimeout(() => {
            $notif.css("animation", "slideOut 0.3s ease");
            setTimeout(() => $notif.remove(), 300);
        }, 2000);
    }

    // Keyboard shortcuts
    $(document).on("keydown", function (e) {
        const $search = $(".search-bar input");
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
            e.preventDefault();
            $search.focus();
        }
        if (e.key === "Escape" && $search.is(":focus")) {
            $search.val("");
            $(".market-card").show();
        }
    });

    // Responsive check
    function checkScreenSize() {
        if ($(window).width() <= 768) {
            $body.addClass("mobile");
            $(".mobile-bottom-nav").addClass("active");
        } else {
            $body.removeClass("mobile");
            $(".mobile-bottom-nav").removeClass("active");
        }
    }
    $(window).on("resize", checkScreenSize);
    checkScreenSize();

    // Mobile Menu Toggle
    $("#mobileMenuToggle").on("click", function () {
        $("#moreMenuSidebar, #moreMenuOverlay").addClass("active");
        $("body").css("overflow", "hidden");
    });

    // Mobile Bottom Navigation
    $(".mobile-nav-item").on("click", function (e) {
        e.preventDefault();
        const text = $(this).find("span").text();

        // Handle navigation actions
        if (text === "Search") {
            // Close More menu if open
            if ($("#moreMenuSidebar").hasClass("active")) {
                $("#moreMenuSidebar, #moreMenuOverlay").removeClass("active");
            }
            // Open mobile search popup
            $("#mobileSearchPopup, #mobileSearchOverlay").addClass("active");
            $("body").css("overflow", "hidden");
            setTimeout(function () {
                $("#mobileSearchInput").focus();
            }, 300);
            $(".mobile-nav-item").removeClass("active");
            $(this).addClass("active");
        } else if (text === "Breaking") {
            // Filter to show breaking markets
            $(".filter-btn").removeClass("active");
            $(".nav-item").removeClass("active");
            $('.nav-item:contains("Breaking")').addClass("active");
            $(".mobile-nav-item").removeClass("active");
            $(this).addClass("active");
        } else if (text === "More") {
            // Close search popup if open
            if ($("#mobileSearchPopup").hasClass("active")) {
                $("#mobileSearchPopup, #mobileSearchOverlay").removeClass(
                    "active"
                );
                $("#mobileSearchInput").val("");
                $("#mobileSearchResults").html("");
                $("#mobileSearchProfiles").html("");
            }
            // Open More menu sidebar
            $("#moreMenuSidebar, #moreMenuOverlay").addClass("active");
            $("body").css("overflow", "hidden");
        } else {
            // Home
            $(".mobile-nav-item").removeClass("active");
            $(this).addClass("active");
        }
    });

    // More Menu Toggle
    $("#moreMenuBtn, #closeMenuBtn, #moreMenuOverlay").on(
        "click",
        function (e) {
            if ($(this).attr("id") === "moreMenuBtn") {
                e.preventDefault();
                // Close search popup if open
                if ($("#mobileSearchPopup").hasClass("active")) {
                    $("#mobileSearchPopup, #mobileSearchOverlay").removeClass(
                        "active"
                    );
                    $("#mobileSearchInput").val("");
                    $("#mobileSearchResults").html("");
                    $("#mobileSearchProfiles").html("");
                    $(".mobile-nav-item").removeClass("active");
                }
                $("#moreMenuSidebar, #moreMenuOverlay").addClass("active");
                $("body").css("overflow", "hidden");
            } else {
                $("#moreMenuSidebar, #moreMenuOverlay").removeClass("active");
                $("body").css("overflow", "");
            }
        }
    );

    // Mobile Search Popup Toggle
    $("#mobileSearchClose, #mobileSearchOverlay").on("click", function (e) {
        e.preventDefault();
        $("#mobileSearchPopup, #mobileSearchOverlay").removeClass("active");
        $("body").css("overflow", "");
        $("#mobileSearchInput").val("");
        $("#mobileSearchResults").html("");
        $("#mobileSearchProfiles").html("");
        $(".mobile-nav-item").removeClass("active");
        $('.mobile-nav-item:has(span:contains("Home"))').addClass("active");
    });

    // Load recent searches
    function loadRecentSearches() {
        const recentSearches = JSON.parse(
            localStorage.getItem("recentSearches") || "[]"
        );
        const $recentList = $(".recent-searches-list");
        $recentList.html("");

        if (recentSearches.length > 0) {
            recentSearches.slice(0, 5).forEach(function (search) {
                $recentList.append(
                    '<div class="recent-search-item" data-search="' +
                        search +
                        '">' +
                        "<span>" +
                        search +
                        "</span>" +
                        '<i class="fas fa-times"></i>' +
                        "</div>"
                );
            });
        } else {
            $recentList.append(
                '<div style="color: var(--text-secondary); padding: 12px;">No recent searches</div>'
            );
        }
    }

    // Initialize recent searches when popup opens
    $(document).on("click", ".mobile-nav-item", function () {
        if ($(this).find("span").text() === "Search") {
            loadRecentSearches();
        }
    });

    // Handle recent search item click
    $(document).on("click", ".recent-search-item span", function (e) {
        e.stopPropagation();
        const searchTerm = $(this).text();
        $("#mobileSearchInput").val(searchTerm).trigger("input");
    });

    // Handle recent search delete
    $(document).on("click", ".recent-search-item i", function (e) {
        e.stopPropagation();
        const searchTerm = $(this).siblings("span").text();
        let recentSearches = JSON.parse(
            localStorage.getItem("recentSearches") || "[]"
        );
        recentSearches = recentSearches.filter((s) => s !== searchTerm);
        localStorage.setItem("recentSearches", JSON.stringify(recentSearches));
        loadRecentSearches();
    });

    // Mobile Search Tab Switching
    $(".mobile-search-tab").on("click", function () {
        const tab = $(this).data("tab");
        $(".mobile-search-tab").removeClass("active");
        $(this).addClass("active");
        $(".mobile-search-tab-content").removeClass("active");
        $("#" + tab + "Tab").addClass("active");

        // Refresh results for active tab
        const searchTerm = $("#mobileSearchInput").val();
        if (searchTerm.length > 0) {
            $("#mobileSearchInput").trigger("input");
        }
    });

    // Mobile Search Input Handler
    $("#mobileSearchInput").on("input", function () {
        const searchTerm = $(this).val();
        const $results = $("#mobileSearchResults");
        const $profiles = $("#mobileSearchProfiles");
        const activeTab = $(".mobile-search-tab.active").data("tab");

        // Clear results if search term is empty
        if (searchTerm.length === 0) {
            $results.html("");
            $profiles.html("");
            return;
        }

        // Show market results
        if (activeTab === "markets") {
            showMarketResults(searchTerm, $results);
        } else {
            showProfileResults(searchTerm, $profiles);
        }
    });

    // Function to show market results
    function showMarketResults(searchTerm, $container) {
        $container.html("");

        // Example market results - replace with actual API
        const marketResults = [
            {
                icon: '<i class="fas fa-landmark"></i>',
                title: "Fed polymarkets",
                subtitle: "Trade on Fed-related events.",
                hasArrow: true,
            },
            {
                icon: '<img src="https://via.placeholder.com/40" alt="Profile">',
                title: "Fed decision in December?",
                percentage: "50%",
                status: "No change",
            },
            {
                icon: '<img src="https://via.placeholder.com/40" alt="Profile">',
                title: "Fed decision in December?",
                percentage: "25 bps decrease",
                status: "success",
                hasCheck: true,
            },
        ];

        marketResults.forEach(function (result) {
            let resultHtml = '<div class="mobile-search-result-item">';
            resultHtml +=
                '<div class="mobile-search-result-icon">' +
                result.icon +
                "</div>";
            resultHtml += '<div class="mobile-search-result-content">';
            resultHtml +=
                '<div class="mobile-search-result-title">' +
                result.title +
                "</div>";
            if (result.subtitle) {
                resultHtml +=
                    '<div class="mobile-search-result-subtitle">' +
                    result.subtitle +
                    "</div>";
            }
            resultHtml += "</div>";

            if (result.percentage || result.status) {
                resultHtml += '<div class="mobile-search-result-stats">';
                if (result.percentage) {
                    resultHtml +=
                        '<div class="mobile-search-result-percentage">' +
                        result.percentage +
                        "</div>";
                }
                if (result.status) {
                    const statusClass =
                        result.status === "success" ? "success" : "";
                    const checkIcon = result.hasCheck
                        ? '<i class="fas fa-check"></i>'
                        : "";
                    resultHtml +=
                        '<div class="mobile-search-result-status ' +
                        statusClass +
                        '">' +
                        checkIcon +
                        (result.status !== "success" ? result.status : "") +
                        "</div>";
                }
                resultHtml += "</div>";
            }

            if (result.hasArrow) {
                resultHtml +=
                    '<i class="fas fa-chevron-right mobile-search-result-arrow"></i>';
            }

            resultHtml += "</div>";
            $container.append(resultHtml);
        });
    }

    // Function to show profile results
    function showProfileResults(searchTerm, $container) {
        $container.html(
            '<div style="color: var(--text-secondary); padding: 20px; text-align: center;">No profile results found</div>'
        );
        // Add profile search logic here
    }

    // Close mobile search on Escape key
    $(document).on("keydown", function (e) {
        if (e.key === "Escape" && $("#mobileSearchPopup").hasClass("active")) {
            $("#mobileSearchPopup, #mobileSearchOverlay").removeClass("active");
            $("body").css("overflow", "");
            $("#mobileSearchInput").val("");
        }
    });

    // Mobile theme toggle in More menu (same as header switch)
    $("#themeToggleMobile").on("click", function () {
        if ($html.hasClass("light-mode")) {
            applyTheme("dark");
        } else {
            applyTheme("light");
        }
    });

    // Live probability update (optional)
    function updateProbabilities() {
        $(".outcome-probability").each(function () {
            let currentValue = parseFloat($(this).text());
            const randomChange = (Math.random() - 0.5) * 5;
            let newValue = currentValue + randomChange;
            newValue = Math.max(1, Math.min(99, newValue));
            $(this)
                .text(newValue.toFixed(0) + "%")
                .css("animation", "pulse 0.5s ease");
            setTimeout(() => $(this).css("animation", "none"), 500);
        });
    }

    // Observer animation (lazy load)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                $(entry.target).css("animation", "fadeIn 0.5s ease");
            }
        });
    });

    $(".market-card").each(function () {
        observer.observe(this);
    });
});

// ********** Profile Dashboard Functionality (arman.html) **********
$(document).ready(function () {
    // Content Tabs (Positions/Activity)
    $(document).on("click", ".content-tab", function () {
        const tab = $(this).data("tab");
        $(".content-tab").removeClass("active");
        $(this).addClass("active");
        $(".tab-content-wrapper").addClass("d-none");
        $(`#${tab}-tab`).removeClass("d-none");
    });

    // Sub Tabs (Active/Closed)
    $(document).on("click", ".subtab-btn", function () {
        const subtab = $(this).data("subtab");
        $(".subtab-btn").removeClass("active");
        $(this).addClass("active");

        // Toggle table headers based on Active/Closed
        if (subtab === "active") {
            $(".active-headers").removeClass("d-none");
            $(".closed-headers").addClass("d-none");
            $("#sortFilterBtn span").text("Value");
        } else {
            $(".active-headers").addClass("d-none");
            $(".closed-headers").removeClass("d-none");
            $("#sortFilterBtn span").text("Profit/Loss");
        }
        // Add logic here to filter positions based on subtab
    });

    // Time Filters
    $(document).on("click", ".time-filter-btn", function () {
        $(".time-filter-btn").removeClass("active");
        $(this).addClass("active");
        const time = $(this).data("time");

        // Update timeframe text based on selection
        const $timeframe = $("#profitLossTimeframe");
        if (time === "ALL") {
            $timeframe.text("All-Time");
        } else if (time === "1D") {
            $timeframe.text("Past Day");
        } else if (time === "1W") {
            $timeframe.text("Past Week");
        } else if (time === "1M") {
            $timeframe.text("Past Month");
        }
        // Add logic here to update profit/loss chart based on time filter
    });

    // Sort Filter Dropdown
    $(document).on("click", "#sortFilterBtn", function (e) {
        e.stopPropagation();
        const $wrapper = $(this).closest(".filter-dropdown-wrapper");
        $(".filter-dropdown-wrapper").not($wrapper).removeClass("active");
        $wrapper.toggleClass("active");
    });

    $(document).on(
        "click",
        "#sortFilterMenu .filter-dropdown-item",
        function (e) {
            e.preventDefault();
            const sort = $(this).data("sort");
            const text = $(this).text();
            $("#sortFilterMenu .filter-dropdown-item").removeClass("active");
            $(this).addClass("active");
            $("#sortFilterBtn span").text(text);
            $(this).closest(".filter-dropdown-wrapper").removeClass("active");
            // Add logic here to sort positions
        }
    );

    // Amount Filter Dropdown
    $(document).on("click", "#amountFilterBtn", function (e) {
        e.stopPropagation();
        const $wrapper = $(this).closest(".filter-dropdown-wrapper");
        $(".filter-dropdown-wrapper").not($wrapper).removeClass("active");
        $wrapper.toggleClass("active");
    });

    $(document).on(
        "click",
        "#amountFilterMenu .filter-dropdown-item",
        function (e) {
            e.preventDefault();
            const amount = $(this).data("amount");
            const text = $(this).text();
            $("#amountFilterMenu .filter-dropdown-item").removeClass("active");
            $(this).addClass("active");
            $("#amountFilterBtn span").text(text);
            $(this).closest(".filter-dropdown-wrapper").removeClass("active");
            // Add logic here to filter activity by amount
        }
    );

    // Search Positions Input
    $(document).on("input", ".search-input", function () {
        const searchTerm = $(this).val().toLowerCase().trim();
        // Add logic here to filter positions/activity based on search term
        // Example: Filter table rows based on market name
        $(".positions-table tbody tr").each(function () {
            const rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Close dropdowns when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".filter-dropdown-wrapper").length) {
            $(".filter-dropdown-wrapper").removeClass("active");
        }
    });
});

// ********** user dashboard **********
$(document).ready(function () {
    // ---------------------------
    // Mobile Profile Dropdown Toggle
    // ---------------------------
    $("#mobileProfileDropdownBtn").on("click", function (e) {
        e.stopPropagation();
        $(".mobile-profile-dropdown").toggleClass("active");
    });

    // Close mobile dropdown when clicking outside
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".mobile-profile-dropdown").length) {
            $(".mobile-profile-dropdown").removeClass("active");
        }
    });

    // Mobile dropdown item click - navigate to tab
    $(".mobile-profile-dropdown-item").on("click", function (e) {
        e.preventDefault();
        const tabId = $(this).data("tab");

        // Update active state in mobile dropdown
        $(".mobile-profile-dropdown-item").removeClass("active");
        $(this).addClass("active");

        // Update active state in sidebar (if visible)
        $(".settings-tab").removeClass("active");
        $(`.settings-tab[data-tab="${tabId}"]`).addClass("active");

        // Show corresponding tab content
        $(".tab-content").removeClass("active");
        $(`#${tabId}-tab`).addClass("active");

        // Close dropdown
        $(".mobile-profile-dropdown").removeClass("active");

        // Scroll to top of settings
        $("html, body").animate(
            {
                scrollTop: $(".settings-container").offset().top - 20,
            },
            300
        );
    });

    // Handle hash navigation from Profile dropdown in nav
    function handleHashNavigation() {
        const hash = window.location.hash.replace("#", "");
        if (
            hash &&
            [
                "profile",
                "account",
                "trading",
                "notifications",
                "builder",
                "export",
            ].includes(hash)
        ) {
            // Update active states
            $(".settings-tab").removeClass("active");
            $(".mobile-profile-dropdown-item").removeClass("active");
            $(".profile-nav-dropdown-menu .dropdown-item").removeClass(
                "active"
            );

            $(`.settings-tab[data-tab="${hash}"]`).addClass("active");
            $(`.mobile-profile-dropdown-item[data-tab="${hash}"]`).addClass(
                "active"
            );
            $(
                `.profile-nav-dropdown-menu .dropdown-item[href*="${hash}"]`
            ).addClass("active");

            // Show corresponding tab content
            $(".tab-content").removeClass("active");
            $(`#${hash}-tab`).addClass("active");

            // Scroll to settings
            setTimeout(() => {
                $("html, body").animate(
                    {
                        scrollTop: $(".settings-container").offset().top - 20,
                    },
                    300
                );
            }, 100);
        }
    }

    // Check hash on page load
    handleHashNavigation();

    // Listen for hash changes
    $(window).on("hashchange", handleHashNavigation);

    // ---------------------------
    // Tab switching (Desktop Sidebar)
    // ---------------------------
    $(".settings-tab").on("click", function (e) {
        e.preventDefault();

        $(".settings-tab").removeClass("active");
        $(this).addClass("active");

        $(".tab-content").removeClass("active");

        let tabId = $(this).data("tab");
        $(`#${tabId}-tab`).addClass("active");

        // Update mobile dropdown active state
        $(".mobile-profile-dropdown-item").removeClass("active");
        $(`.mobile-profile-dropdown-item[data-tab="${tabId}"]`).addClass(
            "active"
        );
    });
});
// ********** user dashboard **********

// ********** Market Detail Page **********
$(document).ready(function () {
    // Only run if we're on market detail page
    if ($("#marketChart").length === 0 && $(".outcome-row").length === 0) {
        return;
    }

    // Initialize trading panel as closed
    $("#tradingPanel").removeClass("active");
    $("#mobilePanelOverlay").removeClass("active");
    $("body").css("overflow", "");

    // =======================
    // Chart Initialization
    // =======================
    function initMarketChart() {
        const ctx = $("#marketChart")[0];
        if (ctx && typeof Chart !== "undefined") {
            window.marketChart = new Chart(ctx.getContext("2d"), {
                type: "line",
                data: {
                    labels: Array.from({ length: 100 }, (_, i) => i),
                    datasets: [
                        {
                            label: "No change",
                            data: generateChartData(50, 100),
                            borderColor: "#f97316",
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                        },
                        {
                            label: "25 bps decrease",
                            data: generateChartData(48, 100),
                            borderColor: "#3b82f6",
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                        },
                        {
                            label: "50+ bps decrease",
                            data: generateChartData(1.9, 100),
                            borderColor: "#06b6d4",
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                        },
                        {
                            label: "25+ bps increase",
                            data: generateChartData(0.5, 100),
                            borderColor: "#eab308",
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: "index", intersect: false },
                    },
                    scales: {
                        y: {
                            grid: { color: "#374151" },
                            position: "right",
                            ticks: { callback: (v) => v + "%" },
                        },
                    },
                    interaction: {
                        mode: "nearest",
                        axis: "x",
                        intersect: false,
                    },
                },
            });
        }
    }

    // Chart data generator
    function generateChartData(target, length) {
        const data = [];
        let current = Math.random() * 20 + 10;
        for (let i = 0; i < length; i++) {
            current =
                current +
                (Math.random() - 0.5) * 10 +
                (target - current) * 0.02;
            data.push(Math.max(0, Math.min(100, current)));
        }
        return data;
    }

    // Initialize chart when Chart.js is loaded
    function checkChartJS() {
        if (typeof Chart !== "undefined") {
            initMarketChart();
        } else {
            setTimeout(checkChartJS, 50);
        }
    }

    // Start checking for Chart.js
    checkChartJS();

    // =======================
    // Tabs
    // =======================
    $(".tab-item").click(function () {
        $(".tab-item").removeClass("active");
        $(this).addClass("active");
        $(".tab-content").removeClass("active");
        $("#" + $(this).data("tab")).addClass("active");
    });

    $(".holders-tab").click(function () {
        $(".holders-tab").removeClass("active");
        $(this).addClass("active");
        $("#yes-holders, #no-holders").hide();
        $("#" + $(this).data("holders") + "-holders").show();
    });

    $(".activity-filter").click(function () {
        $(".activity-filter").removeClass("active");
        $(this).addClass("active");
    });

    // Mobile Trading Panel Toggle
    $("#mobileTradeToggle").click(function () {
        $("#tradingPanel, #mobilePanelOverlay").toggleClass("active");
    });

    // Close trading panel function
    function closeTradingPanel() {
        $("#tradingPanel").removeClass("active");
        $("#mobilePanelOverlay").removeClass("active");
        $("#actionTabs").removeClass("buy-only");
        $("body").css("overflow", "");
    }

    // Overlay click handler
    $(document).on("click", "#mobilePanelOverlay", function (e) {
        e.preventDefault();
        closeTradingPanel();
    });

    // =======================
    // Trading Panel Logic
    // =======================
    let currentShares = 0;
    let isBuy = true;
    let isYes = true;
    let isLimitOrder = false;
    let limitPrice = 0;
    let userBalance = 1000;

    // Buy / Sell
    $("#buyTab").click(function () {
        $(".action-tab").removeClass("active");
        $(this).addClass("active");
        isBuy = true;
        updateOutcomePrice();
        updateSummary();
    });

    $("#sellTab").click(function () {
        $(".action-tab").removeClass("active");
        $(this).addClass("active");
        isBuy = false;
        updateOutcomePrice();
        updateSummary();
    });

    // Order Type
    $("#orderType").change(function () {
        isLimitOrder = $(this).val() === "limit";
        $("#limitOrderFields").toggleClass("active", isLimitOrder);
        updateSummary();
    });

    $("#limitPrice").on("input", function () {
        limitPrice = parseFloat($(this).val()) || 0;
        updateSummary();
    });

    // Yes / No
    $("#yesBtn").click(function () {
        $(".outcome-btn-yes, .outcome-btn-no").removeClass("active");
        $(this).addClass("active");
        isYes = true;
        if (
            window.currentYesPrice !== undefined &&
            !isNaN(window.currentYesPrice)
        ) {
            const price = parseFloat(window.currentYesPrice) || 0;
            $("#limitPrice").val(price);
            $("#avgPriceValue").text(price.toFixed(0) + "¢");
        }
        updateOutcomePrice();
        calculateProfit(); // Update profit calculation
    });

    $("#noBtn").click(function () {
        $(".outcome-btn-yes, .outcome-btn-no").removeClass("active");
        $(this).addClass("active");
        isYes = false;
        if (
            window.currentNoPrice !== undefined &&
            !isNaN(window.currentNoPrice)
        ) {
            const price = parseFloat(window.currentNoPrice) || 0;
            $("#limitPrice").val(price);
            $("#avgPriceValue").text(price.toFixed(0) + "¢");
        }
        updateOutcomePrice();
        calculateProfit(); // Update profit calculation
    });

    function updateOutcomePrice() {
        if (
            window.currentYesPrice !== undefined &&
            window.currentNoPrice !== undefined
        ) {
            const price = isYes
                ? window.currentYesPrice
                : window.currentNoPrice;
            $("#limitPrice").val(price);
            limitPrice = price;
        }
        const $outcomePrice = $("#outcomePrice");
        if ($outcomePrice.length) {
            if (
                window.currentYesPrice !== undefined &&
                window.currentNoPrice !== undefined
            ) {
                if (isBuy) {
                    $outcomePrice.text(
                        isYes
                            ? window.currentYesPrice.toFixed(1) + "¢"
                            : window.currentNoPrice.toFixed(1) + "¢"
                    );
                } else {
                    $outcomePrice.text(
                        isYes
                            ? window.currentNoPrice.toFixed(1) + "¢"
                            : window.currentYesPrice.toFixed(1) + "¢"
                    );
                }
            } else {
                $outcomePrice.text(
                    isBuy
                        ? isYes
                            ? "0.1¢"
                            : "99.9¢"
                        : isYes
                        ? "99.9¢"
                        : "0.1¢"
                );
            }
        }
    }

    // Shares + / -
    $("#decrease-100").click(() => updateShares(-100));
    $("#decrease-10").click(() => updateShares(-10));
    $("#increase-10").click(() => updateShares(10));
    $("#increase-100").click(() => updateShares(100));

    function updateShares(amount) {
        if (amount === "max") {
            updateSharesMax();
            return;
        }
        // Add amount to current value (for +$1, +$20, etc. buttons)
        const currentValue = parseFloat($("#sharesInput").val()) || 0;
        currentShares = Math.max(0, currentValue + amount);
        $("#sharesInput").val(currentShares);
        calculateProfit(); // Update profit calculation
    }

    function updateSharesMax() {
        // Set to a high value or user's available balance
        // You can customize this based on your wallet balance logic
        currentShares = 10000; // Or get from wallet balance
        $("#sharesInput").val(currentShares);
        calculateProfit(); // Update profit calculation
    }

    $("#sharesInput").on("input", function () {
        const val = $(this).val();
        // Use parseFloat for decimal amounts
        currentShares = parseFloat(val) || 0;
        if (val === "" || val === null || val === undefined) {
            currentShares = 0;
        }
        calculateProfit(); // Update profit calculation live
    });

    // Initialize input to show 0
    $(document).ready(function () {
        if (
            $("#sharesInput").val() === "" ||
            $("#sharesInput").val() === null ||
            isNaN($("#sharesInput").val())
        ) {
            $("#sharesInput").val(0);
            currentShares = 0;
        } else {
            currentShares = parseFloat($("#sharesInput").val()) || 0;
        }

        // Initialize prices if not set (prices are in decimal format, e.g., 0.41 for 41¢)
        if (
            window.currentYesPrice === undefined ||
            isNaN(window.currentYesPrice)
        ) {
            const yesBtn = $("#yesBtn");
            if (yesBtn.length && yesBtn.data("price")) {
                // Price is already in decimal (0.41), convert to cents for display
                window.currentYesPrice =
                    parseFloat(yesBtn.data("price")) * 100 || 0;
            } else {
                window.currentYesPrice = 0;
            }
        }
        if (
            window.currentNoPrice === undefined ||
            isNaN(window.currentNoPrice)
        ) {
            const noBtn = $("#noBtn");
            if (noBtn.length && noBtn.data("price")) {
                // Price is already in decimal (0.60), convert to cents for display
                window.currentNoPrice =
                    parseFloat(noBtn.data("price")) * 100 || 0;
            } else {
                window.currentNoPrice = 0;
            }
        }

        // Initialize profit calculation after a short delay to ensure DOM is ready
        setTimeout(function () {
            calculateProfit();
        }, 100);
    });

    // Quick Buttons
    $(".quick-btn").click(function () {
        if (this.id === "maxShares") {
            currentShares = Math.floor(userBalance / 0.01);
        } else {
            const percent = parseInt($(this).data("percent"));
            currentShares = Math.floor((userBalance * percent) / 100 / 0.01);
        }
        $("#sharesInput").val(currentShares);
        calculateProfit(); // Update profit calculation
    });

    // Summary - Polymarket-style profit calculation
    function updateSummary() {
        calculateProfit();
    }

    // Polymarket-style calculation function
    function calculateProfit() {
        const yesBtn = document.getElementById("yesBtn");
        const noBtn = document.getElementById("noBtn");
        const sharesInput = document.getElementById("sharesInput");
        const potentialWin = document.getElementById("potentialWin");

        if (!yesBtn || !noBtn || !sharesInput || !potentialWin) {
            return;
        }

        // Determine selected price based on active button (default to YES)
        let selectedPrice;
        if (yesBtn.classList.contains("active")) {
            // Read price from YES button data-price attribute
            selectedPrice =
                parseFloat(yesBtn.getAttribute("data-price")) || 0.5;
        } else if (noBtn.classList.contains("active")) {
            // Read price from NO button data-price attribute
            selectedPrice = parseFloat(noBtn.getAttribute("data-price")) || 0.5;
        } else {
            // Default to YES if neither is active
            selectedPrice =
                parseFloat(yesBtn.getAttribute("data-price")) || 0.5;
        }

        // Validate price
        if (!selectedPrice || selectedPrice <= 0 || isNaN(selectedPrice)) {
            potentialWin.textContent = "$0";
            return;
        }

        // Read amount from input
        let amount = parseFloat(sharesInput.value);

        // If amount is empty or 0, show $0
        if (!amount || amount <= 0 || isNaN(amount)) {
            potentialWin.textContent = "$0";
            return;
        }

        // Polymarket-style calculation:
        // shares = amount / price
        // payout_if_win = shares * 1 (each share settles at $1)
        // profit = payout_if_win - amount
        let shares = amount / selectedPrice;
        let payout = shares * 1;
        let profit = payout - amount;

        // Round to 2 decimal places to avoid floating point issues
        payout = Math.round(payout * 100) / 100;
        profit = Math.round(profit * 100) / 100;

        // "To win" shows the payout (total return), not just profit
        // This matches Polymarket's "To win" display
        potentialWin.textContent = "$" + payout.toFixed(2);
    }

    // Execute Trade
    $("#executeTrade").click(function () {
        if (currentShares <= 0) return alert("Enter valid shares");
        if (isLimitOrder && limitPrice <= 0)
            return alert("Enter valid limit price");
        const action = isBuy ? "buy" : "sell";
        // Get dynamic outcome name from button
        const outcome = isYes ? 
            ($("#yesBtn").data("outcome") || window.currentFirstOutcome || "Yes") :
            ($("#noBtn").data("outcome") || window.currentSecondOutcome || "No");
        const date =
            $("#panelOutcomeName").text() ||
            $("#panelOutcomeTitle").text() ||
            "";
        const price = isLimitOrder
            ? limitPrice / 100
            : isBuy
            ? isYes
                ? 0.001
                : 0
            : isYes
            ? 0
            : 0.999;
        const total = currentShares * price;
        const toWin = isBuy
            ? currentShares * (1 - price)
            : currentShares * price;
        $("#modalBody").html(`
      <p>You will <strong>${action}</strong> <strong>${currentShares}</strong> shares of <strong>${outcome}</strong> for <strong>${date}</strong>.</p>
      <p><strong>Total Cost:</strong> $${total.toFixed(2)}</p>
      <p><strong>Potential Win:</strong> $${toWin.toFixed(2)}</p>
    `);
        $("#confirmModal").show();
    });

    // Confirm Trade
    $("#confirmTrade").click(function () {
        const original = $("#executeTrade").text();
        $("#executeTrade").text("Success!").css("background", "var(--success)");
        setTimeout(() => {
            $("#executeTrade")
                .text(original)
                .css("background", "var(--accent)");
        }, 2000);
        currentShares = 0;
        $("#sharesInput").val("0");
        $("#limitPrice").val("");
        updateSummary();
        $("#confirmModal").hide();
        const isBuyAction = $(".action-tab.active").text() === "Buy";
        // Get dynamic outcome from active button
        const $activeBtn = $(".outcome-btn.active");
        const isYesAction = $activeBtn.hasClass("outcome-btn-yes");
        const action = isBuyAction ? "bought" : "sold";
        // Get outcome name from button data attribute
        const outcome = $activeBtn.data("outcome") || (isYesAction ? 
            (window.currentFirstOutcome || "Yes") : 
            (window.currentSecondOutcome || "No"));
        const date =
            $("#panelOutcomeName").text() ||
            $("#panelOutcomeTitle").text() ||
            "";
        const price = isYesAction ? 0.78 : 0.22;
        $(".activity-list").prepend(`
      <div class="activity-item">
        <div class="activity-avatar">Y</div>
        <div class="activity-details">
          <div class="activity-action">You ${action} ${currentShares} ${outcome} for ${date}</div>
          <div class="activity-meta">Just now</div>
        </div>
        <div class="activity-value ${isBuyAction ? "buy" : "sell"}">
          ${(price * 100).toFixed(1)}¢ ($${(currentShares * price).toFixed(2)})
        </div>
      </div>
    `);
    });

    // Cancel Modal
    $("#cancelTrade, #closeModal").click(() => $("#confirmModal").hide());
    $(window).click(function (e) {
        if ($(e.target).is("#confirmModal")) $("#confirmModal").hide();
    });

    updateOutcomePrice();
    updateSummary();

    // =======================
    // Chart Range Buttons
    // =======================
    $(".chart-btn").click(function () {
        $(".chart-btn").removeClass("active");
        $(this).addClass("active");
        if (window.marketChart) {
            window.marketChart.data.datasets[0].data = generateChartData(
                50,
                100
            );
            window.marketChart.data.datasets[1].data = generateChartData(
                48,
                100
            );
            window.marketChart.data.datasets[2].data = generateChartData(
                1.9,
                100
            );
            window.marketChart.data.datasets[3].data = generateChartData(
                0.5,
                100
            );
            window.marketChart.update();
        }
    });

    // =======================
    // Date Tab Selection
    // =======================
    $(".date-tab").click(function (e) {
        e.preventDefault();
        $(".date-tab").removeClass("active");
        $(this).addClass("active");
    });

    // =======================
    // Function to populate trading panel with outcome data
    // =======================
    async function populateTradingPanel($row, isYesSelected, isMobile) {
        // Remove highlight from all outcome rows
        $(".outcome-row").removeClass("active selected");

        // Add highlight to clicked row
        $row.addClass("active selected");

        // Get outcome name - try multiple selectors
        let outcomeName = $row.find(".outcome-name").text().trim();
        if (!outcomeName) {
            outcomeName = $row
                .find(".outcome-text .outcome-name")
                .text()
                .trim();
        }
        if (!outcomeName) {
            outcomeName = $row.data("outcome-name") || "";
        }

        const marketTitle =
            $(".market-title").text().trim() ||
            $("#panelMarketTitle").text().trim();
        const $yesBtn = $row.find(".btn-yes");
        const $noBtn = $row.find(".btn-no");
        
        // Get market ID from row
        const marketId = $row.data("market-id");
        
        // Get outcomes from data attributes (dynamic outcomes like Up/Down, Yes/No, etc.)
        let outcomes = $row.data("outcomes");
        if (!outcomes || !Array.isArray(outcomes)) {
            // Try to parse if it's a JSON string
            try {
                outcomes = JSON.parse($row.attr("data-outcomes") || '["Yes", "No"]');
            } catch (e) {
                outcomes = ["Yes", "No"]; // Fallback
            }
        }
        const firstOutcome = outcomes[0] || "Yes";
        const secondOutcome = outcomes[1] || "No";
        
        // Get outcome names from button data attributes (preferred)
        const firstOutcomeName = $yesBtn.data("outcome") || firstOutcome;
        const secondOutcomeName = $noBtn.data("outcome") || secondOutcome;
        
        // Get initial prices from data attributes (fallback)
        let yesPriceDecimal = parseFloat($yesBtn.attr("data-price")) || parseFloat($row.attr("data-yes-price")) || 0.5;
        let noPriceDecimal = parseFloat($noBtn.attr("data-price")) || parseFloat($row.attr("data-no-price")) || 0.5;
        
        // Fallback: Extract from button text if data attributes not available
        if (isNaN(yesPriceDecimal) || yesPriceDecimal <= 0) {
            const yesButtonText = $yesBtn.text();
            const noButtonText = $noBtn.text();
            const yesPriceMatch = yesButtonText.match(/([\d.]+)¢/);
            const noPriceMatch = noButtonText.match(/([\d.]+)¢/);
            // Extract prices in CENTS, then convert to DECIMAL for calculation
            const yesPriceCents = yesPriceMatch ? parseFloat(yesPriceMatch[1]) : 50;
            const noPriceCents = noPriceMatch ? parseFloat(noPriceMatch[1]) : 50;
            // Convert cents to decimal (50¢ = 0.50) for calculation
            yesPriceDecimal = yesPriceCents / 100;
            noPriceDecimal = noPriceCents / 100;
        }
        
        // Fetch live prices from Polymarket API if market ID is available
        if (marketId) {
            try {
                const response = await fetch(`/api/market/${marketId}/live-price`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.yes_price !== undefined && data.no_price !== undefined) {
                        // Use live prices from Polymarket API
                        // Prices from API should already be in decimal format (0.001 to 0.999)
                        // But validate and convert if needed
                        let yesPrice = parseFloat(data.yes_price);
                        let noPrice = parseFloat(data.no_price);
                        
                        // Fix: If price is >= 1, it might be in cents (e.g., 70 = 70¢ = 0.70)
                        // Convert to decimal if needed
                        if (yesPrice >= 1 && yesPrice <= 100) {
                            yesPrice = yesPrice / 100;
                        }
                        if (noPrice >= 1 && noPrice <= 100) {
                            noPrice = noPrice / 100;
                        }
                        
                        // Ensure prices are in valid range (0.001 to 0.999)
                        if (yesPrice > 0 && yesPrice < 1) {
                            yesPriceDecimal = yesPrice;
                        }
                        if (noPrice > 0 && noPrice < 1) {
                            noPriceDecimal = noPrice;
                        }
                        
                        console.log('Live prices fetched from Polymarket:', {
                            rawYes: data.yes_price,
                            rawNo: data.no_price,
                            convertedYes: yesPriceDecimal,
                            convertedNo: noPriceDecimal,
                            yesCents: (yesPriceDecimal * 100).toFixed(1) + '¢',
                            noCents: (noPriceDecimal * 100).toFixed(1) + '¢'
                        });
                    }
                }
            } catch (error) {
                console.warn('Failed to fetch live prices, using cached prices:', error);
            }
        }
        
        // Convert to cents for display
        const yesPriceCents = yesPriceDecimal * 100;
        const noPriceCents = noPriceDecimal * 100;

        // Update panel elements
        if (marketTitle) {
            $("#panelMarketTitle").text(marketTitle);
        }
        if (outcomeName) {
            $("#panelOutcomeName").text(outcomeName);
            // Also update panelOutcomeTitle if it exists (for backward compatibility)
            if ($("#panelOutcomeTitle").length) {
                $("#panelOutcomeTitle").text(outcomeName);
            }
        }
        if (isMobile) {
            $("#buyTab").addClass("active");
            $("#sellTab").removeClass("active");
            $("#actionTabs").addClass("buy-only");
        }
        // Update button text with prices (display in cents) but store decimal in data-price
        // Use dynamic outcome names from the market
        $("#yesBtn")
            .html(`${firstOutcomeName} ${yesPriceCents.toFixed(1)}¢`)
            .attr("data-price", yesPriceDecimal)
            .attr("data-outcome", firstOutcomeName)
            .data("price-decimal", yesPriceDecimal)
            .data("outcome", firstOutcomeName); // Store outcome name
        $("#noBtn")
            .html(`${secondOutcomeName} ${noPriceCents.toFixed(1)}¢`)
            .attr("data-price", noPriceDecimal)
            .attr("data-outcome", secondOutcomeName)
            .data("price-decimal", noPriceDecimal)
            .data("outcome", secondOutcomeName); // Store outcome name
        
        // Store outcomes globally for trade execution
        window.currentFirstOutcome = firstOutcomeName;
        window.currentSecondOutcome = secondOutcomeName;

        if (isYesSelected) {
            $("#yesBtn").addClass("active");
            $("#noBtn").removeClass("active");
            $("#limitPrice").val(yesPriceCents.toFixed(1));
            if ($("#avgPriceValue").length) {
                $("#avgPriceValue").text(yesPriceCents.toFixed(0) + "¢");
            }
        } else {
            $("#noBtn").addClass("active");
            $("#yesBtn").removeClass("active");
            $("#limitPrice").val(noPriceCents.toFixed(1));
            if ($("#avgPriceValue").length) {
                $("#avgPriceValue").text(noPriceCents.toFixed(0) + "¢");
            }
        }
        // Ensure prices are valid numbers (store decimal prices for calculations)
        window.currentYesPrice = yesPriceDecimal;
        window.currentNoPrice = noPriceDecimal;
        limitPrice = isYesSelected ? yesPriceCents : noPriceCents;
        
        // Trigger calculation update after price change
        // Use multiple methods to ensure calculation updates
        setTimeout(function() {
            // Method 1: Direct call to global function
            if (typeof window.calculatePayout === 'function') {
                window.calculatePayout();
            }
            // Method 2: Dispatch custom event
            const event = new CustomEvent('tradingPriceUpdated', {
                detail: {
                    yesPrice: yesPriceDecimal,
                    noPrice: noPriceDecimal,
                    isYesSelected: isYesSelected
                }
            });
            document.dispatchEvent(event);
            // Method 3: Legacy function support
            if (typeof calculate === 'function') {
                calculate();
            }
            if (typeof calculateProfit === 'function') {
                calculateProfit();
            }
        }, 200);

        // Initialize summary with valid values
        if (typeof updateSummary === "function") {
            updateSummary();
        }
    }

    // =======================
    // Buy Yes Button Click
    // =======================
    $(".btn-yes").click(async function (e) {
        e.stopPropagation();
        const $row = $(this).closest(".outcome-row");
        const isMobile = window.innerWidth <= 768;
        await populateTradingPanel($row, true, isMobile);
        if (isMobile) {
            $("#tradingPanel, #mobilePanelOverlay").addClass("active");
            $("body").css("overflow", "hidden");
            setTimeout(function () {
                $("#tradingPanel").scrollTop(0);
            }, 100);
        } else {
            const $panel = $("#tradingPanel");
            if ($panel.length) {
                $("html, body").animate(
                    {
                        scrollTop: $panel.offset().top - 100,
                    },
                    500
                );
            }
        }
    });

    // =======================
    // Buy No Button Click
    // =======================
    $(".btn-no").click(async function (e) {
        e.stopPropagation();
        const $row = $(this).closest(".outcome-row");
        const isMobile = window.innerWidth <= 768;
        await populateTradingPanel($row, false, isMobile);
        if (isMobile) {
            $("#tradingPanel, #mobilePanelOverlay").addClass("active");
            $("body").css("overflow", "hidden");
            setTimeout(function () {
                $("#tradingPanel").scrollTop(0);
            }, 100);
        } else {
            const $panel = $("#tradingPanel");
            if ($panel.length) {
                $("html, body").animate(
                    {
                        scrollTop: $panel.offset().top - 100,
                    },
                    500
                );
            }
        }
    });

    // =======================
    // Outcome Row Click
    // =======================
    $(".outcome-row").click(async function (e) {
        if ($(e.target).closest(".btn-yes, .btn-no").length) {
            return;
        }
        const $row = $(this);
        const isMobile = window.innerWidth <= 768;
        await populateTradingPanel($row, true, isMobile);
        if (isMobile) {
            $("#tradingPanel, #mobilePanelOverlay").addClass("active");
            $("body").css("overflow", "hidden");
        } else {
            const $panel = $("#tradingPanel");
            if ($panel.length) {
                $("html, body").animate(
                    {
                        scrollTop: $panel.offset().top - 100,
                    },
                    500
                );
            }
        }
    });

    // =======================
    // Show More Button
    // =======================
    $(".show-more-btn").click(function () {
        $(this).toggleClass("expanded");
    });

    // =======================
    // Comment Reply Functionality
    // =======================
    $(document).on("click", ".reply-btn", function () {
        const $replyWrapper = $(this)
            .closest(".comment-section")
            .find(".comment-reply-wrapper");
        $replyWrapper.slideToggle(200);
        if ($replyWrapper.is(":visible")) {
            $replyWrapper.find(".comment-reply-input").focus();
        }
    });

    $(document).on("click", ".comment-reply-cancel-btn", function () {
        const $replyWrapper = $(this).closest(".comment-reply-wrapper");
        $replyWrapper.slideUp(200);
        $replyWrapper.find(".comment-reply-input").val("");
    });

    $(document).on("click", ".comment-reply-submit-btn", function () {
        const $replyInput = $(this).siblings(".comment-reply-input");
        const replyText = $replyInput.val().trim();
        if (replyText === "") {
            alert("Please enter a reply");
            return;
        }
        alert("Reply posted: " + replyText);
        $replyInput.val("");
        $(this).closest(".comment-reply-wrapper").slideUp(200);
    });

    $(document).on("keypress", ".comment-reply-input", function (e) {
        if (e.which === 13) {
            $(this).siblings(".comment-reply-submit-btn").click();
        }
    });
    $(document).on("click", ".shares-price", function () {
        const price = parseInt($(this).data("price"));
        updateShares(price);
    });
});
// ********** Header menu (Log In / Sign Up / burger) **********

$(function () {
    const $trigger = $("#headerMenuTrigger");
    const $dropdown = $("#headerMenuDropdown");

    if (!$trigger.length || !$dropdown.length) {
        return;
    }

    function closeHeaderMenu() {
        $dropdown.removeClass("active");
    }

    $trigger.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $dropdown.toggleClass("active");
    });

    $(document).on("click", function () {
        closeHeaderMenu();
    });

    $dropdown.on("click", function (e) {
        e.stopPropagation();
    });

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            closeHeaderMenu();
        }
    });
});
// ********** profile Detail Page **********

$(function () {
    /* ---------------------------
         Image list (for cycling)
      ---------------------------- */
    const imageSources = [];

    /* ---------------------------
         Change avatar function
      ---------------------------- */
    function changeAvatarImage(newImageUrl, fallbackImageUrl) {
        const $avatar = $(".profile-avatar-wrapper .img-responsive");
        const $wrapper = $(".profile-avatar-wrapper");

        if (!$avatar.length) return;

        $wrapper.addClass("loading");
        $avatar.off("load error");

        // When new image loads successfully
        $avatar.on("load", function () {
            $wrapper.removeClass("loading");
        });

        // If image fails → fallback
        $avatar.one("error", function () {
            $(this).attr("src", fallbackImageUrl);
            $wrapper.removeClass("loading");
        });

        // Update image
        $avatar.attr("src", newImageUrl);
    }

    /* ---------------------------
         Initialize fallback
      ---------------------------- */
    $(".profile-avatar-wrapper .img-responsive").each(function () {
        const fallbackSrc = "./assets/images/user.webp";

        $(this).one("error", function () {
            $(this).attr("src", fallbackSrc);
        });
    });

    /* ---------------------------
         Make avatar clickable
      ---------------------------- */
    const $avatarWrapper = $(".profile-avatar-wrapper");
    $avatarWrapper.css("cursor", "pointer");

    let currentIndex = 0;

    $avatarWrapper.on("click", function () {
        currentIndex = (currentIndex + 1) % imageSources.length;

        changeAvatarImage(
            imageSources[currentIndex],
            "./assets/images/user.webp"
        );
    });

    /* ---------------------------
         OPTIONAL: Click to upload
         (Leave commented)
      ---------------------------- */

    const $fileInput = $(
        '<input type="file" accept="image/*" style="display:none;">'
    );
    $("body").append($fileInput);

    $avatarWrapper.on("click", () => $fileInput.click());

    $fileInput.on("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const imgData = event.target.result;
            changeAvatarImage(imgData, "./assets/images/user.webp");

            // Save uploaded avatar
            localStorage.setItem("profileImage", imgData);
        };
        reader.readAsDataURL(file);
    });

    // Load saved avatar on page load
    const savedImage = localStorage.getItem("profileImage");
    if (savedImage) {
        changeAvatarImage(savedImage, "./assets/images/user.webp");
    }
});
