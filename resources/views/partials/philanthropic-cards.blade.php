@foreach ($works as $work)
    <div>
        <article class="group overflow-hidden rounded-md border border-border bg-surface transition-transform hover:-translate-y-1">
            <a href="/philanthropic-work/{{ $work->slug }}" class="block">
                <div class="aspect-[16/10] overflow-hidden">
                    <img src="{{ $work->image ?: '/assets/no-image-placeholder.svg' }}" alt="{{ $work->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"/>
                </div>
            </a>
            <div class="p-5">
                <h3 class="text-lg font-semibold tracking-tight"><a href="/philanthropic-work/{{ $work->slug }}" class="hover:underline">{{ $work->title }}</a></h3>
            </div>
        </article>
    </div>
@endforeach
