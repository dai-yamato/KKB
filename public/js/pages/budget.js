document.addEventListener('DOMContentLoaded', () => {
    const budgetListEl = document.getElementById('category-budgets');
    const totalBudgetView = document.getElementById('total-budget-view');
    const totalBudgetRemaining = document.getElementById('total-budget-remaining');
    const budgetUsagePercent = document.getElementById('budget-usage-percent');
    const totalBudgetProgress = document.getElementById('total-budget-progress');

    let categories = [];
    let transactions = [];
    let budgets = {};

    const currentMonth = new Date().toISOString().slice(0, 7); // YYYY-MM
    const householdId = localStorage.getItem('kkb_householdId');
    if (!householdId) {
        window.location.href = '/login';
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Household-Id': householdId
    };

    const getMonthlySpent = (categoryId) => {
        return transactions
            .filter(t => t.category_id === categoryId && t.date.startsWith(currentMonth) && t.type === 'expense')
            .reduce((sum, t) => sum + Number(t.amount), 0);
    };

    const fetchData = async () => {
        try {
            const catRes = await fetch('/api/categories', { headers });
            categories = await catRes.json();

            const txRes = await fetch('/api/transactions', { headers });
            transactions = await txRes.json();

            const budgetRes = await fetch(`/api/budgets?month=${currentMonth}`, { headers });
            const budgetData = await budgetRes.json();
            budgets = {};
            budgetData.forEach(b => {
                budgets[b.category_id] = b.amount;
            });

            renderBudgets();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const renderBudgets = () => {
        budgetListEl.innerHTML = '';
        let totalBudget = 0;
        let totalSpent = 0;

        categories.forEach(cat => {
            if (cat.slug === 'salary') return; // Income doesn't have budget

            const spent = getMonthlySpent(cat.id);
            const budget = budgets[cat.id] || 0;
            const percent = budget > 0 ? Math.min((spent / budget) * 100, 100) : 0;
            const isOver = budget > 0 && spent > budget;

            totalBudget += Number(budget);
            totalSpent += spent;

            const card = document.createElement('div');
            card.className = 'budget-item';
            card.innerHTML = `
                <div class="budget-info">
                    <div class="budget-name">
                        <span>${cat.icon}</span>
                        ${cat.name}
                    </div>
                    <div class="budget-amounts">
                        <div class="budget-current">¥${spent.toLocaleString()}</div>
                        <div class="budget-total">/ ¥${Number(budget).toLocaleString()}</div>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${percent}%; background: ${isOver ? 'var(--accent-color)' : 'var(--primary-color)'}"></div>
                </div>
                <div class="progress-status">
                    <span class="${isOver ? 'status-over' : 'status-ok'}">
                        ${isOver ? '予算超過！' : '予算内'}
                    </span>
                    <span>残り ¥${budget > 0 ? Math.max(budget - spent, 0).toLocaleString() : 0}</span>
                </div>
                <div class="budget-input-group">
                    <input type="number" id="input-${cat.id}" placeholder="予算額を入力" value="${budget > 0 ? budget : ''}">
                    <button class="btn-save-budget" onclick="saveBudget(${cat.id})">設定</button>
                </div>
            `;
            budgetListEl.appendChild(card);
        });

        // Update Summary
        totalBudgetView.textContent = `¥${totalBudget.toLocaleString()}`;
        const remaining = Math.max(totalBudget - totalSpent, 0);
        totalBudgetRemaining.textContent = totalBudget > 0 ? `残り ¥${remaining.toLocaleString()}` : '予算を全設定しましょう';

        const totalPercent = totalBudget > 0 ? Math.min((totalSpent / totalBudget) * 100, 100) : 0;
        budgetUsagePercent.textContent = `${Math.round(totalPercent)}%`;
        totalBudgetProgress.style.width = `${totalPercent}%`;
        totalBudgetProgress.style.background = totalSpent > totalBudget ? 'var(--accent-color)' : 'var(--primary-color)';
    };

    window.saveBudget = async (categoryId) => {
        const input = document.getElementById(`input-${categoryId}`);
        const value = input.value;
        if (value === '' || value < 0) return;

        try {
            const response = await fetch('/api/budgets', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({
                    category_id: categoryId,
                    amount: value,
                    month: currentMonth
                })
            });
            if (response.ok) {
                await fetchData();
            }
        } catch (error) {
            console.error('Error saving budget:', error);
        }
    };

    fetchData();
});
