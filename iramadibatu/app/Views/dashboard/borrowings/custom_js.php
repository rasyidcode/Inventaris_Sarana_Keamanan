<?=$this->section('custom-js')?>

<script src="<?=site_url('themes/AdminLTE/plugins/datatables/jquery.dataTables.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-buttons/js/buttons.html5.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-buttons/js/buttons.print.min.js')?>"></script>
<script src="<?=site_url('themes/AdminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js')?>"></script>

<script>
$(function() {
    // handles datatable
    const table = $('#data-borrowings').DataTable({
        "responsive": true,
        "drawCallback": function(settings) {
            if ($('#checkAll').is(":checked")) {
                $('.multi_delete').prop('checked', true);
            } else {
                $('.multi_delete').prop('checked', false);
            }
        },
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": function(data, callback, settings) {
            const dataUrl = '<?=site_url('api/dashboard/borrowings/datatables')?>';

            $.ajax({
                type: 'POST',
                url: dataUrl,
                dataType: 'json',
                data: data,
                headers: {
                    'Authorization': 'Bearer ' + accessToken
                },
                success: function(res) {
                    callback(res);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        },
        "columns": [
            {
                "targets": 0,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 1,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 2,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 3,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 4,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 5,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 6,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 7,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 8,
                "orderable": false,
                "searchable": false
            },
            {
                "targets": 9,
                "orderable": false,
                "searchable": false
            }
        ],
    });

    // handle show detail
    $('#data-borrowings tbody').on('click', 'tr td button.btn-primary', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Fitur belum tersedia!',
            showConfirmButton: true,
            timer: 2000
        });
    });

    // handle show detail
    $('#data-borrowings tbody').on('click', 'tr td button.btn-primary', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Fitur belum tersedia!',
            showConfirmButton: true,
            timer: 2000
        });
    });

    // handle show detail
    $('#data-borrowings tbody').on('click', 'tr td button.btn-info', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Fitur belum tersedia!',
            showConfirmButton: true,
            timer: 2000
        });
    });

    // handle show detail
    $('#data-borrowings tbody').on('click', 'tr td button.btn-danger', function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Fitur belum tersedia!',
            showConfirmButton: true,
            timer: 2000
        });
    });

    // handle multi delete
    $('#btn-delete-multiple').click(function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Fitur belum tersedia!',
            showConfirmButton: true,
            timer: 2000
        });
    });
});
</script>

<?=$this->endSection()?>