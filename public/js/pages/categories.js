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
                <div style="display: flex; gap: 0.5rem;">
                    <button class="nav-item edit-btn" data-id="${cat.id}" style="color: var(--primary-color); padding: 0.5rem; background: transparent; border: none; cursor: pointer;">
                        ✏️ 編集
                    </button>
                    <button class="nav-item delete-btn" data-id="${cat.id}" style="color: var(--accent-color); padding: 0.5rem; background: transparent; border: none; cursor: pointer;">
                        🗑️ 削除
                    </button>
                </div>
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

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.closest('.edit-btn').dataset.id;
                openEditModal(id);
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

    const emojisList = ['🎨', '🍎', '🍔', '🛒', '🛍️', '👗', '🚃', '🚗', '✈️', '🎮', '📱', '🎥', '🏠', '💡', '💧', '💰', '💴', '💳', '🏥', '💊', '✂️', '🎁', '🐶', '🐱', '☕', '🍺', '📚', '💪', '👶', '💄'];

    const openEditModal = (id) => {
        const cat = categories.find(c => String(c.id) === String(id));
        if (!cat) return;

        document.getElementById('edit-cat-id').value = cat.id;
        document.getElementById('edit-cat-name').value = cat.name;
        document.getElementById('edit-cat-icon').value = cat.icon;
        document.getElementById('edit-selected-icon-display').innerText = cat.icon;

        const container = document.getElementById('edit-emoji-container');
        container.innerHTML = emojisList.map(e => `
            <div class="edit-emoji-chip" style="cursor: pointer; font-size: 1.5rem; padding: 0.5rem; border-radius: 8px; transition: background 0.2s; background: ${e === cat.icon ? 'var(--primary-color)' : 'transparent'};" onclick="selectEditEmoji('${e}', this)">${e}</div>
        `).join('');

        document.getElementById('edit-cat-modal').style.display = 'flex';
    };

    window.selectEditEmoji = (emoji, el) => {
        document.getElementById('edit-cat-icon').value = emoji;
        document.getElementById('edit-selected-icon-display').innerText = emoji;
        document.querySelectorAll('.edit-emoji-chip').forEach(c => c.style.background = 'transparent');
        el.style.background = 'var(--primary-color)';
    };

    window.closeEditModal = () => {
        document.getElementById('edit-cat-modal').style.display = 'none';
    };

    window.submitEditCategory = async () => {
        const id = document.getElementById('edit-cat-id').value;
        const name = document.getElementById('edit-cat-name').value;
        const icon = document.getElementById('edit-cat-icon').value;

        if (!name || !icon) return alert('カテゴリ名とアイコンを入力してください');

        try {
            const response = await fetch(`/api/categories/${id}`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify({ name, icon })
            });

            if (response.ok) {
                closeEditModal();
                await fetchCategories();
            } else {
                const data = await response.json();
                alert(data.message || '更新に失敗しました');
            }
        } catch (error) {
            console.error('Error updating category:', error);
            alert('更新中にエラーが発生しました');
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
