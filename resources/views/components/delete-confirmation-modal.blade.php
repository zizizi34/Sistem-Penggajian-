<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger bg-opacity-10 border-danger border-bottom-2">
        <h5 class="modal-title" id="deleteConfirmationLabel">
          <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Penghapusan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4">
        <p id="deleteConfirmationMessage" class="fs-6 mb-0">
          Apakah Anda yakin ingin menghapus item ini?
        </p>
      </div>
      <div class="modal-footer border-top pt-3">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-2"></i>Tidak
        </button>
        <form id="deleteConfirmationForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash me-2"></i>Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Setup delete confirmation modal for all delete buttons
    const deleteButtons = document.querySelectorAll('[data-delete-action]');
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    const deleteForm = document.getElementById('deleteConfirmationForm');
    const deleteMessage = document.getElementById('deleteConfirmationMessage');
    
    deleteButtons.forEach(function(btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        
        const action = this.getAttribute('data-delete-action');
        const itemName = this.getAttribute('data-delete-item') || 'item ini';
        const category = this.getAttribute('data-delete-category') || '';
        
        // Set message
        let message = 'Apakah Anda yakin ingin menghapus ';
        if (category) {
          message += category + ' <strong>' + itemName + '</strong>?';
        } else {
          message += '<strong>' + itemName + '</strong>?';
        }
        deleteMessage.innerHTML = message;
        
        // Set form action
        deleteForm.action = action;
        
        // Show modal
        modal.show();
      });
    });
  });
</script>
