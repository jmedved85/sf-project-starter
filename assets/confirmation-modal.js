/* Delete user confirmation modal */
const userManagementTemplate = document.getElementById('user-management-template');

if (userManagementTemplate) {
    const deleteUserModal = document.getElementById('deleteUserModal');

    deleteUserModal.addEventListener('show.bs.modal', function (event) {
        const confirmDeleteButton = document.getElementById('confirmDeleteUserButton');
        let button = event.relatedTarget;
        let userId = button.getAttribute('data-user-id');

        confirmDeleteButton.href = confirmDeleteButton.dataset.url.replace('USER_ID', userId);
    });
}