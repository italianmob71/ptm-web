@if ($errors->any())
    <div role="alert" class="mb-4 px-4 py-3 rounded-lg border" style="border-color: var(--color-danger); color: var(--color-danger);">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
