<script>

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.sweet-confirm').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
    
                const title = this.getAttribute('data-title') || 'Are you sure?';
                const text = this.getAttribute('data-text') || 'This action cannot be undone.';
                const confirmText = this.getAttribute('data-confirm') || 'Yes, proceed!';
                const successMsg = this.getAttribute('data-success') || 'Action completed!';
                const href = this.getAttribute('data-href');
                console.log(href);
    
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmText
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverTriggerList.forEach(function (el) {
            new bootstrap.Popover(el);
        });
    });
      function showAlert(type,meaasge){
         Swal.fire({
         position: "top-end",
         icon: type,
         title: meaasge,
         showConfirmButton: false,
         timer: 1500
         });
      }
   </script>
   @if(session('success'))
     <script>showAlert("success","{{session('success')}}")</script>
   @elseif(session('error'))
   <script>showAlert("error","{{session('error')}}")</script>
   @endif


  
