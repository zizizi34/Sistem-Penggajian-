<script>
  $(function () {
    $('#createOfficerModal').on('shown.bs.modal', () => {
      $('#createOfficerModal').find('input').not('[type=hidden]')[0].focus();
    });

    $('#editOfficerModal').on('shown.bs.modal', () => {
      $('#editOfficerModal').find('input').not('[type=hidden]')[0].focus();
    });

    $('.datatable').on('click', '.editOfficerButton', function (e) {
      let id = $(this).data('id');
      let name = $(this).data('name');
      let email = $(this).data('email');
      let phone_number = $(this).data('phone_number');
      let id_departemen = $(this).data('id_departemen');
      let updateURL = "{{ route('administrators.officers.update', 'param') }}";
      updateURL = updateURL.replace('param', id);

      $('#editOfficerModal #name_edit').val(name);
      $('#editOfficerModal #email_edit').val(email);
      $('#editOfficerModal #phone_number_edit').val(phone_number);
      $('#editOfficerModal #id_departemen_edit').val(id_departemen);
      $('#editOfficerModal form').attr('action', updateURL);
      
      // Clear password fields on edit
      $('#editOfficerModal #password_edit').val('');
      $('#editOfficerModal #password_confirmation_edit').val('');
    });
  });
</script>
