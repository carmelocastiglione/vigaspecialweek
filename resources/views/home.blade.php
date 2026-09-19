<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('home.welcome') }} - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- TODO: ensure that the @fonts directive is correctly loading the required fonts -->
    @fonts

    <style>
      html {
        scroll-behavior: smooth;
      }
    </style>
 
</head>
<body class="h-full bg-white text-gray-900">
  @if($isMaintenanceMode)
    <x-maintenance-banner />
  @endif
  @if($isImpersonating)
    <x-impersonation-banner />
  @endif

<div class="bg-white">
  <!-- Hero Section -->
  <div class="relative isolate px-6 pt-56 pb-32 lg:px-8 flex items-center justify-center min-h-screen">
    <div aria-hidden="true" class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"></div>
    </div>
    <div class="mx-auto max-w-2xl">
      <div class="text-center">
        <h1 class="text-5xl font-bold tracking-tight text-balance text-gray-900 sm:text-7xl">Viga Special Week</h1>
        <p class="mt-4 text-base font-medium text-pretty text-gray-600 sm:text-lg/8">
            La tua settimana per approfondire, crescere e recuperare.
        </p>
        @if (Route::has('login'))
          <div class="mt-10 flex items-center justify-center gap-x-6">
            @auth
              <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-8 py-4 text-lg font-bold text-white shadow-lg hover:bg-indigo-500 hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                Dashboard
              </a>
            @else
              <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-8 py-4 text-lg font-bold text-white shadow-lg hover:bg-indigo-500 hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                Entra
              </a>
            @endauth
            @if (Route::has('activities.index'))
              <a href="{{ route('activities.index') }}" class="rounded-lg border-2 border-indigo-600 px-8 py-4 text-lg font-bold text-indigo-600 hover:bg-indigo-600 hover:text-white shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                Attività disponibili
              </a>
            @endif
          </div>
        @endif
        <div class="mt-8 flex items-center justify-center gap-6 flex-wrap text-sm">
          <a href="#attivita-disponibili" class="text-indigo-600 hover:text-indigo-500 font-semibold transition-colors">scopri le attività</a>
          <span class="text-gray-400">•</span>
          <a href="#come-funziona" class="text-indigo-600 hover:text-indigo-500 font-semibold transition-colors">come funziona</a>
          <span class="text-gray-400">•</span>
          <a href="#numeri" class="text-indigo-600 hover:text-indigo-500 font-semibold transition-colors">i numeri</a>
          <span class="text-gray-400">•</span>
          <a href="#faq" class="text-indigo-600 hover:text-indigo-500 font-extrabold transition-colors">dubbi e domande frequenti</a>
        </div>
      </div>
    </div>
    <div aria-hidden="true" class="absolute inset-x-0 bottom-0 -z-10 transform-gpu overflow-hidden blur-3xl">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"></div>
    </div>
  </div>

  <!-- Call to Action Section - View Activities -->
  <div id="attivita-disponibili" class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-24 sm:py-32 mt-12 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl mb-6">
          Scopri le attività di potenziamento disponibili
        </h2>
        <p class="text-lg leading-8 text-gray-300 mb-8">
          Puoi iniziare a sfogliare tutte le attività proposte anche senza accedere. Trova quello che fa per te e preparati a sperimentare nuove attività
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
          @if (Route::has('activities.index'))
            <a href="{{ route('activities.index') }}" class="rounded-lg bg-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500 transition-colors duration-200 inline-flex items-center gap-2">
              Visualizza attività
            </a>
          @endif
          @if (Route::has('login'))
            @auth
              <a href="{{ route('dashboard') }}" class="rounded-lg border-2 border-indigo-400 px-8 py-3 text-lg font-semibold text-indigo-400 hover:bg-indigo-400/10 transition-colors duration-200">
                Vai alla dashboard
              </a>
            @else
              <a href="{{ route('login') }}" class="rounded-lg border-2 border-indigo-400 px-8 py-3 text-lg font-semibold text-indigo-400 hover:bg-indigo-400/10 transition-colors duration-200">
                Accedi per iscriverti
              </a>
            @endauth
          @endif
        </div>
      </div>

      <!-- Features Grid -->
      <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Feature 1 -->
          <div class="relative pl-9">
            <div class="absolute left-0 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600">
              <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <p class="text-base leading-7 text-gray-300">
              <span class="font-semibold text-white">Attività varie</span> - Sport, Teatro, Filosofia, Informatica e tanto altro
            </p>
          </div>
          <!-- Feature 2 -->
          <div class="relative pl-9">
            <div class="absolute left-0 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600">
              <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <p class="text-base leading-7 text-gray-300">
              <span class="font-semibold text-white">Libertà di scelta</span> - Segui le attività che ti interessano
            </p>
          </div>
          <!-- Feature 3 -->
          <div class="relative pl-9">
            <div class="absolute left-0 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600">
              <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <p class="text-base leading-7 text-gray-300">
              <span class="font-semibold text-white">Conosci altri studenti</span> - Condividi i tuoi interessi
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- How It Works Section -->
  <div id="come-funziona" class="bg-white py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl mb-4">
          Come funziona
        </h2>
        <p class="text-lg leading-8 text-gray-600">
          Scopri come iscriverti ai corsi di potenziamento della Viga Special Week in pochi semplici step
        </p>
      </div>

      <!-- Desktop View -->
      <div class="hidden md:block mx-auto max-w-6xl">
        <div class="flex items-start justify-between">
          <!-- Step 1 -->
          <div class="flex flex-col items-center flex-1">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white font-bold text-2xl shadow-lg mb-4 relative z-10">
              1
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sfoglia</h3>
            <p class="text-gray-600 text-sm text-center">
              Visita la sezione attività e guarda tutte le proposte disponibili
            </p>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center flex-1">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white font-bold text-2xl shadow-lg mb-4 relative z-10">
              2
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Accedi</h3>
            <p class="text-gray-600 text-sm text-center">
              Accedi per selezionare le attività che più ti interessano
            </p>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center flex-1">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white font-bold text-2xl shadow-lg mb-4 relative z-10">
              3
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Scegli</h3>
            <p class="text-gray-600 text-sm text-center">
              Seleziona le 20 attività che più ti interessano (se non scegli, il sistema ti assegnerà altre attività in automatico)
            </p>
          </div>

          <!-- Step 4 -->
          <div class="flex flex-col items-center flex-1">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-600 text-white font-bold text-2xl shadow-lg mb-4 relative z-10">
              4
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Partecipa</h3>
            <p class="text-gray-600 text-sm text-center">
              Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni
            </p>
          </div>
        </div>
      </div>

      <!-- Mobile view - simplified -->
      <div class="md:hidden mx-auto max-w-md">
        <div class="space-y-6">
          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold flex-shrink-0 shadow-lg">1</div>
            <div class="pt-1">
              <p class="font-semibold text-gray-900">Sfoglia</p>
              <p class="text-sm text-gray-600">Visita la sezione attività e guarda tutte le proposte disponibili</p>
            </div>
          </div>

          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold flex-shrink-0 shadow-lg">2</div>
            <div class="pt-1">
              <p class="font-semibold text-gray-900">Accedi</p>
              <p class="text-sm text-gray-600">Accedi per selezionare le attività che più ti interessano</p>
            </div>
          </div>

          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold flex-shrink-0 shadow-lg">3</div>
            <div class="pt-1">
              <p class="font-semibold text-gray-900">Scegli</p>
              <p class="text-sm text-gray-600">Seleziona le 20 attività che più ti interessano (se non scegli, il sistema ti assegnerà altre attività in automatico)</p>
            </div>
          </div>

          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold flex-shrink-0 shadow-lg">4</div>
            <div class="pt-1">
              <p class="font-semibold text-gray-900">Partecipa</p>
              <p class="text-sm text-gray-600">Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Section -->
  <div id="numeri" class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-5xl mb-4">
          I numeri della Viga Special Week
        </h2>
        <p class="text-lg leading-8 text-indigo-100">
          Cosa puoi trovare in questa settimana speciale
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-3 lg:gap-12">
        <!-- Stat 1: Activities -->
        <div class="flex flex-col items-center">
          <div class="rounded-full bg-white/20 p-8 mb-6">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div class="text-center">
            <p class="text-5xl font-bold text-white mb-2">{{ $activitiesCount ?? '-' }}</p>
            <p class="text-lg text-indigo-100 font-semibold">Attività disponibili</p>
            <p class="text-sm text-indigo-200 mt-2">Per tutti gli interessi</p>
          </div>
        </div>

        <!-- Stat 2: Teachers -->
        <div class="flex flex-col items-center">
          <div class="rounded-full bg-white/20 p-8 mb-6">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5m0 0l9 5m-9-5v10l9 5m-9-5l9 5m9-5v10l-9 5m0-10l-9-5" />
            </svg>
          </div>
          <div class="text-center">
            <p class="text-5xl font-bold text-white mb-2">{{ $teachersCount ?? '-'}}</p>
            <p class="text-lg text-indigo-100 font-semibold">Insegnanti coinvolti</p>
            <p class="text-sm text-indigo-200 mt-2">Con il supporto di enti ed esperti esterni</p>
          </div>
        </div>

        <!-- Stat 3: Hours -->
        <div class="flex flex-col items-center">
          <div class="rounded-full bg-white/20 p-8 mb-6">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="text-center">
            <p class="text-5xl font-bold text-white mb-2">{{ $enrichmentHours ?? '-'}}</p>
            <p class="text-lg text-indigo-100 font-semibold">Ore di arricchimento</p>
            <p class="text-sm text-indigo-200 mt-2">Dedicate alla tua crescita personale</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ Section -->
  <div id="faq" class="bg-white py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl mb-4">
          Domande frequenti
        </h2>
        <p class="text-lg leading-8 text-gray-600">
          Qui troverai le risposte alle domande più comuni sulla Viga Special Week
        </p>
      </div>

      <!-- FAQ Items -->
      <div class="mx-auto max-w-3xl space-y-6">
        <x-faq-item question="Che cos'è la Viga Special Week?" answer="È una settimana speciale, dal 19 al 24 gennaio 2026 dedicata al recupero dei debiti e al potenziamento per chi non ha debiti." />
        
        <x-faq-item question="Se ho dei debiti, posso fare anche i corsi di potenziamento?" answer="Si, quando non avrai i corsi di recupero potrai frequentare i corsi di potenziamento." />
        
        <x-faq-item question="Quali sono gli orari della Viga Special Week?" answer="Dal Lunedì al Giovedì 8-14, Venerdì e Sabato dalle 8-12. Ma alcuni possono uscire prima" />

        <x-faq-item question="Se non ho debiti a che ora esco?" answer="Sempre alle 12" />

        <x-faq-item question="Se ho un solo debito a che ora esco?" answer=" Se hai un solo debito: quando non sei impegnato in attività di recupero, puoi uscire alle 12." />

        <x-faq-item question="Quale sarà il mio orario durante la settimana?" answer="Riceverai un orario personale diverso da quello normale, che ti sarà comunicato prima dell'inizio della settimana sull'app VSW" />

        <x-faq-item question="Devo venire a scuola anche se non ho debiti?" answer="Sì. Anche se non hai debiti, parteciperai ai corsi di potenziamento: sono ore di scuola a tutti gli effetti e la presenza è obbligatoria." />

        <x-faq-item question="Cosa faccio se ho uno o più debiti?" answer="Se la tua materia prevede un corso di recupero parteciperai al corso di recupero della materia. Si ricorda che i corsi di recupero sono obbligatori. Se non sei impegnato in recupero, potrai fare attività di potenziamento. Nell'ultimo modulo (dalle 12 alle 14) se non hai i recuperi sarai nei gruppi di studio assistito" />

        <x-faq-item question="Scegliendo un corso significa che sono automaticamente iscritto?" answer="No, stai esprimento la tua preferenza. In base al numero di debiti e quando hai espresso le tue preferenze sarai allocato a dei corsi che hai scelto" />

        <x-faq-item question="Se non esprimo alcuna preferenza sui corsi di potenziamento?" answer="Ti saranno assegnati dei corsi in modo casuali a cui sarai obbligato a partecipare" />

        <x-faq-item question="E se i corsi che voglio fare sono già pieni?" answer="Ti saranno assegnati dei corsi in modo casuali a cui sarai obbligato a partecipare. Per questo è importante scegliere con cura le preferenze dei corsi" />

        <x-faq-item question="Quando saprò a quali corsi posso partecipare?" answer="Quando vedrai il tuo orario definitivo della settimana" />

        <x-faq-item question="Devo studiare per verifiche durante la settimana?" answer="Durante la Viga Special Week non ci saranno verifiche o interrogazioni con voti validi per lo scrutinio. Le verifiche riprenderanno dopo la settimana di recupero." />

        <x-faq-item question="Le attività fuori da scuola sono obbligatorie?" answer="Sì. Se sei iscritto a un'attività che si svolge fuori dall'istituto (palestra, biblioteca, Croce Bianca, ecc.), devi partecipare come a una normale lezione. Sarai sempre accompagnato da docenti della scuola" />

      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-purple-900 py-3">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
      <p class="text-xs text-indigo-100">
        Made with <span class="text-red-500">❤️</span> by the Viga Special Week team
      </p>
    </div>
  </footer>

</div>

</body>
</html>
