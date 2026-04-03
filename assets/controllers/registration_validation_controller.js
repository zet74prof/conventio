import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["email", "error", "submit"];
    static values = {
        studentDomain: String,
        profDomain: String
    };

    connect() {
        this.validate();
    }

    validate() {
        if (!this.hasEmailTarget) return;

        const email = this.emailTarget.value.trim();
        if (!email) {
            if (this.hasErrorTarget) this.errorTarget.style.display = 'none';
            if (this.hasSubmitTarget) this.submitTarget.disabled = true;
            return;
        }

        const isValid = email.endsWith(this.studentDomainValue) || email.endsWith(this.profDomainValue);

        if (isValid) {
            if (this.hasErrorTarget) this.errorTarget.style.display = 'none';
            this.emailTarget.classList.remove('is-invalid');
            this.emailTarget.classList.add('is-valid');
            if (this.hasSubmitTarget) this.submitTarget.disabled = false;
        } else {
            if (this.hasErrorTarget) this.errorTarget.style.display = 'block';
            this.emailTarget.classList.add('is-invalid');
            this.emailTarget.classList.remove('is-valid');
            if (this.hasSubmitTarget) this.submitTarget.disabled = true;
        }
    }
}
