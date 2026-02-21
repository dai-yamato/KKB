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
@endsection

@push('scripts')
<script src="{{ asset('js/pages/categories.js') }}"></script>
@endpush
