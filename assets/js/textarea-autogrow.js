function debounce(callback, delay) {
    var timer;
    return function () {
        var args = arguments;
        var context = this;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, delay);
    };
}

/* Textarea qui s'aggrandit tout seul */
class Autogrow extends HTMLTextAreaElement {

    constructor() {
        super();
        this.onFocus = this.onFocus.bind(this);
        this.autogrow = this.autogrow.bind(this);
        /** Utilisation de debounce pour limiter les resizes trop nombreux */
        this.onResize = debounce(this.onResize.bind(this), 300);
    }

    connectedCallback() {
        this.addEventListener('focus', this.onFocus);
    }

    disconnectedCallback() {
        /** On supprime l'évènement si le textarea est supprimé */
        window.removeEventListener('resize', this.onResize);
    }

    onFocus() {

        /** Modification CSS du textarea */
        this.style.overflow = 'hidden';
        //this.style.resize = 'none';
        this.style.boxSizing = 'border-box';

        /** Le textarea s'adapte */
        this.autogrow();

        /** On n'écoute le resize que lorqu'on a focus au moins une fois */
        window.addEventListener('resize', this.onResize);

        /** idem pour l'évènement input */
        this.addEventListener('input', this.autogrow);

        /** Empêche de relancer onFocus à chaque fois */
        this.removeEventListener('focus', this.onFocus);
    }

    onResize() {
        this.autogrow();
    }

    autogrow() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + "px";
    }
}

const textareaAutogrow = customElements.define('textarea-autogrow', Autogrow, {extends: 'textarea'});

export { textareaAutogrow }
