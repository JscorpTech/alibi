import "./alert.js";


window.addEventListener("alpine:init", () => {
    Alpine.data("vars", () => ({
        image: {},
        model: {
            color:"none"
        },
        setImage(id) {
            this.image = {
                id: id
            }
            this.modal.open = true;
        },

        modal: {
            open: false,
            toggle() {
                this.modal.open = !this.modal.open;
            }
        }
    }))
})

