/**
 * Call booking: month → day → time selects with dependent options.
 */
function daysInMonth(year, monthIndex0) {
    return new Date(year, monthIndex0 + 1, 0).getDate();
}

function parseMonthValue(value) {
    if (!value || !/^\d{4}-\d{2}$/.test(value)) return null;
    const [y, m] = value.split('-').map(Number);
    return { year: y, monthIndex: m - 1 };
}

function formatTimeLabel(hour, minute) {
    const d = new Date(2000, 0, 1, hour, minute);
    return d.toLocaleString(undefined, {
        hour: 'numeric',
        minute: '2-digit',
    });
}

function buildTimeOptions(startHour, endHour, slotMinutes) {
    const options = [];
    const startMin = startHour * 60;
    const endMin = endHour * 60;
    for (let t = startMin; t + slotMinutes <= endMin; t += slotMinutes) {
        const h = Math.floor(t / 60);
        const m = t % 60;
        const value = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
        options.push({
            value,
            label: formatTimeLabel(h, m),
        });
    }
    return options;
}

function isWeekday(year, monthIndex, day) {
    const d = new Date(year, monthIndex, day);
    const w = d.getDay();
    return w !== 0 && w !== 6;
}

function addPlaceholder(select, text) {
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = text;
    opt.disabled = true;
    opt.selected = true;
    opt.hidden = true;
    select.appendChild(opt);
}

export function initCallBookingPicker(root) {
    const monthSelect = root.querySelector('[data-cbp-month]');
    const daySelect = root.querySelector('[data-cbp-day]');
    const timeSelect = root.querySelector('[data-cbp-time]');

    if (!monthSelect || !daySelect || !timeSelect) return;

    const monthsAhead = Math.max(1, parseInt(root.dataset.monthsAhead || '6', 10));
    const slotMinutes = Math.max(5, parseInt(root.dataset.slotMinutes || '30', 10));
    const startHour = parseInt(root.dataset.startHour || '9', 10);
    const endHour = parseInt(root.dataset.endHour || '17', 10);
    const weekdaysOnly = root.dataset.weekdaysOnly === 'true';

    const timeOptions = buildTimeOptions(startHour, endHour, slotMinutes);

    function populateMonths() {
        monthSelect.innerHTML = '';
        addPlaceholder(monthSelect, monthSelect.dataset.placeholderMonth || 'Month');

        const now = new Date();
        for (let i = 0; i < monthsAhead; i++) {
            const d = new Date(now.getFullYear(), now.getMonth() + i, 1);
            const y = d.getFullYear();
            const m = d.getMonth();
            const value = `${y}-${String(m + 1).padStart(2, '0')}`;
            const label = d.toLocaleString(undefined, { month: 'long' });
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;
            monthSelect.appendChild(opt);
        }
    }

    function populateDays() {
        daySelect.innerHTML = '';
        addPlaceholder(daySelect, daySelect.dataset.placeholderDay || 'Day');

        const parsed = parseMonthValue(monthSelect.value);
        if (!parsed) return;

        const { year, monthIndex } = parsed;
        const lastDay = daysInMonth(year, monthIndex);
        const now = new Date();
        let startDay = 1;
        if (year === now.getFullYear() && monthIndex === now.getMonth()) {
            startDay = Math.max(1, now.getDate());
        }

        for (let d = startDay; d <= lastDay; d++) {
            if (weekdaysOnly && !isWeekday(year, monthIndex, d)) continue;
            const opt = document.createElement('option');
            opt.value = String(d);
            opt.textContent = String(d);
            daySelect.appendChild(opt);
        }
    }

    function populateTimes() {
        timeSelect.innerHTML = '';
        addPlaceholder(timeSelect, timeSelect.dataset.placeholderTime || 'Time');

        for (const { value, label } of timeOptions) {
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;
            timeSelect.appendChild(opt);
        }
    }

    populateMonths();
    populateTimes();

    monthSelect.addEventListener('change', () => {
        populateDays();
        daySelect.dispatchEvent(new Event('change', { bubbles: true }));
    });

    daySelect.addEventListener('change', () => {
        // Reserved for future: narrow times by same-day rules
    });

    populateDays();
}

export function initCallBookingPickers() {
    document.querySelectorAll('[data-call-booking-picker]').forEach((root) => {
        initCallBookingPicker(root);
    });
}
