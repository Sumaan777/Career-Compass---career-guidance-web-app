
window.showToast = function (message, type = 'success') {
    const toast = document.getElementById('cc-toast');
    const msg   = document.getElementById('cc-toast-message');
    const icon  = document.getElementById('cc-toast-icon');

    toast.className = `cc-toast cc-${type}`;
    msg.innerText = message;

    icon.className = 'bi';
    if (type === 'success') icon.classList.add('bi-check-circle-fill');
    if (type === 'error')   icon.classList.add('bi-x-circle-fill');
    if (type === 'warning') icon.classList.add('bi-exclamation-triangle-fill');
    if (type === 'info')    icon.classList.add('bi-info-circle-fill');

    toast.classList.remove('d-none');

    setTimeout(() => {
        toast.classList.add('d-none');
    }, 3000);
};
