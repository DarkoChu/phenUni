const menuToggle = document.getElementById("menuToggle");
const siteNav = document.getElementById("siteNav");

if (menuToggle && siteNav) {
    menuToggle.addEventListener("click", () => {
        siteNav.classList.toggle("open");
    });
}

const liveSearch = document.getElementById("liveSearch");
const liveRole = document.getElementById("liveRole");
const cards = document.querySelectorAll(".person-card");

function runClientFilter() {
    if (!cards.length || !liveSearch || !liveRole) {
        return;
    }

    const query = liveSearch.value.trim().toLowerCase();
    const role = liveRole.value.trim().toLowerCase();

    cards.forEach((card) => {
        const name = card.dataset.name || "";
        const email = card.dataset.email || "";
        const phone = card.dataset.phone || "";
        const cardRole = (card.dataset.role || "").toLowerCase();

        const queryMatch = query === "" || name.includes(query) || email.includes(query) || phone.includes(query);
        const roleMatch = role === "" || cardRole === role;

        card.style.display = queryMatch && roleMatch ? "block" : "none";
    });
}

if (liveSearch && liveRole && cards.length) {
    liveSearch.addEventListener("input", runClientFilter);
    liveRole.addEventListener("change", runClientFilter);
}

