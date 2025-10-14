(() => {
  let unsaved = false;
  let cancelling = false;

  function log(...args) {
    if (window.DEBUG_GUARD) console.log("[UNSAVED-GUARD]", ...args);
  }

  function armHistory(label = "arm") {
    try {
      history.replaceState({ guard: true, label }, "", location.href);
      history.pushState({ guard: true, label }, "", location.href);
      log("history armed:", label);
    } catch (err) {
      console.warn("history API failed:", err);
    }
  }

  // --- Public API ---
  window.enableUnsavedGuard = function () {
    if (unsaved) return;
    unsaved = true;
    $("#unsaved_warning_banner").slideDown(150);
    armHistory("enable");
    window.addEventListener("beforeunload", beforeUnloadHandler);
    log("unsaved ON");
  };

  window.disableUnsavedGuard = function () {
    if (!unsaved) return;
    unsaved = false;
    $("#unsaved_warning_banner").slideUp(150);
    window.removeEventListener("beforeunload", beforeUnloadHandler);
    log("unsaved OFF");
  };

  function beforeUnloadHandler(e) {
    if (!unsaved || cancelling) return;
    e.preventDefault();
    e.returnValue = "You have unsaved changes. Leaving will discard them.";
    return e.returnValue;
  }

  // --- Back button / navigation trap ---
  function blockNavigation(e) {
    if (!unsaved || cancelling) return;
    history.pushState({ guard: true, rebound: true }, "", location.href);

    if (typeof Swal === "undefined") {
      alert("Unsaved changes! Use the Cancel button to leave.");
      return;
    }

    Swal.fire({
      icon: "warning",
      title: "Unsaved changes detected!",
      html: `
        <div>You have unsaved work on this page.</div>
        <div class="mt-2 text-danger fw-bold">
          You cannot leave directly. Please use the <b>Cancel</b> button to discard changes.
        </div>
      `,
      showCancelButton: false,
      confirmButtonText: "OK",
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then(() => {
      armHistory("post-stay");
    });
  }

  // --- Refresh key trap ---
  function blockRefresh(e) {
    if (!unsaved) return;
    // F5, Ctrl+R, Cmd+R
    if (
      e.key === "F5" ||
      ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "r")
    ) {
      e.preventDefault();
      Swal.fire({
        icon: "warning",
        title: "Refresh blocked!",
        text: "You have unsaved changes. Please click Cancel or Save first.",
        confirmButtonText: "OK",
        allowOutsideClick: false,
        allowEscapeKey: false
      });
    }
  }

  // --- Click trap for all links ---
  function onOutboundClick(e) {
    if (!unsaved || cancelling) return;
    const href = e.currentTarget.getAttribute("href");
    const target = e.currentTarget.getAttribute("target");
    if (!href || href.startsWith("#") || (target && target !== "_self")) return;

    e.preventDefault();
    Swal.fire({
      icon: "warning",
      title: "Unsaved changes detected!",
      html: `
        <div>You cannot navigate away directly.</div>
        <div class="mt-2">Please click <b>Cancel</b> to discard changes safely.</div>
      `,
      confirmButtonText: "OK",
      allowOutsideClick: false,
      allowEscapeKey: false
    });
  }

  // --- Simulate cancel logic ---
  function triggerCancel() {
    cancelling = true;
    const $cancel = $(".btn-secondary, .btn-cancel");
    if ($cancel.length) {
      $cancel.first().trigger("click");
    } else {
      const listLink = sessionStorage.getItem("list_link");
      if (listLink) window.location.href = listLink;
    }
  }
  
  // 🔹 Global Cancel / Exit button handler
    $(document).on("click", ".btn-secondary, .btn-cancel, .btn-soft-danger", function (e) {
      // Mark cancellation globally so popstate & reload handlers ignore it
      cancelling = true;
      disableUnsavedGuard();
      log("Global cancel triggered — unsaved guard disabled");
    
      // Optionally, if you want to prevent double prompts during navigation:
      window.removeEventListener("popstate", blockNavigation);
    
      // If this button has a data-href or href, allow normal navigation
      const href = $(this).attr("href") || $(this).data("href");
      if (href) {
        e.preventDefault();
        setTimeout(() => { window.location.href = href; }, 50);
      }
    });


  // --- Initialization ---
// --- Initialization ---
$(function () {
  // --- On page load, check if user reloaded while unsaved ---
  if (sessionStorage.getItem("unsaved_reload_attempt") === "1") {
    sessionStorage.removeItem("unsaved_reload_attempt");
    Swal.fire({
      icon: "info",
      title: "You reloaded this page.",
      html: `
        <div>Any unsaved data may have been lost.</div>
        <div class="mt-2">Please review and re-enter your changes if necessary.</div>
      `,
      confirmButtonText: "OK"
    });
  }

  window.DEBUG_GUARD = false;
  setTimeout(() => armHistory("domready"), 0);
  window.addEventListener("popstate", blockNavigation, { passive: true });
  window.addEventListener("keydown", blockRefresh, true);
  $(document).on("click", "a[href]", onOutboundClick);

  // Auto-enable guard on any input/textarea/select change
  $(document).on("input change", "input:not([type=hidden]), textarea, select", function () {
    if (!$(this).prop("readonly") && !$(this).prop("disabled")) {
      enableUnsavedGuard();
    }
  });

  // Disable guard on Save
  $(document).on("submit", "form", disableUnsavedGuard);
  $(document).on("click", ".btn-success, .btn-save", disableUnsavedGuard);

  // If the Cancel button is clicked, allow leaving
  $(document).on("click", ".btn-secondary, .btn-cancel", function () {
    cancelling = true;
    disableUnsavedGuard();
  });
});
})();
