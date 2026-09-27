<x-layouts::home :title="'Benvenuto'">
  @if($isMaintenanceMode)
      <x-maintenance-banner />
  @endif
  @if($isImpersonating)
      <x-impersonation-banner />
  @endif

  <!-- Hero Section -->
  <div class="relative isolate bg-white px-6 pt-56 pb-32 lg:px-8 flex items-center justify-center min-h-screen">
    <div aria-hidden="true" class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"></div>
    </div>

    <div class="mx-auto max-w-2xl">
      <div class="text-center">
        <flux:heading size="2xl" level="1">
          Viga Special Week
        </flux:heading>

        <flux:heading size="lg" class="mt-4" level="2">
          La tua settimana per approfondire, crescere e recuperare.
        </flux:heading>

        @if (Route::has('login'))
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6">
            @auth
              <flux:button variant="primary" href="{{ route('dashboard') }}" class="px-8 py-3 text-base">
                Dashboard
              </flux:button>
            @else
              <flux:button variant="primary" href="{{ route('login') }}" class="px-8 py-3 text-base">
                Entra
              </flux:button>
            @endauth

            @if (Route::has('activities.index'))
              <flux:button variant="ghost" href="{{ route('activities.index') }}" size="md" class="px-8">
                Attività disponibili
              </flux:button>
            @endif
          </div>
        @endif

        <div class="mt-8 flex items-center justify-center gap-4 flex-wrap text-sm">
          <flux:link href="#attivita-disponibili">scopri le attività</flux:link>
          <span class="text-gray-400">•</span>
          <flux:link href="#come-funziona">come funziona</flux:link>
          <span class="text-gray-400">•</span>
          <flux:link href="#numeri">i numeri</flux:link>
          <span class="text-gray-400">•</span>
          <flux:link href="#faq">FAQ</flux:link>
        </div>
      </div>
    </div>

    <div aria-hidden="true" class="absolute inset-x-0 bottom-0 -z-10 transform-gpu overflow-hidden blur-3xl">
      <div style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"></div>
    </div>
  </div>
  <!-- Call to Action Section - View Activities -->
  <div id="attivita-disponibili" class="bg-linear-to-br from-slate-900 via-purple-900 to-slate-900 py-24 sm:py-32 mt-12 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <flux:heading size="2xl" class="text-white mb-3">
          Scopri le attività di potenziamento disponibili
        </flux:heading>

        <flux:heading size="lg" class="mb-8 text-gray-300">
          Puoi iniziare a sfogliare tutte le attività proposte anche senza accedere. Trova quello che fa per te e preparati a sperimentare nuove attività
        </flux:heading>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          @if (Route::has('activities.index'))
            <flux:button variant="primary" href="{{ route('activities.index') }}">
              Visualizza attività
            </flux:button>
          @endif

          @if (Route::has('login'))
            @auth
              <flux:button variant="outline" href="{{ route('dashboard') }}">
                Vai alla dashboard
              </flux:button>
            @else
              <flux:button variant="outline" href="{{ route('login') }}">
                Accedi per iscriverti
              </flux:button>
            @endauth
          @endif
        </div>
      </div>

      <!-- Features Grid -->
      <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Feature 1 -->
          <flux:card class="bg-white/10 border-white/20 backdrop-blur">
            <div class="flex items-start gap-4">
              <div class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 shrink-0 mt-1">
                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="text-left">
                <flux:text weight="semibold" class="text-white">Attività varie</flux:text>
                <flux:text class="text-gray-300 text-sm">Sport, Teatro, Filosofia, Informatica e tanto altro</flux:text>
              </div>
            </div>
          </flux:card>

          <!-- Feature 2 -->
          <flux:card class="bg-white/10 border-white/20 backdrop-blur">
            <div class="flex items-start gap-4">
              <div class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 shrink-0 mt-1">
                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="text-left">
                <flux:text weight="semibold" class="text-white">Libertà di scelta</flux:text>
                <flux:text class="text-gray-300 text-sm">Segui le attività che ti interessano</flux:text>
              </div>
            </div>
          </flux:card>

          <!-- Feature 3 -->
          <flux:card class="bg-white/10 border-white/20 backdrop-blur">
            <div class="flex items-start gap-4">
              <div class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 shrink-0 mt-1">
                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="text-left">
                <flux:text weight="semibold" class="text-white">Conosci altri studenti</flux:text>
                <flux:text class="text-gray-300 text-sm">Condividi i tuoi interessi</flux:text>
              </div>
            </div>
          </flux:card>
        </div>
      </div>
    </div>
  </div>
  <!-- How It Works Section -->
  <div id="come-funziona" class="bg-white py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <flux:heading class="text-4xl sm:text-5xl text-gray-900 mb-4 font-bold">
          Come funziona
        </flux:heading>

        <flux:text class="text-lg text-gray-600">
          Scopri come iscriverti ai corsi di potenziamento della Viga Special Week in pochi semplici step
        </flux:text>
      </div>

      <!-- Desktop View -->
      <div class="hidden md:block mx-auto max-w-6xl">
        <div class="flex items-start justify-between">
          <!-- Step 1 -->
          <div class="flex flex-col items-center flex-1">
            <button class="rounded-full h-16 w-16 px-0 mb-4 flex items-center justify-center bg-indigo-600 text-white font-bold text-2xl shadow-lg" disabled>
              1
            </button>
            <flux:heading size="sm" class="text-gray-900 mb-2">Sfoglia</flux:heading>
            <flux:text class="text-gray-600 text-sm text-center">
              Visita la sezione attività e guarda tutte le proposte disponibili
            </flux:text>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center flex-1">
            <button class="rounded-full h-16 w-16 px-0 mb-4 flex items-center justify-center bg-indigo-600 text-white font-bold text-2xl shadow-lg" disabled>
              2
            </button>
            <flux:heading size="sm" class="text-gray-900 mb-2">Accedi</flux:heading>
            <flux:text class="text-gray-600 text-sm text-center">
              Accedi per selezionare le attività che più ti interessano
            </flux:text>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center flex-1">
            <button class="rounded-full h-16 w-16 px-0 mb-4 flex items-center justify-center bg-indigo-600 text-white font-bold text-2xl shadow-lg" disabled>
              3
            </button>
            <flux:heading size="sm" class="text-gray-900 mb-2">Scegli</flux:heading>
            <flux:text class="text-gray-600 text-sm text-center">
              Seleziona le 20 attività che più ti interessano (se non scegli, il sistema ti assegnerà altre attività in automatico)
            </flux:text>
          </div>

          <!-- Step 4 -->
          <div class="flex flex-col items-center flex-1">
            <button class="rounded-full h-16 w-16 px-0 mb-4 flex items-center justify-center bg-indigo-600 text-white font-bold text-2xl shadow-lg" disabled>
              4
            </button>
            <flux:heading size="sm" class="text-gray-900 mb-2">Partecipa</flux:heading>
            <flux:text class="text-gray-600 text-sm text-center">
              Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni
            </flux:text>
          </div>
        </div>
      </div>

      <!-- Mobile view - simplified -->
      <div class="md:hidden mx-auto max-w-md space-y-6">
        <flux:card>
          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold shrink-0 shadow-lg">1</div>
            <div class="pt-1">
              <flux:heading size="sm" class="text-gray-900 mb-1">Sfoglia</flux:heading>
              <flux:text class="text-sm text-gray-600">Visita la sezione attività e guarda tutte le proposte disponibili</flux:text>
            </div>
          </div>
        </flux:card>

        <flux:card>
          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold shrink-0 shadow-lg">2</div>
            <div class="pt-1">
              <flux:heading size="sm" class="text-gray-900 mb-1">Accedi</flux:heading>
              <flux:text class="text-sm text-gray-600">Accedi per selezionare le attività che più ti interessano</flux:text>
            </div>
          </div>
        </flux:card>

        <flux:card>
          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold shrink-0 shadow-lg">3</div>
            <div class="pt-1">
              <flux:heading size="sm" class="text-gray-900 mb-1">Scegli</flux:heading>
              <flux:text class="text-sm text-gray-600">Seleziona le 20 attività che più ti interessano (se non scegli, il sistema ti assegnerà altre attività in automatico)</flux:text>
            </div>
          </div>
        </flux:card>

        <flux:card>
          <div class="flex gap-4 items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600 text-white font-bold shrink-0 shadow-lg">4</div>
            <div class="pt-1">
              <flux:heading size="sm" class="text-gray-900 mb-1">Partecipa</flux:heading>
              <flux:text class="text-sm text-gray-600">Dal 18/1 visualizza il tuo piano attività e partecipa alle lezioni</flux:text>
            </div>
          </div>
        </flux:card>
      </div>
    </div>
  </div>
  <!-- Statistics Section -->
  <div id="numeri" class="bg-linear-to-br from-slate-900 via-purple-900 to-slate-900 py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <flux:heading class="text-3xl sm:text-5xl text-white mb-4 font-bold">
          I numeri della Viga Special Week
        </flux:heading>

        <flux:text class="text-lg text-indigo-100">
          Cosa puoi trovare in questa settimana speciale
        </flux:text>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-3 lg:gap-12">
        <!-- Stat 1: Activities -->
        <flux:card class="bg-white/10 border-white/20 backdrop-blur text-center">
          <div class="flex justify-center mb-6">
            <div class="rounded-full bg-white/20 p-8">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
          <flux:text weight="semibold" class="text-5xl text-white mb-2">{{ $activitiesCount ?? '-' }}</flux:text>
          <flux:heading size="sm" class="text-indigo-100 font-semibold mb-1">Attività disponibili</flux:heading>
          <flux:text class="text-sm text-indigo-200">Per tutti gli interessi</flux:text>
        </flux:card>

        <!-- Stat 2: Teachers -->
        <flux:card class="bg-white/10 border-white/20 backdrop-blur text-center">
          <div class="flex justify-center mb-6">
            <div class="rounded-full bg-white/20 p-8">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5m0 0l9 5m-9-5v10l9 5m-9-5l9 5m9-5v10l-9 5m0-10l-9-5" />
              </svg>
            </div>
          </div>
          <flux:text weight="semibold" class="text-5xl text-white mb-2">{{ $teachersCount ?? '-'}}</flux:text>
          <flux:heading size="sm" class="text-indigo-100 font-semibold mb-1">Insegnanti coinvolti</flux:heading>
          <flux:text class="text-sm text-indigo-200">Con il supporto di enti ed esperti esterni</flux:text>
        </flux:card>

        <!-- Stat 3: Hours -->
        <flux:card class="bg-white/10 border-white/20 backdrop-blur text-center">
          <div class="flex justify-center mb-6">
            <div class="rounded-full bg-white/20 p-8">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <flux:text weight="semibold" class="text-5xl text-white mb-2">{{ $enrichmentHours ?? '-'}}</flux:text>
          <flux:heading size="sm" class="text-indigo-100 font-semibold mb-1">Ore di arricchimento</flux:heading>
          <flux:text class="text-sm text-indigo-200">Dedicate alla tua crescita personale</flux:text>
        </flux:card>
      </div>
    </div>
  </div>
  <!-- FAQ Section -->
  <div id="faq" class="bg-white py-24 sm:py-32 scroll-mt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center mb-16">
        <flux:heading class="text-4xl sm:text-5xl text-gray-900 mb-4 font-bold">
          Domande frequenti
        </flux:heading>

        <flux:text class="text-lg text-gray-600">
          Qui troverai le risposte alle domande più comuni sulla Viga Special Week
        </flux:text>
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
      <flux:text size="sm" class="text-indigo-100">
        Made with <span class="text-red-500">❤️</span> by the Viga Special Week team
      </flux:text>
    </div>
  </footer>

</x-layouts::home>