document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('transaction-form');
    const transactionList = document.getElementById('transaction-list');
    const totalIncomeEl = document.getElementById('total-income');
    const totalExpenseEl = document.getElementById('total-expense');
    const totalBalanceEl = document.getElementById('total-balance');

    // Mappings
    const groupNames = { fixed: '固定', variable: '変動', none: '-' };

    const defaultCategories = [
        { name: '食費', icon: '🍎', id: 'food', group: 'variable' },
        { name: '買い物', icon: '🛍️', id: 'shopping', group: 'variable' },
        { name: '交通費', icon: '🚃', id: 'transport', group: 'variable' },
        { name: '娯楽', icon: '🎮', id: 'entertainment', group: 'variable' },
        { name: '住居・光熱費', icon: '🏠', id: 'housing', group: 'fixed' },
        { name: '給与', icon: '💰', id: 'salary', group: 'none' },
        { name: 'その他', icon: '📦', id: 'other', group: 'variable' }
    ];



    let transactions = [];
    let categories = [];
    let budgets = {};

    const categoryIcons = {};
    const categoryGroups = {};

    const householdId = localStorage.getItem('kkb_householdId');
    const userId = localStorage.getItem('kkb_userId');
    const currentRole = localStorage.getItem('kkb_currentUser') || 'member';
    const userName = localStorage.getItem('kkb_userName') || 'Guest';

    const roleNames = { admin: '管理者', editor: '入力者' };
    const roleIcons = { admin: '👑', editor: '✍️' };

    // Update Header UI
    const userDisplay = document.getElementById('user-display-name');
    const userRole = document.getElementById('user-role');
    const userAvatar = document.getElementById('user-avatar');
    if (userDisplay) userDisplay.textContent = userName;
    if (userRole) userRole.textContent = roleNames[currentRole] || 'メンバー';
    if (userAvatar) userAvatar.textContent = roleIcons[currentRole] || '👤';



    // --- Header user dropdown (logout only) ---
    const trigger = document.getElementById('user-profile-trigger');
    const dropdown = document.getElementById('user-dropdown');

    if (trigger && dropdown) {
        // Fill dropdown with just logout (no family members list)
        const membersEl = document.getElementById('family-members-list');
        if (membersEl) membersEl.remove();

        trigger.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
            const profile = document.getElementById('current-user-profile');
            if (profile && !profile.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    window.logout = () => {
        localStorage.clear();
        window.location.href = '/login';
    };



    const fetchData = async () => {
        if (!householdId) {
            window.location.href = '/login';
            return;
        }

        const headers = {
            'Content-Type': 'application/json',
            'X-Household-Id': householdId
        };

        try {
            // Fetch Categories
            const catRes = await fetch('/api/categories', { headers });
            categories = await catRes.json();

            const categorySelect = document.getElementById('category');
            if (categorySelect) {
                categorySelect.innerHTML = categories.map(cat =>
                    `<option value="${cat.id}">${cat.name} ${cat.icon}</option>`
                ).join('');
            }

            categories.forEach(cat => {
                categoryIcons[cat.id] = cat.icon;
                categoryGroups[cat.id] = cat.group;
            });

            // Fetch Transactions
            const txRes = await fetch('/api/transactions', { headers });
            transactions = await txRes.json();

            // Fetch Budgets
            const budgetRes = await fetch(`/api/budgets?month=${new Date().toISOString().slice(0, 7)}`, { headers });
            const budgetData = await budgetRes.json();
            budgets = {};
            budgetData.forEach(b => {
                budgets[b.category_id] = b.amount;
            });

            updateUI();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const updateUI = () => {
        if (!transactionList) return;
        transactionList.innerHTML = '';
        if (transactions.length === 0) {
            transactionList.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding-top: 3rem;">データがありません</div>';
        } else {
            transactions.forEach(t => {
                const div = document.createElement('div');
                div.className = 'transaction-item';
                div.style.position = 'relative';
                const isExpense = t.type === 'expense';
                const cat = t.category || { icon: '📦', group: 'variable' };
                const userDisplayName = t.user ? t.user.name : t.user_id;
                div.innerHTML = `
                    <div class="item-info">
                        <div class="item-icon">${cat.icon || '📦'}</div>
                        <div class="item-details">
                            <h4>${t.note || cat.name} <span style="font-size: 0.75rem; font-weight: 400; opacity: 0.7;">via ${userDisplayName}</span></h4>
                            <p>${t.date} · <span style="color: ${cat.group === 'fixed' ? '#38bdf8' : '#94a3b8'}">${groupNames[cat.group] || '-'}</span></p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                        <div class="item-amount ${isExpense ? 'amount-expense' : 'amount-income'}">
                            ${isExpense ? '-' : '+'}${(typeof KKB !== 'undefined' ? KKB.formatMoney(t.amount) : '¥' + Number(t.amount).toLocaleString())}
                        </div>
                        <button onclick="deleteTransaction(${t.id})" style="background: transparent; border: none; font-size: 0.75rem; color: var(--accent-color); cursor: pointer; opacity: 0.5;">削除</button>
                    </div>
                `;
                transactionList.appendChild(div);
            });
        }

        // Use period defined by month start day setting
        const period = typeof KKB !== 'undefined' ? KKB.getCurrentPeriod() : null;
        const periodTx = period
            ? transactions.filter(t => t.date >= period.startDate && t.date <= period.endDate)
            : transactions.filter(t => t.date.startsWith(new Date().toISOString().slice(0, 7)));

        const currentIncome = periodTx.filter(t => t.type === 'income').reduce((s, t) => s + Number(t.amount), 0);
        const currentExpense = periodTx.filter(t => t.type === 'expense').reduce((s, t) => s + Number(t.amount), 0);
        // Last period: shift back by one month
        const lastMonthDate = new Date();
        lastMonthDate.setMonth(lastMonthDate.getMonth() - 1);
        const lastMonth = lastMonthDate.toISOString().slice(0, 7);
        const lastIncome = transactions.filter(t => t.type === 'income' && t.date.startsWith(lastMonth)).reduce((s, t) => s + Number(t.amount), 0);
        const lastExpense = transactions.filter(t => t.type === 'expense' && t.date.startsWith(lastMonth)).reduce((s, t) => s + Number(t.amount), 0);

        animateValue(totalIncomeEl, currentIncome);
        animateValue(totalExpenseEl, currentExpense);
        animateValue(totalBalanceEl, currentIncome - currentExpense);

        // Update Trends
        const updateTrend = (elId, current, last) => {
            const el = document.querySelector(`#${elId}`).parentElement.querySelector('.card-trend');
            if (!el) return;
            if (last === 0) {
                el.textContent = '先月のデータなし';
                return;
            }
            const diff = ((current - last) / last) * 100;
            const isUp = diff > 0;
            el.className = `card-trend ${isUp ? 'trend-up' : 'trend-down'}`;
            el.textContent = `${isUp ? '↑' : '↓'} ${Math.abs(Math.round(diff))}% 先月比`;
        };
        updateTrend('total-income', currentIncome, lastIncome);
        updateTrend('total-expense', currentExpense, lastExpense);

        // Budget Summary Logic
        const totalBudget = Object.values(budgets).reduce((s, v) => s + Number(v), 0);
        const monthlyExpense = currentExpense;

        const budgetPercentEl = document.getElementById('budget-summary-percent');
        const budgetBarEl = document.getElementById('budget-summary-bar');
        if (budgetPercentEl && budgetBarEl) {
            const percent = totalBudget > 0 ? Math.min((monthlyExpense / totalBudget) * 100, 100) : 0;
            budgetPercentEl.textContent = `${Math.round(percent)}%`;
            budgetBarEl.style.width = `${percent}%`;
            if (monthlyExpense > totalBudget) budgetBarEl.style.background = 'var(--accent-color)';
        }
    };

    const animateValue = (el, value) => {
        if (!el) return;
        const start = parseInt(el.textContent.replace(/[¥,]/g, '')) || 0;
        const duration = 500;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            el.textContent = `¥${Math.floor(progress * (value - start) + start).toLocaleString()}`;
            if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
    };

    window.deleteTransaction = async (id) => {
        if (!confirm('この収支記録を削除しますか？')) return;
        try {
            const response = await fetch(`/api/transactions/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Household-Id': householdId
                }
            });
            if (response.ok) {
                await fetchData();
            }
        } catch (error) {
            console.error('Error deleting transaction:', error);
        }
    };

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const selectedCatId = document.getElementById('category').value;
            const newTx = {
                date: document.getElementById('date').value,
                type: document.getElementById('type').value,
                user_id: userId,
                category_id: selectedCatId,
                amount: document.getElementById('amount').value,
                note: document.getElementById('note').value
            };

            try {
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Household-Id': householdId
                    },
                    body: JSON.stringify(newTx)
                });
                if (response.ok) {
                    const currentD = document.getElementById('date').value;
                    form.reset();
                    document.getElementById('date').value = currentD;
                    await fetchData();
                }
            } catch (error) {
                console.error('Error saving transaction:', error);
            }
        });
    }

    const dateInput = document.getElementById('date');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

    fetchData();
});
