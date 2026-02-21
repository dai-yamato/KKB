document.addEventListener('DOMContentLoaded', () => {
    const householdId = localStorage.getItem('kkb_householdId');
    const userId = localStorage.getItem('kkb_userId');
    const currentUserRole = localStorage.getItem('kkb_currentUser') || 'editor';

    if (!householdId) {
        window.location.href = '/login';
        return;
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Household-Id': householdId,
        'X-User-Id': userId
    };

    const listEl = document.getElementById('full-transaction-list');
    const monthFilter = document.getElementById('month-filter');
    const typeFilter = document.getElementById('type-filter');
    const totalEl = document.getElementById('list-total');

    // Set default month to current month
    const now = new Date();
    monthFilter.value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

    let transactions = [];

    const fetchTransactions = async () => {
        try {
            const res = await fetch('/api/transactions', { headers });
            transactions = await res.json();
            renderList();
        } catch (error) {
            console.error('Error fetching transactions:', error);
            listEl.innerHTML = '<p style="text-align: center; padding: 2rem;">エラーが発生しました。</p>';
        }
    };

    const renderList = () => {
        // Filter by month
        const [year, month] = monthFilter.value.split('-');
        let filtered = transactions.filter(t => {
            if (!year || !month) return true;
            return t.date.startsWith(`${year}-${month}`);
        });

        // Filter by type
        const type = typeFilter.value;
        if (type !== 'all') {
            filtered = filtered.filter(t => t.type === type);
        }

        // Sort by date descending, then ID descending
        filtered.sort((a, b) => {
            if (a.date !== b.date) return b.date.localeCompare(a.date);
            return b.id - a.id;
        });

        if (filtered.length === 0) {
            listEl.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">この期間の記録はありません。</p>';
            totalEl.textContent = KKB.formatMoney(0);
            return;
        }

        let total = 0;
        let html = '';
        let currentDate = '';

        filtered.forEach(t => {
            // Group by date
            if (t.date !== currentDate) {
                const dateObj = new Date(t.date);
                const dayStr = ['日', '月', '火', '水', '木', '金', '土'][dateObj.getDay()];
                const dateFmt = `${dateObj.getMonth() + 1}月${dateObj.getDate()}日 (${dayStr})`;

                html += `
                    <div style="padding: 1rem 1rem 0.5rem; border-bottom: 1px solid var(--border-color); background: var(--bg-color); font-weight: 700; font-size: 0.875rem; position: sticky; top: 0; z-index: 10;">
                        ${dateFmt}
                    </div>
                `;
                currentDate = t.date;
            }

            const isExpense = t.type === 'expense';
            if (isExpense) {
                total -= Number(t.amount);
            } else {
                total += Number(t.amount);
            }

            const cat = t.category || { icon: '📦', name: '不明' };
            const userName = t.user ? t.user.name : 'メンバー';
            const amountColor = isExpense ? 'var(--text-main)' : 'var(--primary-color)';
            const sign = isExpense ? '-' : '+';

            const roleNames = { admin: '👑 管理者', editor: '✍️ 入力者' };
            const userRole = t.user && t.user.role ? roleNames[t.user.role] || 'メンバー' : 'メンバー';

            // Allow delete if admin or within 24 hours for editors
            const createdAt = new Date(t.created_at);
            const _now = new Date();
            const diffHours = (_now - createdAt) / (1000 * 60 * 60);
            const canDelete = currentUserRole === 'admin' || diffHours <= 24;

            html += `
                <div class="transaction-item" style="border-radius: 0; border-left: none; border-right: none; border-top: none; margin: 0; padding: 1rem; cursor: pointer; display: flex; flex-direction: column;" onclick="this.querySelector('.memo-accordion').style.display = this.querySelector('.memo-accordion').style.display === 'none' ? 'block' : 'none'">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div class="item-info">
                            <div class="item-icon" style="background: ${isExpense ? 'var(--bg-color)' : 'rgba(16,185,129,0.1)'}">${cat.icon}</div>
                            <div class="item-details">
                                <h4>${cat.name}</h4>
                                <p style="font-size: 0.75rem; color: var(--text-muted);">
                                    ${userName} <span style="font-size: 0.65rem;">(${userRole})</span>
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <div class="item-amount" style="color: ${amountColor}; font-weight: 700;">
                                ${sign}${KKB.formatMoney(t.amount)}
                            </div>
                            ${canDelete ? `
                            <button class="delete-btn" data-id="${t.id}" style="padding: 0.5rem; margin-left: 0.5rem; background: transparent; border: none; color: var(--accent-color); cursor: pointer; font-size: 1rem;" title="削除" onclick="event.stopPropagation();">
                                🗑️
                            </button>
                            ` : `
                            <span style="padding: 0.5rem; margin-left: 0.5rem; color: var(--text-muted); font-size: 0.8rem;" title="登録から24時間以上経過したため削除できません">🔒</span>
                            `}
                        </div>
                    </div>
                    <div class="memo-accordion" style="display: none; padding: 0.75rem; background: rgba(0,0,0,0.1); border-radius: 8px; border: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-main); margin-top: 0.75rem; line-height: 1.5;">
                        <span style="color: var(--text-muted); display: block; margin-bottom: 0.25rem; font-size: 0.75rem; font-weight: 700;">📝 メモ</span>
                        ${t.note ? String(t.note).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\\n/g, '<br>') : '<span style="color: var(--text-muted);">メモはありません</span>'}
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
        totalEl.textContent = KKB.formatMoney(total);

        // Add delete listeners
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.currentTarget.dataset.id;
                if (!confirm('この記録を削除しますか？')) return;

                try {
                    const response = await fetch(`/api/transactions/${id}`, {
                        method: 'DELETE',
                        headers: headers
                    });
                    if (response.ok) {
                        await fetchTransactions();
                    } else {
                        alert('削除に失敗しました。');
                    }
                } catch (error) {
                    console.error('Error deleting transaction:', error);
                }
            });
        });
    };

    monthFilter.addEventListener('change', renderList);
    typeFilter.addEventListener('change', renderList);

    fetchTransactions();
});
