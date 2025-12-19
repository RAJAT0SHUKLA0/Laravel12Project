            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Velzon.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by Themesbrand
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('js/plugins.js')}}"></script>
    <script src="{{asset('js/pages/dashboard-ecommerce.init.js')}}"></script>
    <script src="{{asset('libs/list.pagination.js/list.pagination.min.js')}}"></script>
    <script src="{{asset('libs/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('js/flatpickr.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('js/funcation.js')}}"></script>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{asset('js/ajax/ajax.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function refreshData(){
            location.reload(true);
        }
        $(document).ready(function() {
            $('.beat').select2();
            
             flatpickr("#datepicker", {
    dateFormat: "Y-m-d"
  });
  
    flatpickr("#datepickercurrent", {
        dateFormat: "Y-m-d",
        defaultDate: new Date() // sets today's date
    });
            
        });
    </script>
<script>
    window.chartData = {!! isset($chartData) ? json_encode($chartData) : '{}' !!};

    $(document).on('change', '#selectAll', function () {
        let isChecked = $(this).is(':checked');
        $('.item-checkbox').prop('checked', isChecked);
    });

    // When any individual checkbox is clicked, update Select All state
    $(document).on('change', '.item-checkbox', function () {
        let total = $('.item-checkbox').length;
        let checked = $('.item-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });

    // Optional: clear "Select All" when table is reloaded via AJAX
    $(document).ajaxComplete(function() {
        $('#selectAll').prop('checked', false);
    });

</script>


    @include('admin.partial.map')
    @include('admin.partial.toastr')


</body>
</html>