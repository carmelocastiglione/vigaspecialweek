<x-layouts::home :title="'Benvenuto'">
  @if($isMaintenanceMode)
      <x-maintenance-banner />
  @endif
  @if($isImpersonating)
      <x-impersonation-banner />
  @endif

  <!-- Hero Section -->
  <div class="h-full relative overflow-hidden bg-gradient-to-b from-slate-950 via-purple-950 to-slate-900">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Top left gradient -->
      <div class="absolute -left-40 -top-40 h-80 w-80 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 opacity-20 blur-3xl"></div>
      <!-- Bottom right gradient -->
      <div class="absolute -right-40 bottom-0 h-80 w-80 rounded-full bg-gradient-to-l from-blue-500 to-cyan-500 opacity-20 blur-3xl"></div>
      <!-- Animated grid background -->
      <svg class="absolute inset-0 h-full w-full opacity-5" viewBox="0 0 1200 1200">
        <defs>
          <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
            <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="1200" height="1200" fill="url(#grid)" />
      </svg>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 py-32 sm:py-40 lg:px-8">
      <div class="mx-auto max-w-3xl text-center">
        <!-- Badge -->
        <div class="mb-8 inline-flex items-center rounded-full border border-purple-400/30 bg-purple-400/10 px-4 py-1.5 backdrop-blur">
          <span class="text-sm font-semibold bg-gradient-to-r from-purple-300 to-pink-300 bg-clip-text text-transparent">
            La tua settimana per approfondire, crescere e recuperare
          </span>
        </div>

        <!-- Main Heading -->
        <h1 class="mt-8 text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight">
          <span class="block text-white">Viga</span>
          <span class="block bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">
            Special Week
          </span>
        </h1>

        <!-- Subtitle -->
        <p class="mt-6 text-lg sm:text-xl leading-8 text-gray-300 max-w-2xl mx-auto">
          Approfondisci i tuoi interessi, recupera i debiti e conosci studenti che condividono le tue passioni. Una settimana straordinaria ti aspetta.
        </p>

        <!-- CTA Buttons -->
        @if (Route::has('login'))
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            @auth
              <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-2xl">
                Dashboard
              </a>
            @else
              <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-2xl">
                Entra subito
              </a>
            @endauth

            @if (Route::has('activities.index'))
              <a href="{{ route('activities.index') }}" class="px-8 py-4 text-base font-semibold rounded-lg border-2 border-purple-400 text-purple-300 hover:bg-purple-400/10 transition-all">
                Scopri le attività
              </a>
            @endif
          </div>
        @endif

        <!-- Navigation Links -->
        <div class="mt-12 flex items-center justify-center gap-6 flex-wrap text-sm">
          <a href="#attivita-disponibili" class="font-semibold text-gray-400 hover:text-purple-300 transition-colors flex items-center gap-2">
            Attività
          </a>
          <span class="hidden sm:inline text-gray-600">•</span>
          <a href="#come-funziona" class="font-semibold text-gray-400 hover:text-purple-300 transition-colors flex items-center gap-2">
            Come funziona
          </a>
          <span class="hidden sm:inline text-gray-600">•</span>
          <a href="#numeri" class="font-semibold text-gray-400 hover:text-purple-300 transition-colors flex items-center gap-2">
            Statistiche
          </a>
          <span class="hidden sm:inline text-gray-600">•</span>
          <a href="#faq" class="font-semibold text-gray-400 hover:text-purple-300 transition-colors flex items-center gap-2">
            FAQ
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Activities Section -->
  <div id="attivita-disponibili" class="relative py-24 sm:py-32 bg-white scroll-mt-0">
    <!-- Background decoration -->
    <div class="absolute left-0 top-0 -z-10 w-96 h-96 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 opacity-40 blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center mb-16">
        <div class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-4 py-1.5 mb-4">
          <span class="text-sm font-semibold text-purple-700">Le attività</span>
        </div>

        <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
          Scopri le attività di potenziamento disponibili
        </h2>

        <p class="text-lg text-gray-600">
          Puoi iniziare a sfogliare tutte le attività proposte anche senza accedere. Trova quello che fa per te e preparati a sperimentare nuove attività
        </p>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="group relative rounded-2xl border border-gray-200 bg-white p-8 hover:border-purple-400 hover:shadow-2xl transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 mb-6">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Attività varie</h3>
            <p class="text-gray-600">Sport, Teatro, Filosofia, Informatica, Scienze e tanto altro ancora. Qualcosa per ogni passione.</p>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="group relative rounded-2xl border border-gray-200 bg-white p-8 hover:border-purple-400 hover:shadow-2xl transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 mb-6">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Libertà di scelta</h3>
            <p class="text-gray-600">Seleziona fino a 20 attività che ti interessano di più. Se non scegli, il sistema farà una selezione casuale.</p>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="group relative rounded-2xl border border-gray-200 bg-white p-8 hover:border-purple-400 hover:shadow-2xl transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 mb-6">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Conosci altri studenti</h3>
            <p class="text-gray-600">Condividi i tuoi interessi con altri studenti. Ogni attività è un'opportunità di amicizia e crescita.</p>
          </div>
        </div>
      </div>

      <!-- CTA Button -->
      <div class="mt-16 text-center">
        @if (Route::has('activities.index'))
          <a href="{{ route('activities.index') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-2xl">
            Visualizza tutte le attività
          </a>
        @endif
      </div>
    </div>
  </div>

  <!-- How It Works Section -->
  <div id="come-funziona" class="relative py-24 sm:py-32 bg-gradient-to-b from-gray-50 to-white scroll-mt-0">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center mb-16">
        <div class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-4 py-1.5 mb-4">
          <span class="text-sm font-semibold text-purple-700">Come funziona</span>
        </div>

        <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
          Scopri come iscriverti ai corsi di potenziamento
        </h2>

        <p class="text-lg text-gray-600">
          Un percorso semplice in 4 step per iscriverti e iniziare la tua avventura
        </p>
      </div>

      <!-- Desktop Timeline -->
      <div class="hidden md:block">
        <div class="relative">
          <!-- Connecting Line -->
          <div class="absolute top-20 left-0 right-0 h-1 bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400" style="width: calc(100% - 3rem); margin-left: 1.5rem;"></div>
          
          <!-- Steps -->
          <div class="grid grid-cols-4 gap-8 relative z-10">
            <!-- Step 1 -->
            <div class="flex flex-col items-center">
              <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white font-bold text-2xl shadow-lg mb-4 ring-4 ring-white">
                1
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Sfoglia</h3>
              <p class="text-gray-600 text-center text-sm">
                Visita la sezione attività e scopri tutte le proposte disponibili
              </p>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center">
              <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 text-white font-bold text-2xl shadow-lg mb-4 ring-4 ring-white">
                2
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Accedi</h3>
              <p class="text-gray-600 text-center text-sm">
                Accedi per selezionare le attività che più ti interessano
              </p>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center">
              <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 text-white font-bold text-2xl shadow-lg mb-4 ring-4 ring-white">
                3
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Scegli</h3>
              <p class="text-gray-600 text-center text-sm">
                Seleziona fino a 20 attività o lascia che il sistema scelga per te
              </p>
            </div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center">
              <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-emerald-500 text-white font-bold text-2xl shadow-lg mb-4 ring-4 ring-white">
                4
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Partecipa</h3>
              <p class="text-gray-600 text-center text-sm">
                Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Accordion -->
      <div class="md:hidden space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-4 p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white font-bold shrink-0">1</div>
            <div>
              <h3 class="font-bold text-gray-900">Sfoglia</h3>
              <p class="text-sm text-gray-600">Visita la sezione attività e scopri tutte le proposte disponibili</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-4 p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 text-white font-bold shrink-0">2</div>
            <div>
              <h3 class="font-bold text-gray-900">Accedi</h3>
              <p class="text-sm text-gray-600">Accedi per selezionare le attività che più ti interessano</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-4 p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 text-white font-bold shrink-0">3</div>
            <div>
              <h3 class="font-bold text-gray-900">Scegli</h3>
              <p class="text-sm text-gray-600">Seleziona fino a 20 attività o lascia che il sistema scelga per te</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-4 p-6">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-emerald-500 text-white font-bold shrink-0">4</div>
            <div>
              <h3 class="font-bold text-gray-900">Partecipa</h3>
              <p class="text-sm text-gray-600">Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Section -->
  <div id="numeri" class="relative py-24 sm:py-32 bg-gradient-to-r from-purple-950 via-slate-900 to-blue-950 scroll-mt-0 overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-gradient-to-l from-cyan-500/20 to-transparent blur-3xl"></div>
    <div class="absolute left-0 bottom-0 w-96 h-96 rounded-full bg-gradient-to-r from-purple-500/20 to-transparent blur-3xl"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center mb-16">
        <div class="inline-flex items-center rounded-full border border-purple-400/30 bg-purple-400/10 px-4 py-1.5 backdrop-blur mb-4">
          <span class="text-sm font-semibold text-purple-300">I numeri</span>
        </div>

        <h2 class="text-4xl sm:text-5xl font-bold text-white mb-6">
          I numeri della Viga Special Week
        </h2>

        <p class="text-lg text-gray-300">
          Scopri cosa puoi trovare in questa settimana speciale
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Stat 1: Activities -->
        <div class="group relative rounded-2xl border border-purple-400/30 bg-gradient-to-br from-white/10 to-purple-400/10 backdrop-blur-xl p-8 hover:border-purple-300 hover:from-white/20 transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-600/0 to-pink-600/0 group-hover:from-purple-600/10 group-hover:to-pink-600/10 transition-colors duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 mb-6 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-purple-300 to-pink-300 bg-clip-text mb-2">
              {{ $activitiesCount ?? '-' }}
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Attività disponibili</h3>
            <p class="text-gray-300 text-sm">Per tutti gli interessi</p>
          </div>
        </div>

        <!-- Stat 2: Teachers -->
        <div class="group relative rounded-2xl border border-cyan-400/30 bg-gradient-to-br from-white/10 to-cyan-400/10 backdrop-blur-xl p-8 hover:border-cyan-300 hover:from-white/20 transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-cyan-600/0 to-blue-600/0 group-hover:from-cyan-600/10 group-hover:to-blue-600/10 transition-colors duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 mb-6 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-cyan-300 to-blue-300 bg-clip-text mb-2">
              {{ $teachersCount ?? '-' }}
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Insegnanti coinvolti</h3>
            <p class="text-gray-300 text-sm">Con il supporto di enti ed esperti esterni</p>
          </div>
        </div>

        <!-- Stat 3: Hours -->
        <div class="group relative rounded-2xl border border-yellow-400/30 bg-gradient-to-br from-white/10 to-yellow-400/10 backdrop-blur-xl p-8 hover:border-yellow-300 hover:from-white/20 transition-all duration-300">
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-yellow-600/0 to-orange-600/0 group-hover:from-yellow-600/10 group-hover:to-orange-600/10 transition-colors duration-300"></div>
          <div class="relative">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 mb-6 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-5xl font-bold text-transparent bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text mb-2">
              {{ $enrichmentHours ?? '-' }}h
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Ore di arricchimento</h3>
            <p class="text-gray-300 text-sm">Dedicate alla tua crescita personale</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FAQ Section -->
  <div id="faq" class="relative py-24 sm:py-32 bg-white scroll-mt-0">
    <!-- Background decoration -->
    <div class="absolute right-0 top-0 -z-10 w-96 h-96 rounded-full bg-gradient-to-bl from-pink-100 to-purple-100 opacity-40 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-3xl text-center mb-16">
        <div class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-4 py-1.5 mb-4">
          <span class="text-sm font-semibold text-purple-700">Domande frequenti</span>
        </div>

        <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
          FAQ
        </h2>

        <p class="text-lg text-gray-600">
          Qui troverai le risposte alle domande più comuni sulla Viga Special Week
        </p>
      </div>

      <!-- FAQ Items -->
      <div class="mx-auto max-w-4xl space-y-4">
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
  <footer class="relative bg-gradient-to-r from-purple-950 to-slate-900 border-t border-purple-800">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-6 sm:py-8">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-gray-400 text-sm">
          Made with <span class="text-red-500">❤️</span> by the Viga Special Week Team
        </p>
        <p class="text-gray-400 text-sm flex items-center gap-2">
          Puoi trovare il codice sorgente su 
          <a href="https://github.com/carmelocastiglione/vigaspecialweek" class="text-gray-400 hover:text-gray-200 transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v 3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
            </svg>
            GitHub
          </a>
        </p>
      </div>
    </div>
  </footer>

</x-layouts::home>