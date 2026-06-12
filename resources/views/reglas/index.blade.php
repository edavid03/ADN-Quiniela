@extends('layouts.app')

@section('title', 'Reglas | '.config('app.name', 'Quiniela'))

@section('content')
    <section class="page-header">
        <div>
            <span class="kicker">Manual de juego</span>
            <h1 class="page-title">Reglas de la quiniela</h1>
            <p class="page-copy">Consulta c&oacute;mo participar, sumar puntos y registrar tus pron&oacute;sticos.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver a la mesa</a>
        </div>
    </section>

    @if (session('status'))
        <div class="alert border-emerald-200 bg-emerald-50 text-[var(--app-success)] dark:border-emerald-900/50 dark:bg-emerald-950/30">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert border-red-200 bg-red-50 text-[var(--app-danger)] dark:border-red-900/50 dark:bg-red-950/30">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-6 {{ auth()->user()->is_admin ? 'xl:grid-cols-[.72fr_1.28fr]' : '' }}">
        @if (auth()->user()->is_admin)
            <aside class="surface-strong h-fit p-4 sm:p-5">
                <span class="kicker">Nueva regla</span>
                <h2 class="mt-3 font-display text-2xl font-black">Agregar al reglamento</h2>
                <p class="mt-2 text-sm font-semibold leading-6 text-[var(--app-muted)]">La nueva regla ser&aacute; visible inmediatamente para todos los participantes.</p>

                <form method="POST" action="{{ route('admin.reglas.store') }}" class="mt-5 grid gap-4">
                    @csrf
                    <label class="grid gap-2 text-sm font-extrabold">
                        T&iacute;tulo
                        <input name="titulo" type="text" value="{{ old('titulo') }}" maxlength="120" required class="rounded-lg px-3 py-2.5">
                    </label>
                    <label class="grid gap-2 text-sm font-extrabold">
                        Explicaci&oacute;n
                        <textarea name="contenido" rows="6" maxlength="2000" required class="resize-y rounded-lg px-3 py-2.5">{{ old('contenido') }}</textarea>
                    </label>
                    <button type="submit" class="btn btn-primary">Guardar regla</button>
                </form>
            </aside>
        @endif

        <section class="surface overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-[var(--app-border)] px-5 py-4">
                <div>
                    <span class="kicker">Reglamento vigente</span>
                    <h2 class="mt-2 font-display text-2xl font-black">{{ $reglas->count() }} {{ $reglas->count() === 1 ? 'regla' : 'reglas' }}</h2>
                </div>
                <strong class="grid h-12 min-w-12 place-items-center rounded-lg bg-[var(--app-accent)] px-3 font-display text-xl font-black text-[#170f2f]">26</strong>
            </div>

            @forelse ($reglas as $index => $regla)
                <article class="grid gap-4 border-b border-[var(--app-border)] px-4 py-5 last:border-b-0 sm:px-5 {{ auth()->user()->is_admin ? 'md:grid-cols-[3.5rem_1fr]' : 'sm:grid-cols-[3.5rem_1fr]' }}">
                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-[var(--app-primary)] font-display text-lg font-black text-white dark:text-[#170f2f]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                    @if (auth()->user()->is_admin)
                        <div>
                            <form method="POST" action="{{ route('admin.reglas.update', $regla) }}" class="grid gap-3">
                                @csrf
                                @method('PATCH')
                                <input name="titulo" type="text" value="{{ $regla->titulo }}" maxlength="120" required class="rounded-lg px-3 py-2.5 font-display font-black">
                                <textarea name="contenido" rows="3" maxlength="2000" required class="resize-y rounded-lg px-3 py-2.5 text-sm font-semibold leading-6">{{ $regla->contenido }}</textarea>
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="text-xs font-bold text-[var(--app-muted)]">
                                        Actualizada {{ $regla->updated_at->diffForHumans() }}
                                        @if ($regla->updatedBy)
                                            por {{ $regla->updatedBy->name }}
                                        @endif
                                    </span>
                                    <button type="submit" class="btn btn-secondary min-h-0 py-2">Actualizar</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.reglas.destroy', $regla) }}" class="mt-2 text-right" onsubmit="return confirm('¿Eliminar esta regla?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-black uppercase text-[var(--app-danger)]">Eliminar regla</button>
                            </form>
                        </div>
                    @else
                        <div>
                            <h3 class="font-display text-xl font-black">{{ $regla->titulo }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm font-semibold leading-7 text-[var(--app-muted)]">{{ $regla->contenido }}</p>
                        </div>
                    @endif
                </article>
            @empty
                <div class="px-5 py-8 text-center">
                    <strong class="font-display text-xl">Todav&iacute;a no hay reglas publicadas.</strong>
                    <p class="mt-2 text-sm font-semibold text-[var(--app-muted)]">El administrador publicar&aacute; el reglamento aqu&iacute;.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
