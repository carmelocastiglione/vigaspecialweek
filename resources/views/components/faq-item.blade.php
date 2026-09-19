<details class="group border border-gray-300 rounded-lg p-6 hover:border-indigo-500 transition-colors duration-200 cursor-pointer">
  <summary class="flex items-center justify-between font-semibold text-gray-900 text-lg select-none">
    {{ $question }}
    <span class="transition group-open:rotate-180">
      <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
    </span>
  </summary>
  <p class="text-gray-600 text-base mt-4">
    {!! $answer !!}
  </p>
</details>
