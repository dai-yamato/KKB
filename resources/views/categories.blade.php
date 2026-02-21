@extends('layouts.app')

@section('title', 'KKB Premium | カテゴリ管理')
@section('header_title', 'カテゴリ管理 🏷️')
@section('header_subtitle', '収支の分類をカスタマイズしましょう')

@section('content')
<div class="input-section">
    <!-- Add Category Form -->
    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">新規カテゴリ追加</h2>
        <form id="category-form">
            <div class="form-group">
                <label for="cat-name">カテゴリ名</label>
                <input type="text" id="cat-name" placeholder="例: 趣味、美容" required>
            </div>
            <div class="form-group">
                <label for="cat-type">収支タイプ</label>
                <select id="cat-type">
                    <option value="expense">支出 💸</option>
                    <option value="income">収入 💰</option>
                </select>
            </div>
            <div class="form-group" id="group-container">
                <label for="cat-group">支出分類 (支出の場合のみ)</label>
                <select id="cat-group">
                    <option value="variable">変動費 🔄</option>
                    <option value="fixed">固定費 📌</option>
                    <option value="none">分類なし</option>
                </select>
            </div>
            <div class="form-group">
                <label>アイコン (絵文字)</label>
                <input type="hidden" id="cat-icon" value="🎨" required>
                <div style="font-size: 2rem; margin-bottom: 0.5rem; text-align: center; background: var(--surface-color); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);" id="selected-icon-display">
                    🎨
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; max-height: 150px; overflow-y: auto; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 12px; background: var(--bg-color);">
                    <!-- Commonly used emojis -->
                    <script>
                        const emojis = ['🎨', '🍎', '🍔', '🛒', '🛍️', '👗', '🚃', '🚗', '✈️', '🎮', '📱', '🎥', '🏠', '💡', '💧', '💰', '💴', '💳', '🏥', '💊', '✂️', '🎁', '🐶', '🐱', '☕', '🍺', '📚', '💪', '👶', '💄'];
                        emojis.forEach(e => {
                            document.write(`<div class="emoji-chip" style="cursor: pointer; font-size: 1.5rem; padding: 0.5rem; border-radius: 8px; transition: background 0.2s;" onclick="selectEmoji('${e}', this)">${e}</div>`);
                        });
                        
                        function selectEmoji(emoji, el) {
                            document.getElementById('cat-icon').value = emoji;
                            document.getElementById('selected-icon-display').innerText = emoji;
                            document.querySelectorAll('.emoji-chip').forEach(c => c.style.background = 'transparent');
                            el.style.background = 'var(--primary-color)';
                        }
                    </script>
                </div>
            </div>
            <button type="submit" class="btn-primary">カテゴリを追加</button>
        </form>
    </div>

    <!-- Category List -->
    <div class="form-card history-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">現在のカテゴリ</h2>
        <div class="transaction-list" id="category-list">
            <!-- Categories will be injected here -->
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-cat-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--surface-color); padding: 2rem; border-radius: 16px; width: 90%; max-width: 400px; border: 1px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--primary-color);">カテゴリの編集</h3>
        <input type="hidden" id="edit-cat-id">
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="edit-cat-name" style="display:block; margin-bottom:0.5rem; font-size:0.875rem; color:var(--text-muted);">カテゴリ名</label>
            <input type="text" id="edit-cat-name" required style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main); font-family:inherit; box-sizing:border-box;">
        </div>
        
        <div class="form-group">
            <label style="display:block; margin-bottom:0.5rem; font-size:0.875rem; color:var(--text-muted);">アイコン (絵文字)</label>
            <input type="hidden" id="edit-cat-icon">
            <div style="font-size: 2rem; margin-bottom: 0.5rem; text-align: center; background: var(--bg-color); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);" id="edit-selected-icon-display"></div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; max-height: 150px; overflow-y: auto; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-color);" id="edit-emoji-container">
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="button" style="flex: 1; padding:0.75rem; border-radius:8px; background:var(--primary-color); color:white; border:none; cursor:pointer; font-weight:700;" onclick="submitEditCategory()">保存する</button>
            <button type="button" style="flex: 1; padding:0.75rem; border-radius:8px; background:transparent; color:var(--text-muted); border:1px solid var(--border-color); cursor:pointer;" onclick="closeEditModal()">キャンセル</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/categories.js') }}?v={{ filemtime(public_path('js/pages/categories.js')) }}"></script>
@endpush
