/**
 * KKB Premium - Global Settings Utility
 * Handles: dark/light mode, currency display, month start day
 * Load this script in <head> (before other scripts) to avoid FOUC on theme.
 */

const KKB = (() => {
    // ── Defaults ──────────────────────────────────────────────────────────
    const DEFAULTS = {
        theme: 'dark',   // 'dark' | 'light'
        monthStartDay: 1,        // 1–28
    };

    // ── Storage helpers ───────────────────────────────────────────────────
    const get = (key) => localStorage.getItem('kkb_setting_' + key) ?? DEFAULTS[key];
    const set = (key, value) => localStorage.setItem('kkb_setting_' + key, value);

    // ── Theme ─────────────────────────────────────────────────────────────
    const applyTheme = () => {
        const theme = get('theme');
        document.documentElement.classList.toggle('light-mode', theme === 'light');
    };

    const toggleTheme = () => {
        const next = get('theme') === 'dark' ? 'light' : 'dark';
        set('theme', next);
        applyTheme();
        return next;
    };

    // ── Currency (Fixed to JPY) ───────────────────────────────────────────
    const formatMoney = (amount) => {
        const num = Number(amount) || 0;
        return '¥' + num.toLocaleString('ja-JP');
    };

    const getCurrencySymbol = () => '¥';

    // ── Month period ──────────────────────────────────────────────────────
    /**
     * Returns { startDate, endDate } strings (YYYY-MM-DD) for the "current month"
     * based on the configured start day.
     */
    const getCurrentPeriod = () => {
        const startDay = parseInt(get('monthStartDay'), 10) || 1;
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth(); // 0-indexed

        let periodStart, periodEnd;

        if (now.getDate() >= startDay) {
            // e.g. start=25, today=28 → period: this month 25th → next month 24th
            periodStart = new Date(y, m, startDay);
            periodEnd = new Date(y, m + 1, startDay - 1);
        } else {
            // e.g. start=25, today=5 → period: last month 25th → this month 24th
            periodStart = new Date(y, m - 1, startDay);
            periodEnd = new Date(y, m, startDay - 1);
        }

        const fmt = d => d.toISOString().split('T')[0];
        return { startDate: fmt(periodStart), endDate: fmt(periodEnd) };
    };

    /**
     * Filter transactions to the current period.
     */
    const filterByCurrentPeriod = (transactions) => {
        const { startDate, endDate } = getCurrentPeriod();
        return transactions.filter(t => t.date >= startDate && t.date <= endDate);
    };

    // ── Init ──────────────────────────────────────────────────────────────
    // Apply theme immediately (before DOMContentLoaded to avoid flash)
    applyTheme();

    return {
        get, set,
        applyTheme, toggleTheme,
        formatMoney, getCurrencySymbol,
        getCurrentPeriod, filterByCurrentPeriod,
    };
})();
