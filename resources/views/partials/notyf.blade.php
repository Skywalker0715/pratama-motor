<script>
    const notyf = new Notyf({
        duration: 5000,
        position: {
            x: 'right',
            y: 'top',
        },
        dismissible: true
    });

    @if(session('success'))
        notyf.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        notyf.error('{{ session('error') }}');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            notyf.error('{{ $error }}');
        @endforeach
    @endif
</script>