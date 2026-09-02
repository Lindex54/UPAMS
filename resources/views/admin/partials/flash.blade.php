@if (session('success'))
    <div class="mb-5 rounded-xl border border-emerald-300 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">{{ session('success') }}</div>
@endif
@if (isset($errors) && $errors->any())
    <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200" role="alert">
        <p class="font-semibold">Please correct the highlighted information.</p>
        <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
