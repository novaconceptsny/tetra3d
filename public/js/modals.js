
let modalsElement = document.getElementById('livewire-modals');

modalsElement.addEventListener('hidden.bs.modal', () => {
    Livewire.emit('resetModal');
});

Livewire.on('showBootstrapModal', () => {
    let modal = bootstrap.Modal.getInstance(modalsElement);

    if (!modal) {
        modal = new bootstrap.Modal(modalsElement);
    }

    modal.show();
});

Livewire.on('hideModal', () => {
    let modal = bootstrap.Modal.getInstance(modalsElement);
    modal.hide();
});
