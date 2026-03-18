<div id="infoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl transform scale-95 opacity-0 transition-all duration-300" id="modalContent">

        <!-- Title -->
        <h2 class="text-lg font-semibold text-slate-900">
            Welcome 👋
        </h2>

        <!-- Content -->
        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
            This website helps you explore products easily.  
            You can add items to wishlist, check reviews, and order quickly.
        </p>

        <!-- Button -->
        <button id="closeModal"
            class="mt-5 w-full rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
            Got it
        </button>

    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("infoModal");
    const content = document.getElementById("modalContent");
    const closeBtn = document.getElementById("closeModal");

    // Check if already shown
    if (!localStorage.getItem("siteVisited")) {

        modal.classList.remove("hidden");
        modal.classList.add("flex");

        // Animation
        setTimeout(() => {
            content.classList.remove("opacity-0", "scale-95");
            content.classList.add("opacity-100", "scale-100");
        }, 100);

        // Save visit
        localStorage.setItem("siteVisited", "true");
    }

    // Close modal
    closeBtn.addEventListener("click", () => {
        content.classList.add("opacity-0", "scale-95");

        setTimeout(() => {
            modal.classList.add("hidden");
        }, 300);
    });

});
</script>