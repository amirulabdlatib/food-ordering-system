@if (session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                timer: 3000
            });
        });
    </script>
@endif

@if (session()->has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Not Succeed!',
                text: '{{ session('error') }}',
                showConfirmButton: true,
                timer: 3000
            });
        });
    </script>
@endif