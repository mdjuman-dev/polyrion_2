@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>Contact & Support - {{ $appName }}</title>
    <meta name="description" content="Get in touch with us for support and inquiries on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5 mb-5">
            <div class="row justify-content-center">
                <div class="col-12" style="max-width: 1200px; margin: 0 auto;">
                    <div class="contact-header mb-5" style="text-align: center;">
                        <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Need Help? Contact Us
                        </h1>
                        <p style="color: var(--text-secondary); font-size: 1.1rem; margin: 0;">We're here to help you</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-5 col-md-6">
                            <div class="support-info-card" style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2rem; height: 100%; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                                <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-info-circle" style="color: #ffb11a;"></i>
                                    <span>Support Information</span>
                                </h2>
                                
                                @if($supportDescription)
                                    <div class="support-description mb-4" style="color: var(--text-secondary); line-height: 1.8; font-size: 0.95rem;">
                                        {!! ($supportDescription) !!}
                                    </div>
                                @endif

                                <div class="support-contact-details">
                                    @if($supportEmail)
                                        <div class="contact-item mb-3" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(255, 177, 26, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                                            <div style="flex-shrink: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255, 177, 26, 0.15); border-radius: 8px;">
                                                <i class="fas fa-envelope" style="color: #ffb11a; font-size: 1.2rem;"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <strong style="color: var(--text-primary); display: block; margin-bottom: 0.25rem; font-size: 0.875rem;">Email</strong>
                                                <a href="mailto:{{ $supportEmail }}" style="color: #ffb11a; text-decoration: none; font-size: 0.95rem; word-break: break-word;">
                                                    {{ $supportEmail }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($supportPhone)
                                        <div class="contact-item mb-3" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(255, 177, 26, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                                            <div style="flex-shrink: 0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255, 177, 26, 0.15); border-radius: 8px;">
                                                <i class="fas fa-phone" style="color: #ffb11a; font-size: 1.2rem;"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <strong style="color: var(--text-primary); display: block; margin-bottom: 0.25rem; font-size: 0.875rem;">Phone</strong>
                                                <a href="tel:{{ $supportPhone }}" style="color: #ffb11a; text-decoration: none; font-size: 0.95rem;">
                                                    {{ $supportPhone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($socialMediaLinks && $socialMediaLinks->count() > 0)
                                        <div class="contact-item mt-4" style="    padding: 10px;
    border-radius: 10px;">
                                            <strong style="color: var(--text-primary); display: block; margin-bottom: 1rem; font-size: 0.875rem; ">Follow Us</strong>
                                            <div class="social-links" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                                @foreach($socialMediaLinks as $link)
                                                    @php
                                                        $iconClass = match($link->platform) {
                                                            'facebook' => 'fab fa-facebook',
                                                            'twitter' => 'fa-brands fa-x',
                                                            'instagram' => 'fab fa-instagram',
                                                            'telegram' => 'fab fa-telegram',
                                                            'whatsapp' => 'fab fa-whatsapp',
                                                            'youtube' => 'fab fa-youtube',
                                                            'linkedin' => 'fab fa-linkedin',
                                                            default => 'fas fa-link',
                                                        };
                                                        $ariaLabel = ucfirst($link->platform === 'twitter' ? 'X (Twitter)' : $link->platform);
                                                    @endphp
                                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" 
                                                        style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; background: rgba(255, 177, 26, 0.1); border-radius: 10px; color: #ffb11a; text-decoration: none; transition: all 0.3s ease; border: 1px solid rgba(255, 177, 26, 0.2);"
                                                        aria-label="{{ $ariaLabel }}"
                                                        onmouseover="this.style.background='rgba(255, 177, 26, 0.2)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(255, 177, 26, 0.3)'"
                                                        onmouseout="this.style.background='rgba(255, 177, 26, 0.1)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="{{ $iconClass }}" style="font-size: 1.25rem;"></i>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6">
                            <div class="contact-form-card" style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                                <h2 style="font-size: 1.5rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-paper-plane" style="color: #ffb11a;"></i>
                                    <span>Send us a Message</span>
                                </h2>

                                <form id="contactForm">
                                    @csrf
                                    <div class="form-group mb-4">
                                        <label for="name" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                            <i class="fas fa-user" style="color: #ffb11a;"></i>
                                            <span>Full Name <span style="color: #ef4444;">*</span></span>
                                        </label>
                                        <input type="text" id="name" name="name" class="form-control contact-input" 
                                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid var(--border); border-radius: 8px; background: var(--secondary); color: var(--text-primary); font-size: 0.95rem; transition: all 0.3s ease;" 
                                            placeholder="Enter your full name"
                                            required>
                                        <div id="error_name" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="email" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                            <i class="fas fa-envelope" style="color: #ffb11a;"></i>
                                            <span>Email <span style="color: #ef4444;">*</span></span>
                                        </label>
                                        <input type="email" id="email" name="email" class="form-control contact-input" 
                                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid var(--border); border-radius: 8px; background: var(--secondary); color: var(--text-primary); font-size: 0.95rem; transition: all 0.3s ease;" 
                                            placeholder="your.email@example.com"
                                            required>
                                        <div id="error_email" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="subject" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                            <i class="fas fa-tag" style="color: #ffb11a;"></i>
                                            <span>Subject <span style="color: #ef4444;">*</span></span>
                                        </label>
                                        <input type="text" id="subject" name="subject" class="form-control contact-input" 
                                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid var(--border); border-radius: 8px; background: var(--secondary); color: var(--text-primary); font-size: 0.95rem; transition: all 0.3s ease;" 
                                            placeholder="What is this regarding?"
                                            required>
                                        <div id="error_subject" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="message" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.5rem;">
                                            <i class="fas fa-comment-alt" style="color: #ffb11a;"></i>
                                            <span>Message <span style="color: #ef4444;">*</span></span>
                                        </label>
                                        <textarea id="message" name="message" rows="6" class="form-control contact-input" 
                                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid var(--border); border-radius: 8px; background: var(--secondary); color: var(--text-primary); font-size: 0.95rem; resize: vertical; transition: all 0.3s ease; font-family: inherit;" 
                                            placeholder="Tell us how we can help you..."
                                            required></textarea>
                                        <div id="error_message" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; display: none;"></div>
                                    </div>

                                    <div id="contactMessage" style="display: none; padding: 0.875rem 1rem; margin-bottom: 1rem; border-radius: 8px;"></div>

                                    <button type="submit" id="submitBtn" class="btn btn-primary contact-submit-btn" 
                                        style="width: 100%; padding: 0.875rem 1.5rem; background: linear-gradient(135deg, #ffb11a 0%, #ff9500 100%); color: #000; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 177, 26, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Send Message</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .contact-input:focus {
            outline: none;
            border-color: #ffb11a;
            box-shadow: 0 0 0 3px rgba(255, 177, 26, 0.1);
        }

        .contact-input::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .contact-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 177, 26, 0.4);
            background: linear-gradient(135deg, #ff9500 0%, #ffb11a 100%);
        }

        .contact-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .contact-item:hover {
            background: rgba(255, 177, 26, 0.08) !important;
        }

        @media (max-width: 768px) {
            .contact-header h1 {
                font-size: 2rem !important;
            }

            .support-info-card,
            .contact-form-card {
                padding: 1.5rem !important;
            }
        }
    </style>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const messageDiv = document.getElementById('contactMessage');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Sending...</span>';
            messageDiv.style.display = 'none';
            
            document.querySelectorAll('[id^="error_"]').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });

            const formData = new FormData(form);

            fetch('{{ route("contact.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    form.reset();
                    if (typeof showSuccess === 'function') {
                        showSuccess(data.message);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            confirmButtonColor: '#ffb11a',
                            timer: 3000
                        });
                    } else {
                        messageDiv.style.display = 'block';
                        messageDiv.style.background = '#d4edda';
                        messageDiv.style.color = '#155724';
                        messageDiv.style.border = '1px solid #c3e6cb';
                        messageDiv.textContent = data.message;
                    }
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (error.errors) {
                    Object.keys(error.errors).forEach(key => {
                        const errorEl = document.getElementById('error_' + key);
                        if (errorEl) {
                            errorEl.style.display = 'block';
                            errorEl.textContent = error.errors[key][0];
                        }
                    });
                } else {
                    const errorMsg = error.message || 'Failed to send message. Please try again.';
                    if (typeof showError === 'function') {
                        showError(errorMsg);
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        messageDiv.style.display = 'block';
                        messageDiv.style.background = '#f8d7da';
                        messageDiv.style.color = '#721c24';
                        messageDiv.style.border = '1px solid #f5c6cb';
                        messageDiv.textContent = errorMsg;
                    }
                }
            });
        });
    </script>
@endsection
