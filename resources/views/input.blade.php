@extends('layouts.app')

@section('title', 'KKB Premium | 連続入力')
@section('header_title', '連続入力 ✍️')
@section('header_subtitle', 'サクサク記録しましょう')

@push('styles')
<style>
    .quick-input-container {
        max-width: 520px;
        margin: 0 auto;
    }

    /* Type switch (income / expense) */
    .type-switch {
        display: flex;
        background: var(--bg-color);
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 1.5rem;
        gap: 4px;
    }

    .type-btn {
        flex: 1;
        padding: 0.65rem;
        border: none;
        border-radius: 10px;
        background: transparent;
        color: var(--text-muted);
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .type-btn.active-expense {
        background: rgba(244, 63, 94, 0.15);
        color: var(--accent-color);
    }

    .type-btn.active-income {
        background: rgba(16, 185, 129, 0.15);
        color: var(--primary-color);
    }

    .amount-display {
        font-size: 2.5rem;
        font-weight: 800;
        text-align: center;
        padding: 1rem;
        background: var(--bg-color);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        min-height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 2px;
    }

    .amount-display.expense { color: var(--accent-color); }
    .amount-display.income  { color: var(--primary-color); }

    .category-grid-select {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 1.5rem;
    }

    .cat-chip {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 10px 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
    }

    .cat-chip:hover { border-color: var(--primary-color); }

    .cat-chip.active {
        background: rgba(16, 185, 129, 0.1);
        border-color: var(--primary-color);
        transform: scale(1.04);
    }

    .cat-chip span  { font-size: 1.4rem; }
    .cat-chip label { font-size: 0.65rem; color: var(--text-muted); cursor: pointer; }

    .note-input {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.95rem;
        margin-bottom: 1.25rem;
        box-sizing: border-box;
    }

    .note-input:focus { outline: none; border-color: var(--primary-color); }

    .numeric-pad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .num-btn {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        padding: 1.25rem;
        border-radius: 16px;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s, transform 0.1s;
    }

    .num-btn:active {
        background: var(--surface-hover);
        transform: scale(0.96);
    }

    .save-btn {
        background: var(--primary-gradient);
        border: none;
        color: white;
        font-size: 1.1rem;
    }

    .save-btn.expense-mode {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
    }

    .backspace-btn {
        font-size: 1rem;
    }

    .success-toast {
        position: fixed;
        top: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(-120px);
        background: var(--primary-color);
        color: white;
        padding: 14px 28px;
        border-radius: 30px;
        font-weight: 700;
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 2000;
        white-space: nowrap;
    }

    .success-toast.show { transform: translateX(-50%) translateY(0); }

    /* Mobile specific optimizations for input screen */
    @media (max-width: 768px) {
        /* Hide global header to save massive vertical space */
        header {
            display: none !important;
        }

        .main-content {
            padding: 0.5rem !important;
            padding-bottom: 80px !important;
        }

        .form-card {
            padding: 1rem !important;
            border-radius: 16px;
        }

        .amount-display {
            font-size: 2rem;
            min-height: 60px;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .type-switch {
            margin-bottom: 1rem;
        }

        .category-grid-select {
            margin-bottom: 1rem;
            gap: 6px;
        }

        .cat-chip {
            padding: 8px 4px;
        }
        .cat-chip span {
            font-size: 1.2rem;
        }
        .cat-chip label {
            font-size: 0.55rem;
            letter-spacing: -0.5px;
        }
        
        .num-btn {
            padding: 1rem;
            font-size: 1.1rem;
        }

        .note-input {
            margin-bottom: 0.75rem !important;
            padding: 0.6rem 0.8rem;
        }

        .num-btn {
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 12px;
        }

        .numeric-pad {
            gap: 6px;
        }
    }
</style>
@endpush

@section('content')
<div id="toast" class="success-toast">保存しました！ ✨</div>

<div class="quick-input-container">
    <div class="form-card">

        <!-- Type Switch -->
        <div class="type-switch">
            <button class="type-btn active-expense" id="btn-expense" onclick="setType('expense')">💸 支出</button>
            <button class="type-btn" id="btn-income"  onclick="setType('income')">💰 収入</button>
        </div>

        <!-- Amount Display -->
        <div class="amount-display expense" id="amount-display">¥ 0</div>

        <!-- Categories -->
        <label style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem; display: block;">カテゴリー</label>
        <div class="category-grid-select" id="quick-cat-grid">
            <div style="text-align:center; padding: 1rem; color: var(--text-muted); grid-column: span 4;">読み込み中...</div>
        </div>

        <!-- Note -->
        <input type="text" id="quick-note" class="note-input" placeholder="💬 メモ（任意）：ランチ、コンビニなど">

        <!-- Date override -->
        <input type="date" id="quick-date" class="note-input" style="margin-bottom: 1.25rem;">

        <!-- Numpad -->
        <div class="numeric-pad">
            <button class="num-btn" onclick="addNum('7')">7</button>
            <button class="num-btn" onclick="addNum('8')">8</button>
            <button class="num-btn" onclick="addNum('9')">9</button>
            <button class="num-btn" onclick="addNum('4')">4</button>
            <button class="num-btn" onclick="addNum('5')">5</button>
            <button class="num-btn" onclick="addNum('6')">6</button>
            <button class="num-btn" onclick="addNum('1')">1</button>
            <button class="num-btn" onclick="addNum('2')">2</button>
            <button class="num-btn" onclick="addNum('3')">3</button>
            <button class="num-btn backspace-btn" onclick="backspace()">⌫</button>
            <button class="num-btn" onclick="addNum('0')">0</button>
            <button class="num-btn save-btn expense-mode" id="save-btn" onclick="saveTransaction()">✓ 保存</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const householdId = localStorage.getItem('kkb_householdId');
        const dbUserId    = localStorage.getItem('kkb_userId');

        if (!householdId) {
            window.location.href = '/login';
            return;
        }

        const headers = {
            'Content-Type':  'application/json',
            'X-Household-Id': householdId,
            'Accept':        'application/json',
        };

        // --- State ---
        let categories    = [];
        let selectedCatId = null;
        let currentType   = 'expense';
        let amountStr     = '';

        // --- Type switch ---
        window.setType = (type) => {
            currentType = type;
            const display  = document.getElementById('amount-display');
            const saveBtn  = document.getElementById('save-btn');
            const btnExp   = document.getElementById('btn-expense');
            const btnInc   = document.getElementById('btn-income');

            if (type === 'expense') {
                display.className  = 'amount-display expense';
                saveBtn.className  = 'num-btn save-btn expense-mode';
                btnExp.className   = 'type-btn active-expense';
                btnInc.className   = 'type-btn';
            } else {
                display.className  = 'amount-display income';
                saveBtn.className  = 'num-btn save-btn';
                btnExp.className   = 'type-btn';
                btnInc.className   = 'type-btn active-income';
            }
            renderChips();
        };

        // --- Numpad ---
        window.addNum = (n) => {
            if (amountStr.length >= 9) return; // max 9 digits
            if (amountStr === '0') amountStr = n;
            else amountStr += n;
            updateDisplay();
        };

        window.backspace = () => {
            amountStr = amountStr.slice(0, -1);
            updateDisplay();
        };

        function updateDisplay() {
            const num = parseInt(amountStr || '0', 10);
            document.getElementById('amount-display').textContent = '¥ ' + num.toLocaleString('ja-JP');
        }

        // --- Categories ---
        async function fetchCategories() {
            try {
                const res = await fetch('/api/categories', { headers });
                if (!res.ok) throw new Error('Failed');
                categories = await res.json();
                renderChips();
            } catch (e) {
                document.getElementById('quick-cat-grid').innerHTML =
                    '<div style="color:var(--accent-color);grid-column:span 4;text-align:center;">カテゴリ取得に失敗しました</div>';
            }
        }

        function renderChips() {
            const grid = document.getElementById('quick-cat-grid');
            const filteredCats = categories.filter(cat => cat.type === currentType);

            if (!filteredCats.length) {
                grid.innerHTML = '<div style="color:var(--text-muted);grid-column:span 4;text-align:center;">カテゴリがありません</div>';
                selectedCatId = null;
                return;
            }
            grid.innerHTML = filteredCats.map(cat => `
                <div class="cat-chip" data-id="${cat.id}" id="chip-${cat.id}">
                    <span>${cat.icon}</span>
                    <label>${cat.name}</label>
                </div>
            `).join('');

            document.querySelectorAll('.cat-chip').forEach(chip => {
                chip.addEventListener('click', () => selectCat(chip.dataset.id));
            });

            if (!filteredCats.find(c => String(c.id) === String(selectedCatId))) {
                selectCat(filteredCats[0].id);
            } else {
                selectCat(selectedCatId);
            }
        }

        function selectCat(id) {
            if (selectedCatId) {
                const prev = document.getElementById(`chip-${selectedCatId}`);
                if (prev) prev.classList.remove('active');
            }
            selectedCatId = id;
            const current = document.getElementById(`chip-${id}`);
            if (current) current.classList.add('active');
        }

        // --- Save ---
        window.saveTransaction = async () => {
            const amount = parseInt(amountStr || '0', 10);
            if (!amount) {
                alert('金額を入力してください');
                return;
            }
            if (!selectedCatId) {
                alert('カテゴリーを選択してください');
                return;
            }

            const dateVal = document.getElementById('quick-date').value
                || new Date().toISOString().split('T')[0];

            const payload = {
                date:        dateVal,
                type:        currentType,
                user_id:     dbUserId,
                category_id: selectedCatId,
                amount:      amount,
                note:        document.getElementById('quick-note').value.trim(),
            };

            const btn = document.getElementById('save-btn');
            btn.disabled    = true;
            btn.textContent = '...';

            try {
                const res = await fetch('/api/transactions', {
                    method:  'POST',
                    headers,
                    body: JSON.stringify(payload),
                });

                if (res.ok) {
                    // Reset
                    amountStr = '';
                    updateDisplay();
                    document.getElementById('quick-note').value = '';

                    // Show toast
                    const toast = document.getElementById('toast');
                    toast.classList.add('show');
                    setTimeout(() => toast.classList.remove('show'), 2000);
                } else {
                    const err = await res.json();
                    alert('保存に失敗しました: ' + (err.message || JSON.stringify(err.errors)));
                }
            } catch (e) {
                alert('ネットワークエラーが発生しました');
            } finally {
                btn.disabled    = false;
                btn.textContent = '✓ 保存';
            }
        };

        // --- Init ---
        // Set today's date as default
        document.getElementById('quick-date').value = new Date().toISOString().split('T')[0];

        fetchCategories();
    });
</script>
@endpush
