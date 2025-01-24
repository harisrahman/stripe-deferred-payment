export const setBtnLoading = (btn, isLoading) => {
    // Disable form submission while loading
    btn.disabled = isLoading;

    const spinnerEl = btn.querySelector('[role="status"]');

    if (!spinnerEl) return;

    if (isLoading) {
        spinnerEl.classList.remove("hidden");
        spinnerEl.classList.add("inline-block");
    } else {
        spinnerEl.classList.remove("inline-block");
        spinnerEl.classList.add("hidden");
    }
};
