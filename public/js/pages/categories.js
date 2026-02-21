document.addEventListener('DOMContentLoaded', () => {
    const categoryForm = document.getElementById('category-form');
    const categoryList = document.getElementById('category-list');

    // Default Categories (not directly used but kept for reference)
    const typeNames = { expense: '支出', income: '収入' };

    const groupNames = { fixed: '固定費', variable: '変動費', none: '-' };

    let categories = [];
    const householdId = localStorage.getItem('kkb_householdId');
    const userId = localStorage.getItem('kkb_userId');
    const currentUserRole = localStorage.getItem('kkb_currentUser') || 'editor';
    if (!householdId) {
        window.location.href = '/login';
    }

    const headers = {
        'Content-Type': 'application/json',
        'X-Household-Id': householdId,
        'X-User-Id': userId
    };

    const fetchCategories = async () => {
        try {
            const response = await fetch('/api/categories', { headers });
            categories = await response.json();
            renderCategories();
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    };

    const renderCategories = () => {
        categoryList.innerHTML = '';
        categories.forEach(cat => {
            const div = document.createElement('div');
            div.className = 'transaction-item';
            div.innerHTML = `
                <div class="item-info">
                    <div class="item-icon">${cat.icon}</div>
                    <div class="item-details">
                        <h4 style="display:flex; align-items:center; gap:0.5rem;">
                            ${cat.name} 
                            <span style="font-size:0.6rem; padding:2px 6px; border-radius:8px; background: ${cat.type === 'income' ? 'rgba(16,185,129,0.1)' : 'rgba(244,63,94,0.1)'}; color: ${cat.type === 'income' ? 'var(--primary-color)' : 'var(--accent-color)'};">
                                ${typeNames[cat.type] || '支出'}
                            </span>
                        </h4>
                        <p style="font-size: 0.75rem; color: var(--text-muted);">
                            ${cat.type === 'income' ? '-' : (groupNames[cat.group] || '-')}
                        </p>
                    </div>
                </div>
                ${currentUserRole === 'admin' ? `
                <button class="nav-item delete-btn" data-id="${cat.id}" style="color: var(--accent-color); padding: 0.5rem; background: transparent; border: none; cursor: pointer;">
                    🗑️ 削除
                </button>
                ` : ''}
            `;
            categoryList.appendChild(div);
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.closest('.delete-btn').dataset.id;
                deleteCategory(id);
            });
        });
    };

    const deleteCategory = async (id) => {
        if (confirm('このカテゴリを削除しますか？')) {
            try {
                const response = await fetch(`/api/categories/${id}`, {
                    method: 'DELETE',
                    headers: headers
                });
                if (response.ok) {
                    await fetchCategories();
                }
            } catch (error) {
                console.error('Error deleting category:', error);
            }
        }
    };

    const catTypeEl = document.getElementById('cat-type');
    const groupContainer = document.getElementById('group-container');
    const catGroupEl = document.getElementById('cat-group');

    if (currentUserRole !== 'admin') {
        const formCard = document.querySelector('.form-card');
        if (formCard) formCard.style.display = 'none';

        // Hide entire form header if exists
        const header = document.querySelector('.input-section .form-card h3');
        if (header) header.style.display = 'none';
    }

    if (catTypeEl) {
        catTypeEl.addEventListener('change', (e) => {
            if (e.target.value === 'income') {
                groupContainer.style.display = 'none';
                catGroupEl.value = 'none';
            } else {
                groupContainer.style.display = 'block';
                catGroupEl.value = 'variable';
            }
        });
    }

    categoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('cat-name').value;
        const icon = document.getElementById('cat-icon').value;
        const group = document.getElementById('cat-group').value;
        const type = document.getElementById('cat-type') ? document.getElementById('cat-type').value : 'expense';

        try {
            const response = await fetch('/api/categories', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ name, icon, group, type })
            });
            if (response.ok) {
                categoryForm.reset();
                document.getElementById('cat-icon').value = '🎨';
                document.getElementById('selected-icon-display').innerText = '🎨';
                document.querySelectorAll('.emoji-chip').forEach(c => c.style.background = 'transparent');
                if (catTypeEl) catTypeEl.dispatchEvent(new Event('change'));
                await fetchCategories();
            }
        } catch (error) {
            console.error('Error saving category:', error);
        }
    });

    fetchCategories();
});
