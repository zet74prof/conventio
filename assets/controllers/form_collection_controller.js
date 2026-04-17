import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container", "submit"];
    static values = {
        index: Number,
        prototype: String,
    }

    connect() {
        this.validate();
        // Add event listener for all inputs inside the container
        this.containerTarget.addEventListener('input', (event) => {
            if (event.target.type === 'date') {
                this.validate();
            }
        });
    }

    add(event) {
        const prototype = this.prototypeValue.replace(/__name__/g, this.indexValue);
        const item = document.createElement('div');
        item.classList.add('card', 'mb-2', 'bg-light', 'border-0', 'form-collection-item');
        
        item.innerHTML = `
            <div class="card-body p-2 d-flex gap-2 align-items-end">
                <div class="flex-grow-1">
                    ${prototype}
                </div>
                <button type="button" class="btn btn-outline-danger mb-3" data-action="form-collection#remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        this.containerTarget.appendChild(item);
        this.indexValue++;
        this.validate();
    }

    remove(event) {
        event.target.closest('.form-collection-item').remove();
        this.validate();
    }

    validate() {
        const items = Array.from(this.containerTarget.querySelectorAll('.form-collection-item'));
        let dates = [];
        let hasError = false;

        // Reset previous errors
        items.forEach(item => {
            const inputs = item.querySelectorAll('input[type="date"]');
            inputs.forEach(input => input.classList.remove('is-invalid'));
        });

        items.forEach((item, index) => {
            const startDateInput = item.querySelector('input[id$="_startDate"]');
            const endDateInput = item.querySelector('input[id$="_endDate"]');

            if (startDateInput && endDateInput) {
                const start = startDateInput.value;
                const end = endDateInput.value;

                if (start && end) {
                    if (start >= end) {
                        endDateInput.classList.add('is-invalid');
                        hasError = true;
                    }
                    dates.push({ start, end, index, startDateInput, endDateInput });
                }
            }
        });

        // Check for overlaps
        dates.sort((a, b) => a.start.localeCompare(b.start));

        for (let i = 1; i < dates.length; i++) {
            if (dates[i].start <= dates[i - 1].end) {
                dates[i].startDateInput.classList.add('is-invalid');
                hasError = true;
            }
        }

        if (this.hasSubmitTarget) {
            this.submitTarget.disabled = hasError;
        }
    }
}
