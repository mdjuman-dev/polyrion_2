/*! Notifications & Utilities - Production Optimized */

// Get theme color from CSS variable
function getThemeColor(variable) {
    return getComputedStyle(document.documentElement).getPropertyValue(variable).trim();
}

// Configure toastr options
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    onclick: null,
    showDuration: "400",
    hideDuration: "300",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
    hideEasing: "cubic-bezier(0.68, -0.55, 0.265, 1.55)",
    showMethod: "slideIn",
    hideMethod: "slideOut",
    onHidden: function() {
        $(this).remove();
        if ($('#toast-container').children().length === 0) {
            $('#toast-container').remove();
        }
    },
    onCloseClick: function() {
        $(this).fadeOut(300, function() {
            $(this).remove();
            if ($('#toast-container').children().length === 0) {
                $('#toast-container').remove();
            }
        });
    }
};

// Show success notification
function showSuccess(message, title) {
    title = title || 'Success';
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            background: getThemeColor('--card-bg'),
            color: getThemeColor('--text-primary'),
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-toast-theme',
                title: 'swal2-title-theme',
                content: 'swal2-content-theme'
            },
            didClose: function() {
                cleanupSwal();
            }
        });
    } else {
        toastr.success(message, title);
    }
}

// Show error notification
function showError(message, title) {
    title = title || 'Error';
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            toast: true,
            background: getThemeColor('--card-bg'),
            color: getThemeColor('--text-primary'),
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-toast-theme',
                title: 'swal2-title-theme',
                content: 'swal2-content-theme'
            },
            didClose: function() {
                cleanupSwal();
            }
        });
    } else {
        toastr.error(message, title);
    }
}

// Show warning notification
function showWarning(message, title) {
    title = title || 'Warning';
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            toast: true,
            background: getThemeColor('--card-bg'),
            color: getThemeColor('--text-primary'),
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-toast-theme',
                title: 'swal2-title-theme',
                content: 'swal2-content-theme'
            },
            didClose: function() {
                cleanupSwal();
            }
        });
    } else {
        toastr.warning(message, title);
    }
}

// Show info notification
function showInfo(message, title) {
    title = title || 'Info';
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            background: getThemeColor('--card-bg'),
            color: getThemeColor('--text-primary'),
            allowOutsideClick: true,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-toast-theme',
                title: 'swal2-title-theme',
                content: 'swal2-content-theme'
            },
            didClose: function() {
                cleanupSwal();
            }
        });
    } else {
        toastr.info(message, title);
    }
}

// Copy to clipboard function
function copyToClipboard(text, buttonIdOrElement) {
    var btn = null;
    if (typeof buttonIdOrElement === 'string') {
        btn = document.getElementById(buttonIdOrElement);
    } else if (buttonIdOrElement && buttonIdOrElement.nodeType === 1) {
        btn = buttonIdOrElement;
    }
    
    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    textarea.setSelectionRange(0, 99999);
    
    try {
        var successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        if (successful) {
            if (btn) {
                var icon = btn.querySelector('i');
                if (icon) {
                    var originalClass = icon.className;
                    var originalTitle = btn.getAttribute('title') || '';
                    icon.className = 'fas fa-check';
                    btn.style.background = '#10b981';
                    if (btn.style.color) {
                        btn.style.color = '#fff';
                    }
                    btn.setAttribute('title', 'Copied!');
                    setTimeout(function() {
                        icon.className = originalClass;
                        btn.style.background = 'var(--accent, #ffb11a)';
                        if (btn.style.color) {
                            btn.style.color = '#000';
                        }
                        btn.setAttribute('title', originalTitle);
                    }, 2000);
                } else {
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.style.background = '#10b981';
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                        btn.style.background = 'var(--accent, #ffb11a)';
                    }, 2000);
                }
            }
        } else {
            if (btn) {
                btn.style.background = '#ef4444';
                setTimeout(function() {
                    btn.style.background = 'var(--accent, #ffb11a)';
                }, 2000);
            }
        }
    } catch (err) {
        document.body.removeChild(textarea);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                if (btn) {
                    var icon = btn.querySelector('i');
                    if (icon) {
                        var originalClass = icon.className;
                        var originalTitle = btn.getAttribute('title') || '';
                        icon.className = 'fas fa-check';
                        btn.style.background = '#10b981';
                        if (btn.style.color) {
                            btn.style.color = '#fff';
                        }
                        btn.setAttribute('title', 'Copied!');
                        setTimeout(function() {
                            icon.className = originalClass;
                            btn.style.background = 'var(--accent, #ffb11a)';
                            if (btn.style.color) {
                                btn.style.color = '#000';
                            }
                            btn.setAttribute('title', originalTitle);
                        }, 2000);
                    } else {
                        var originalHTML = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-check"></i>';
                        btn.style.background = '#10b981';
                        setTimeout(function() {
                            btn.innerHTML = originalHTML;
                            btn.style.background = 'var(--accent, #ffb11a)';
                        }, 2000);
                    }
                }
            }).catch(function() {
                if (btn) {
                    btn.style.background = '#ef4444';
                    setTimeout(function() {
                        btn.style.background = 'var(--accent, #ffb11a)';
                    }, 2000);
                }
            });
        } else {
            if (btn) {
                btn.style.background = '#ef4444';
                setTimeout(function() {
                    btn.style.background = 'var(--accent, #ffb11a)';
                }, 2000);
            }
        }
    }
}

// Show confirm dialog
function showConfirm(message, title, confirmText, cancelText) {
    title = title || 'Confirm';
    confirmText = confirmText || 'Yes';
    cancelText = cancelText || 'No';
    
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: getThemeColor('--accent'),
        cancelButtonColor: getThemeColor('--secondary'),
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        background: getThemeColor('--card-bg'),
        color: getThemeColor('--text-primary'),
        customClass: {
            popup: 'swal2-theme',
            title: 'swal2-title-theme',
            content: 'swal2-content-theme'
        },
        didClose: function() {
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.documentElement.style.overflow = '';
            var containers = document.querySelectorAll('.swal2-container');
            containers.forEach(function(container) {
                container.remove();
            });
            var backdrops = document.querySelectorAll('.swal2-backdrop-show');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
        }
    });
}

// Cleanup Swal
function cleanupSwal() {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.documentElement.style.overflow = '';
    var containers = document.querySelectorAll('.swal2-container');
    containers.forEach(function(container) {
        container.remove();
    });
    var backdrops = document.querySelectorAll('.swal2-backdrop-show');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });
}

// Logout form handler
document.addEventListener('DOMContentLoaded', function() {
    var logoutForms = document.querySelectorAll('form[action*="logout"]');
    logoutForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to logout?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Logout',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal2-theme',
                        title: 'swal2-title-theme',
                        content: 'swal2-content-theme'
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        if (typeof showInfo !== 'undefined') {
                            showInfo('Logging out...', 'Logout');
                        } else if (typeof toastr !== 'undefined') {
                            toastr.info('Logging out...', 'Logout');
                        }
                        setTimeout(function() {
                            form.submit();
                        }, 300);
                    }
                });
            } else {
                if (confirm('Are you sure you want to logout?')) {
                    form.submit();
                }
            }
        });
    });
});

// Swal cleanup wrapper
if (typeof Swal !== 'undefined') {
    var originalFire = Swal.fire;
    Swal.fire = function(options) {
        var result = originalFire.call(this, options);
        if (result && typeof result.then === 'function') {
            result.then(function() {
                setTimeout(function() {
                    cleanupSwal();
                }, 100);
            }).catch(function() {
                setTimeout(function() {
                    cleanupSwal();
                }, 100);
            });
        }
        return result;
    };
    
    var observer = new MutationObserver(function(mutations) {
        var hasSwal = document.querySelector('.swal2-container');
        if (!hasSwal) {
            cleanupSwal();
        }
    });
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    setInterval(function() {
        var hasSwal = document.querySelector('.swal2-container.swal2-backdrop-show');
        if (!hasSwal) {
            var bodyOverflow = window.getComputedStyle(document.body).overflow;
            if (bodyOverflow === 'hidden') {
                cleanupSwal();
            }
        }
    }, 500);
}

// Cleanup on visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        cleanupSwal();
    }
});

// Cleanup on beforeunload
window.addEventListener('beforeunload', function() {
    cleanupSwal();
});


