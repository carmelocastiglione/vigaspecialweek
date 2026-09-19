<div class="bg-yellow-50 border-b-4 border-yellow-600">
  <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm font-medium text-yellow-900">
          You are impersonating <strong>{{ auth()->user()->name }} {{ auth()->user()->surname }}</strong>
        </p>
      </div>
      <form method="POST" action="{{ route('admin.impersonate.stop') }}">
        @csrf
        <button type="submit" class="ml-4 px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition-colors">
          Return to your account
        </button>
      </form>
    </div>
  </div>
</div>
