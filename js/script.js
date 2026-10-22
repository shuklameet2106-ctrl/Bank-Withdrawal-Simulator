const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

function showMessage(element, message, type) {
    element.className = `notice ${type}`;
    element.textContent = message;
    element.hidden = false;
}

async function postForm(endpoint, form) {
    const response = await fetch(endpoint, {
        method: 'POST',
        body: new FormData(form),
        credentials: 'same-origin',
    });
    const result = await response.json();

    if (!response.ok) {
        throw new Error(result.error || 'Request failed. Please try again.');
    }

    return result;
}

const loginForm = document.querySelector('#login-form');
if (loginForm) {
    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = document.querySelector('#login-message');

        if (!loginForm.reportValidity()) {
            return;
        }

        try {
            await postForm('php/login.php', loginForm);
            window.location.href = 'dashboard.html';
        } catch (error) {
            showMessage(message, error.message, 'error');
        }
    });
}

const createForm = document.querySelector('#create-account-form');
if (createForm) {
    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = document.querySelector('#create-message');

        if (!createForm.reportValidity()) {
            return;
        }

        try {
            const result = await postForm('php/create_account.php', createForm);
            createForm.reset();
            showMessage(message, result.message, 'success');
        } catch (error) {
            showMessage(message, error.message, 'error');
        }
    });
}

const dashboard = document.querySelector('#dashboard');
if (dashboard) {
    const holderName = document.querySelector('#holder-name');
    const balanceAmount = document.querySelector('#balance-amount');
    const withdrawForm = document.querySelector('#withdraw-form');
    const withdrawMessage = document.querySelector('#withdraw-message');

    async function refreshBalance() {
        const response = await fetch('php/check_balance.php', {
            credentials: 'same-origin',
        });
        const result = await response.json();

        if (response.status === 401) {
            window.location.replace('index.html');
            return false;
        }

        if (!response.ok) {
            throw new Error(result.error || 'Could not load account details.');
        }

        holderName.textContent = result.name;
        balanceAmount.textContent = currency.format(result.balance_cents / 100);
        return true;
    }

    refreshBalance().catch((error) => {
        showMessage(withdrawMessage, error.message, 'error');
    });

    withdrawForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!withdrawForm.reportValidity()) {
            return;
        }

        try {
            const result = await postForm('php/withdraw.php', withdrawForm);
            balanceAmount.textContent = currency.format(result.balance_cents / 100);
            withdrawForm.reset();
            showMessage(withdrawMessage, result.message, 'success');
        } catch (error) {
            showMessage(withdrawMessage, error.message, 'error');
        }
    });

    document.querySelector('#logout-button').addEventListener('click', async () => {
        try {
            await fetch('php/logout.php', {
                method: 'POST',
                credentials: 'same-origin',
            });
        } finally {
            window.location.href = 'index.html';
        }
    });
}