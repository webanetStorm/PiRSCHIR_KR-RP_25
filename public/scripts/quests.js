document.addEventListener('DOMContentLoaded', () => {
    const updateVisibility = (type) => {
        document.getElementById('minParticipantsGroup')?.classList.toggle('hidden', type !== 'collective');
        document.getElementById('deadlineGroup')?.classList.toggle('hidden', type !== 'timed');
    };

    const typeInputs = document.querySelectorAll('input[name="type"]');
    typeInputs.forEach(input => {
        if (input.checked) updateVisibility(input.value);
    });

    typeInputs.forEach(input => {
        input.addEventListener('change', () => updateVisibility(input.value));
    });

    document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', e => {
            const msg = link.dataset.confirm;
            if (msg && !confirm(msg)) e.preventDefault();
        });
    });
});
