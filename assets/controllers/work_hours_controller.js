// assets/controllers/work_hours_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input", "row"];

    connect() {
        this.loadFromInput();
    }

    // 1. Parse JSON from hidden input and fill the grid
    loadFromInput() {
        try {
            const data = JSON.parse(this.inputTarget.value || '{}');

            this.rowTargets.forEach(row => {
                const day = row.dataset.day;
                if (data[day]) {
                    this.setInputValue(row, 'am_start', data[day].am_start);
                    this.setInputValue(row, 'am_end', data[day].am_end);
                    this.setInputValue(row, 'pm_start', data[day].pm_start);
                    this.setInputValue(row, 'pm_end', data[day].pm_end);
                }
            });
        } catch (e) {
            console.error("Error parsing work hours JSON", e);
        }
    }

    // 2. Read grid inputs and update the hidden JSON input
    update() {
        const schedule = {};

        this.rowTargets.forEach(row => {
            const day = row.dataset.day;
            const amS = this.getInputValue(row, 'am_start');
            const amE = this.getInputValue(row, 'am_end');
            const pmS = this.getInputValue(row, 'pm_start');
            const pmE = this.getInputValue(row, 'pm_end');

            // Only save if at least one field is filled for that day
            if (amS || amE || pmS || pmE) {
                schedule[day] = {
                    am_start: amS,
                    am_end: amE,
                    pm_start: pmS,
                    pm_end: pmE
                };
            }
        });

        this.inputTarget.value = JSON.stringify(schedule);
    }

    // --- Helpers ---

    setInputValue(row, name, value) {
        const input = row.querySelector(`input[name="${name}"]`);
        if (input) input.value = value || '';
    }

    getInputValue(row, name) {
        const input = row.querySelector(`input[name="${name}"]`);
        return input ? input.value : '';
    }
}
