@foreach ($feedbacks as $feedback)
    <div>
        <article class="flex h-full flex-col overflow-hidden rounded-md border border-border bg-surface shadow-xs hover:shadow-md transition-all duration-300">
            <button type="button" class="feedback-modal-trigger block aspect-[4/3] w-full cursor-pointer overflow-hidden bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center" style="border:0;padding:0;" data-message="{{ $feedback->message }}" data-image="{{ $feedback->image ?: '/assets/no-image-placeholder.svg' }}" aria-label="View feedback photo">
                <img src="{{ $feedback->image ?: '/assets/no-image-placeholder.svg' }}" alt="Feedback" loading="lazy" class="h-full w-full object-contain transition-transform duration-500 hover:scale-105"/>
            </button>
            <div class="flex flex-1 flex-col p-6">
                <p class="text-sm sm:text-base leading-relaxed text-foreground font-medium">&quot;{{ $feedback->message }}&quot;</p>
            </div>
        </article>
    </div>
@endforeach
