document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("menuToggle");
    const nav = document.getElementById("navMenu");

    if (toggle && nav) {
        toggle.addEventListener("click", function () {
            nav.classList.toggle("active");
        });
    }

    const searchInput = document.getElementById("courseSearch");
    const courseCards = document.querySelectorAll(".course-card");

    if (searchInput && courseCards.length > 0) {
        searchInput.addEventListener("keyup", function () {
            const value = this.value.toLowerCase();

            courseCards.forEach(card => {
                const title = card.getAttribute("data-title") || "";
                const instructor = card.getAttribute("data-instructor") || "";
                const category = card.getAttribute("data-category") || "";

                if (
                    title.toLowerCase().includes(value) ||
                    instructor.toLowerCase().includes(value) ||
                    category.toLowerCase().includes(value)
                ) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    }
});