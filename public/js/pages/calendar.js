document.addEventListener('DOMContentLoaded', () => {
    const calendarGrid = document.querySelector('.calendar-grid');
    const currentMonthEl = document.getElementById('current-month');
    let transactions = [];
    let categories = [];
    let categoryIcons = {};

    let currentDate = new Date(); // Use actual current date

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
            categories.forEach(cat => categoryIcons[cat.id] = cat.icon);

            const txRes = await fetch('/api/transactions', { headers });
            transactions = await txRes.json();

            renderCalendar();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    const renderCalendar = () => {
        // Clear previous days (keep labels)
        const labels = Array.from(calendarGrid.querySelectorAll('.calendar-day-label'));
        calendarGrid.innerHTML = '';
        labels.forEach(l => calendarGrid.appendChild(l));

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        currentMonthEl.textContent = `${year}年 ${month + 1}月`;

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Empty slots
        for (let i = 0; i < firstDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-day';
            empty.style.opacity = '0.3';
            calendarGrid.appendChild(empty);
        }

        // Days
        const now = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';

            if (year === now.getFullYear() && month === now.getMonth() && day === now.getDate()) {
                dayEl.classList.add('today');
            }

            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayTransactions = transactions.filter(t => t.date === dateStr);

            dayEl.innerHTML = `
                <span class="day-number">${day}</span>
                <div class="day-events">
                    ${dayTransactions.map(t => {
                const icon = t.category ? t.category.icon : (categoryIcons[t.category_id] || '📦');
                return `
                            <div class="event-tag ${t.type === 'expense' ? 'tag-expense' : 'tag-income'}">
                                ${icon} ¥${Number(t.amount).toLocaleString()}
                            </div>
                        `;
            }).join('')}
                </div>
            `;
            calendarGrid.appendChild(dayEl);
        }
    };

    document.getElementById('prev-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('next-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    fetchData();
});
