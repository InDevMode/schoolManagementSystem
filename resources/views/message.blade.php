@if(session('primary'))
<x-alert type="primary" id="alert-primary" :message="session('primary')" />
@endif

@if(session('error'))
<x-alert type="error" id="alert-error" :message="session('error')" />
@endif

@if(session('success'))
<x-alert type="success" id="alert-success" :message="session('success')" />
@endif

@if(session('secondary'))
<x-alert type="secondary" id="alert-secondary" :message="session('secondary')" />
@endif

@if(session('light'))
<x-alert type="light" id="alert-light" :message="session('light')" />
@endif

<script>
    document.querySelectorAll('[data-dismiss-target]').forEach(button => {
        button.addEventListener('click', () => {
            const targetSelector = button.getAttribute('data-dismiss-target');
            const alert = document.querySelector(targetSelector);
            if (alert) {
                alert.remove();
            }
        });
    });
</script>
