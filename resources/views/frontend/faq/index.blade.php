@extends('frontend.layout.frontend')
@section('meta_derails')
    <title>FAQ - {{ $appName }}</title>
    <meta name="description" content="Frequently Asked Questions on {{ $appName }}.">
@endsection
@section('content')
    <main>
        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-12">
                    <div class="faq-header mb-4" style="text-align: center;">
                        <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Frequently Asked Questions
                        </h1>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">Find answers to common questions</p>
                    </div>

                    @if($faqs->count() > 0)
                        <div class="faq-container" style="max-width: 900px; margin: 0 auto;">
                            <div class="accordion" id="faqAccordion">
                                @foreach($faqs as $index => $faq)
                                    <div class="faq-item" style="background: var(--card-bg); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                                        <div class="faq-question" id="heading{{ $faq->id }}">
                                            <button class="faq-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" 
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $faq->id }}"
                                                style="width: 100%; padding: 1.25rem 1.5rem; background: transparent; border: none; text-align: left; color: var(--text-primary); font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
                                                <span>{{ $faq->question }}</span>
                                                <i class="fas fa-chevron-down faq-icon" style="transition: transform 0.3s ease; flex-shrink: 0; margin-left: 1rem;"></i>
                                            </button>
                                        </div>
                                        <div id="collapse{{ $faq->id }}" class="collapse {{ $index === 0 ? 'show' : '' }}" 
                                            aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                            <div class="faq-answer" style="padding: 0 1.5rem 1.5rem 1.5rem; color: var(--text-secondary); line-height: 1.8; font-size: 1rem;">
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem;">
                            <i class="fas fa-question-circle" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p style="color: var(--text-secondary); font-size: 1.1rem;">No FAQs available at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <style>
        .faq-btn:hover {
            background: rgba(255, 255, 255, 0.05) !important;
        }
        .faq-btn[aria-expanded="true"] .faq-icon {
            transform: rotate(180deg);
        }
        .faq-item {
            transition: box-shadow 0.3s ease;
        }
        .faq-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

