
        function showPage(id, btn) {
            // Hide all pages
            document.querySelectorAll(".page").forEach(p => {
                p.classList.add("hidden");
                p.classList.remove("show");
            });

            // Remove active from sidebar
            document.querySelectorAll(".sidebar-btn").forEach(b => b.classList.remove("active"));

            // Show selected page
            const pg = document.getElementById(id);
            pg.classList.remove("hidden");
            setTimeout(() => pg.classList.add("show"), 20);

            // Set active button
            btn.classList.add("active");
        }
    