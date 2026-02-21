document.addEventListener('DOMContentLoaded', () => {
    const now = new Date();
    let transactions = [];
    let categories = [];

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

            generateCharts();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const generateCharts = () => {
        const categoryNames = {};
        const categoryIcons = {};
        const categoryGroups = {};
        categories.forEach(cat => {
            categoryNames[cat.id] = cat.name;
            categoryIcons[cat.id] = cat.icon;
            categoryGroups[cat.id] = cat.group;
        });

        const expenseData = transactions.filter(t => t.type === 'expense');
        const groupTotals = {
            fixed: expenseData.filter(t => {
                const catGroup = t.category ? t.category.group : (categoryGroups[t.category_id] || 'variable');
                return catGroup === 'fixed';
            }).reduce((s, t) => s + Number(t.amount), 0),
            variable: expenseData.filter(t => {
                const catGroup = t.category ? t.category.group : (categoryGroups[t.category_id] || 'variable');
                return catGroup === 'variable';
            }).reduce((s, t) => s + Number(t.amount), 0)
        };

        // Category Chart
        const ctxCategory = document.getElementById('category-chart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: ['固定費', '変動費'],
                datasets: [{
                    data: [groupTotals.fixed, groupTotals.variable],
                    backgroundColor: ['#38bdf8', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8' } }
                },
                cutout: '70%'
            }
        });

        // Time Series Chart by User
        const ctxBalance = document.getElementById('balance-chart').getContext('2d');
        const dates = [...new Set(transactions.map(t => t.date))].sort();

        // Dynamic users from transactions
        const usersInTx = {};
        transactions.forEach(t => {
            if (t.user) {
                usersInTx[t.user_id] = t.user.name;
            }
        });
        const userIdList = Object.keys(usersInTx);

        const userColorsList = ['#10b981', '#f43f5e', '#38bdf8', '#8b5cf6', '#f59e0b'];

        const datasets = userIdList.map((uid, idx) => ({
            label: usersInTx[uid],
            data: dates.map(d => transactions.filter(t => t.date === d && String(t.user_id) === String(uid) && t.type === 'expense').reduce((s, t) => s + Number(t.amount), 0)),
            borderColor: userColorsList[idx % userColorsList.length],
            backgroundColor: 'transparent',
            tension: 0.4
        }));

        new Chart(ctxBalance, {
            type: 'line',
            data: { labels: dates, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                    x: { ticks: { color: '#94a3b8' } }
                },
                plugins: { legend: { labels: { color: '#94a3b8' } } }
            }
        });

        // 6 Month Trend Chart
        const ctxTrend = document.getElementById('trend-6month-chart').getContext('2d');
        const last6Months = [];
        for (let i = 5; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            last6Months.push(d.toISOString().slice(0, 7));
        }

        const trendData = last6Months.map(m => {
            return transactions
                .filter(t => t.type === 'expense' && t.date.startsWith(m))
                .reduce((s, t) => s + Number(t.amount), 0);
        });

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: last6Months.map(m => {
                    const [y, mm] = m.split('-');
                    return `${parseInt(mm)}月`;
                }),
                datasets: [{
                    label: '月間支出',
                    data: trendData,
                    backgroundColor: '#10b981',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                    x: { ticks: { color: '#94a3b8' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Breakdown List
        const listEl = document.getElementById('category-details-list');
        listEl.innerHTML = '';
        const currentMonth = now.toISOString().slice(0, 7);

        // Category breakdown
        const categoryTotals = categories.map(cat => ({
            name: cat.name,
            icon: cat.icon,
            total: expenseData.filter(t => t.category_id === cat.id && t.date.startsWith(currentMonth)).reduce((s, t) => s + Number(t.amount), 0)
        })).filter(ct => ct.total > 0).sort((a, b) => b.total - a.total);

        categoryTotals.forEach(ct => {
            const item = document.createElement('div');
            item.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--bg-color); border-radius: 12px; margin-bottom: 0.5rem;';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">${ct.icon}</span>
                    <span style="font-weight: 600;">${ct.name}</span>
                </div>
                <div style="font-weight: 700;">¥${ct.total.toLocaleString()}</div>
            `;
            listEl.appendChild(item);
        });

        // User breakdown
        const userTotals = userIdList.map((uid, idx) => ({
            name: usersInTx[uid],
            total: expenseData.filter(t => String(t.user_id) === String(uid) && t.date.startsWith(currentMonth)).reduce((s, t) => s + Number(t.amount), 0),
            color: userColorsList[idx % userColorsList.length]
        }));

        const userDivider = document.createElement('h3');
        userDivider.textContent = 'ユーザー別支出';
        userDivider.style.margin = '2rem 0 1rem';
        listEl.appendChild(userDivider);

        userTotals.forEach(ut => {
            const item = document.createElement('div');
            item.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: var(--bg-color); border-radius: 12px; margin-bottom: 0.5rem;';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background: ${ut.color};"></div>
                    <span style="font-weight: 600;">${ut.name} の今月の支出合計</span>
                </div>
                <div style="font-weight: 700;">¥${ut.total.toLocaleString()}</div>
            `;
            listEl.appendChild(item);
        });
    };

    fetchData();
});
