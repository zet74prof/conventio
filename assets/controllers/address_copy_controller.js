// assets/controllers/address_copy_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        // Source Fields (HQ)
        "hqPlaceName", "hqAddress", "hqZip", "hqCity", "hqCountry",

        // Destination Fields (Internship)
        "jobPlaceName", "jobAddress", "jobZip", "jobCity", "jobCountry",

        // UI Elements
        "container", "checkbox"
    ];

    connect() {
        // Set initial state on page load
        this.toggle();
    }

    toggle() {
        if (this.checkboxTarget.checked) {
            this.containerTarget.classList.add('d-none');
            this.copy();
        } else {
            this.containerTarget.classList.remove('d-none');
        }
    }

    // Called when HQ fields change (to keep Internship fields in sync if hidden)
    sync() {
        if (this.checkboxTarget.checked) {
            this.copy();
        }
    }

    copy() {
        this.jobPlaceNameTarget.value = this.hqPlaceNameTarget.value;
        this.jobAddressTarget.value = this.hqAddressTarget.value;
        this.jobZipTarget.value = this.hqZipTarget.value;
        this.jobCityTarget.value = this.hqCityTarget.value;
        this.jobCountryTarget.value = this.hqCountryTarget.value;
    }
}
