window.addEventListener("alert", (event) => {
    let message = event.detail[0].message
    let type = event.detail[0].type ?? "success"

    let container = document.createElement("div");
    let icon = document.createElement("div");
    let span = document.createElement("span")
    let msg = document.createElement("p")
    let button = document.createElement("button")

    container.role = "alert"
    container.className = `!fixed bottom-0 right-0 z-[9999] alert alert-${type} border-0 d-flex align-items-center`;
    icon.className = "me-3 icon-item " + (type === "success" ? "bg-success" : "bg-danger");
    span.className = "fas text-white fs-6 " + (type === "success" ? "fa-check-circle" : "")
    msg.className = "mb-0 flex-1"
    msg.innerHTML = message
    button.className = "btn-close ml-5"
    button.type = "button"
    button.setAttribute("data-bs-dismiss", "alert")
    button.ariaLabel = "Close"

    icon.appendChild(span)
    container.append(icon, message, button);

    let el = document.querySelector("#alert-container")
    el.innerHTML = "";
    el.appendChild(container)
    setTimeout(() => {
        el.innerHTML = "";
    }, 5000);
})
