import { Controller } from '@hotwired/stimulus';

/*
 * This controller handles adding and removing items in a Symfony CollectionType form.
 *
 * Usage in Twig:
 * <div data-controller="form-collection"
 * data-form-collection-index-value="{{ form.sessionDates|length }}"
 * data-form-collection-prototype-value="{{ form_widget(form.sessionDates.vars.prototype)|e('html_attr') }}">
 *
 * <div data-form-collection-target="collectionContainer">
 * ... existing items ...
 * </div>
 *
 * <button type="button" data-action="form-collection#addCollectionElement">Add</button>
 * </div>
 */
export default class extends Controller {
    static targets = ["collectionContainer"];
    static values = {
        index: Number,
        prototype: String,
    }

    addCollectionElement(event) {
        const item = document.createElement('div');
        // Add classes for styling (matching your existing structure)
        item.classList.add('row', 'mb-3', 'session-date-item', 'border-bottom', 'pb-3');

        // Replace '__name__' in the prototype with the current index
        const prototype = this.prototypeValue.replace(/__name__/g, this.indexValue);

        // Inject the prototype HTML with a delete button
        item.innerHTML = `
            <div class="col-md-10">
                ${prototype}
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-danger btn-sm" data-action="click->form-collection#removeCollectionElement">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }

    removeCollectionElement(event) {
        // Find the closest parent row and remove it
        event.target.closest('.session-date-item').remove();
    }
}
