document.addEventListener('DOMContentLoaded', () => {
    const compGridEl = document.getElementById('category-comparison');
    const totalDiffPercent = document.getElementById('total-diff-percent');
    const totalDiffLabel = document.getElementById('total-diff-label');
    const totalDiffAmount = document.getElementById('total-diff-amount');
    const totalCompCard = document.getElementById('total-comp-card');

    const targetMonthSelect = document.getElementById('target-month-select');
    const baseMonthSelect = document.getElementById('base-month-select');

    let categories = [];
    let transactions = [];

    // Initialize selectors
    const now = new Date();
    const currentMonthStr = now.toISOString().slice(0, 7);
    const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    const lastMonthStr = lastMonth.toISOString().slice(0, 7);

    targetMonthSelect.value = currentMonthStr;
    baseMonthSelect.value = lastMonthStr;

    const getSpentForMonth = (categoryId, monthStr) => {
        return transactions
            .filter(t => t.category_id === categoryId && t.date.startsWith(monthStr) && t.type === 'expense')
            .reduce((sum, t) => sum + Number(t.amount), 0);
    };

    const householdId = localStorage.getItem('kkb_householdId');
    if (!householdId) {
        window.location.href = '/login';
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Household-Id': householdId
    };

    const fetchData = async () => {
        try {
            const catRes = await fetch('/api/categories', { headers });
            categories = await catRes.json();

            const txRes = await fetch('/api/transactions', { headers });
            transactions = await txRes.json();

            renderComparison();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const renderComparison = () => {
        const targetMonth = targetMonthSelect.value;
        const baseMonth = baseMonthSelect.value;

        const targetLabel = `${targetMonth.split('-')[1]}月`;
        const baseLabel = `${baseMonth.split('-')[1]}月`;

        compGridEl.innerHTML = '';
        let totalCurrent = 0;
        let totalBase = 0;

        categories.forEach(cat => {
            if (cat.slug === 'salary') return;

            const current = getSpentForMonth(cat.id, targetMonth);
            const last = getSpentForMonth(cat.id, baseMonth);
            const diff = current - last;
            let percent = 0;
            if (last > 0) {
                percent = Math.round((diff / last) * 100);
            } else if (current > 0) {
                percent = 100;
            }

            totalCurrent += current;
            totalBase += last;

            const card = document.createElement('div');
            card.className = 'comparison-card';

            const isIncrease = diff > 0;
            const diffClass = diff === 0 ? 'diff-neutral' : (isIncrease ? 'diff-up' : 'diff-down');
            const diffIcon = diff === 0 ? '•' : (isIncrease ? '↑' : '↓');

            card.innerHTML = `
                <div class="comp-header">
                    <div class="comp-title">
                        <span>${cat.icon}</span>
                        ${cat.name}
                    </div>
                    <div class="diff-indicator ${diffClass}">
                        ${diffIcon} ${Math.abs(percent)}%
                    </div>
                </div>
                <div class="comp-values">
                    <div class="val-box">
                        <div class="val-label">${targetLabel}</div>
                        <div class="val-amount">¥${current.toLocaleString()}</div>
                    </div>
                    <div class="val-box">
                        <div class="val-label">${baseLabel}</div>
                        <div class="val-amount">¥${last.toLocaleString()}</div>
                    </div>
                </div>
                <div style="font-size: 0.875rem; text-align: right; color: ${isIncrease ? 'var(--accent-color)' : 'var(--primary-color)'}">
                    ${isIncrease ? '+' : ''}${diff.toLocaleString()} 円
                </div>
            `;
            compGridEl.appendChild(card);
        });

        // Overall Summary
        const totalDiff = totalCurrent - totalBase;
        let totalPercent = 0;
        if (totalBase > 0) {
            totalPercent = Math.round((totalDiff / totalBase) * 100);
        } else if (totalCurrent > 0) {
            totalPercent = 100;
        }

        totalDiffPercent.textContent = `${Math.abs(totalPercent)}%`;
        totalDiffLabel.textContent = totalDiff >= 0 ? '増加' : '減少';
        totalDiffAmount.textContent = `${baseLabel}より ${totalDiff >= 0 ? '+' : ''}${totalDiff.toLocaleString()} 円`;

        if (totalDiff > 0) {
            totalCompCard.style.setProperty('--card-accent', 'var(--accent-color)');
            totalDiffPercent.style.color = 'var(--accent-color)';
        } else {
            totalCompCard.style.setProperty('--card-accent', 'var(--primary-color)');
            totalDiffPercent.style.color = 'var(--primary-color)';
        }
    };

    targetMonthSelect.addEventListener('change', renderComparison);
    baseMonthSelect.addEventListener('change', renderComparison);

    fetchData();
});
