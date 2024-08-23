document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".edit-button");
  const updateButtons = document.querySelectorAll(".update-button");

  editButtons.forEach((button, index) => {
    button.addEventListener("click", function () {
      const row = button.closest("tr");
      const attendanceStatus = row.querySelector(".attendance-status");

      if (!attendanceStatus.querySelector("select")) {
        const currentStatus = attendanceStatus.textContent.trim();
        attendanceStatus.innerHTML = `
          <select class="status-dropdown">
            <option value="Present" ${
              currentStatus === "Present" ? "selected" : ""
            }>Present</option>
            <option value="Absent" ${
              currentStatus === "Absent" ? "selected" : ""
            }>Absent</option>`;
        updateButtons[index].disabled = true;
      }
    });
  });

  updateButtons.forEach((button, index) => {
    button.addEventListener("click", function () {
      const row = button.closest("tr");
      const attendanceStatus = row.querySelector(".attendance-status");
      const selectField = attendanceStatus.querySelector("select");

      if (selectField) {
        attendanceStatus.textContent = selectField.value;
        button.disabled = true;
        alert("The Status has been Updated!");
      }
    });
  });

  document.addEventListener("change", function (event) {
    if (
      event.target.tagName === "SELECT" &&
      event.target.closest(".attendance-status")
    ) {
      const row = event.target.closest("tr");
      const updateButton = row.querySelector(".update-button");
      updateButton.disabled = false;
    }
  });
});