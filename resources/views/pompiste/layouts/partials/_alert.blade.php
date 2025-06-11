<!-- DANS resources/views/partials/_alert.blade.php -->
@if (session('success_modal'))
<script>
    Swal.fire({
        title: 'Succès !',
        text: "{{ session('success_modal') }}",
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 3000, // Le pop-up se ferme après 3 secondes
        timerProgressBar: true
    });
</script>
@endif

@if (session('error_modal'))
<script>
    Swal.fire({
        title: 'Erreur !',
        text: "{{ session('error_modal') }}",
        icon: 'error',
        confirmButtonText: 'Compris'
    });
</script>
@endif

{{-- Gère les erreurs de validation spécifiques à un modal --}}
@if (!empty($errorBag) && $errors->hasBag($errorBag))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Trouvez le premier message d'erreur pour ce "error bag"
        const firstError = @json($errors->{$errorBag}->first());

        Swal.fire({
            title: 'Action impossible',
            html: `Veuillez proceder à un ravitaillement svp :<br><strong>${firstError}</strong>`,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif