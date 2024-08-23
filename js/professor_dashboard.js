document.addEventListener("DOMContentLoaded", () => {
  let isEditingAll = false;

  fetch("../php/prof_dashboard.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.prof_name !== "Not found") {
        document.getElementById("prof_name").textContent = data.prof_name;
        document.getElementById("class_no").textContent = data.class_no;
        document.getElementById("course_name").textContent = data.course_code;

        const attendanceTable = document
          .getElementById("attendance_table")
          .getElementsByTagName("tbody")[0];
        data.attendance.forEach((record) => {
          const row = attendanceTable.insertRow();
          row.setAttribute("data-student-no", record.student_no);

          row.insertCell(0).textContent = record.firstname;
          row.insertCell(1).textContent = record.lastname;

          const dateCell = row.insertCell(2);
          dateCell.textContent = record.date;
          dateCell.setAttribute("data-original-date", record.date);

          const statusCell = row.insertCell(3);
          const statusSelect = document.createElement("select");
          statusSelect.innerHTML = `
                      <option value="Present" ${
                        record.status === "Present" ? "selected" : ""
                      }>Present</option>
                      <option value="Absent" ${
                        record.status === "Absent" ? "selected" : ""
                      }>Absent</option>
                  `;
          statusSelect.style.display = "none";
          statusCell.appendChild(statusSelect);
          statusCell.insertAdjacentHTML(
            "beforeend",
            `<span>${record.status}</span>`
          );

          const actionCell = row.insertCell(4);
          const editButton = document.createElement("button");
          editButton.textContent = "Edit";
          editButton.classList.add("edit-btn");
          actionCell.appendChild(editButton);

          const saveButton = document.createElement("button");
          saveButton.textContent = "Save";
          saveButton.classList.add("save-btn");
          saveButton.style.display = "none";
          actionCell.appendChild(saveButton);

          editButton.addEventListener("click", () => {
            if (!isEditingAll && confirm("Do you want to edit this row?")) {
              toggleRowEditing(row, true);
              document.getElementById("save_all_button").disabled = false;
            }
          });

          saveButton.addEventListener("click", () => {
            if (confirm("Do you want to save changes to this row?")) {
              saveRowChanges(row);
            }
          });
        });

        document
          .getElementById("edit_all_button")
          .addEventListener("click", () => {
            if (!isEditingAll && confirm("Do you want to edit all rows?")) {
              isEditingAll = true;
              toggleAllEditing(true);
              document.getElementById("save_all_button").disabled = false;
            }
          });

        document
          .getElementById("save_all_button")
          .addEventListener("click", () => {
            if (isEditingAll && confirm("Do you want to save all changes?")) {
              saveAllChanges();
            }
          });

        // Add event listener for statistics button
        const statisticsButton = document.getElementById("statistics_button");
        const profIdElement = document.getElementById("prof_id");

        console.log("Statistics Button:", statisticsButton);
        console.log("Professor ID Element:", profIdElement);

        if (statisticsButton && profIdElement) {
          statisticsButton.addEventListener("click", () => {
            const profId = profIdElement.textContent.trim();
            if (profId) {
              window.location.href = `statistics_page.html?prof_id=${encodeURIComponent(
                profId
              )}`;
            } else {
              console.error("Professor ID is not set.");
            }
          });
        } else {
          console.error(
            "Statistics button or professor ID element is missing."
          );
        }

        const logoutButton = document.getElementById("logout");
        if (logoutButton) {
          logoutButton.addEventListener("click", function () {
            window.location.href = "../index.html";
          });
        }
      } else {
        console.error("Professor information not found.");
      }
    })
    .catch((error) => console.error("Error fetching data:", error));

  function toggleRowEditing(row, isEditing) {
    const dateCell = row.cells[2];
    const statusSelect = row.querySelector("select");
    const statusSpan = row.querySelector("span");
    const saveButton = row.querySelector(".save-btn");
    const editButton = row.querySelector(".edit-btn");

    if (isEditing) {
      const dateValue = dateCell.textContent;
      dateCell.innerHTML = `<input type="date" value="${dateValue}">`;
    } else {
      const dateInput = dateCell.querySelector("input");
      if (dateInput) {
        dateCell.textContent = dateInput.value;
      }
    }

    if (statusSelect) {
      statusSelect.style.display = isEditing ? "inline" : "none";
    }
    if (statusSpan) {
      statusSpan.style.display = isEditing ? "none" : "inline";
    }
    if (saveButton) {
      saveButton.style.display = isEditing ? "inline" : "none";
    }
    if (editButton) {
      editButton.style.display = isEditing ? "none" : "inline";
    }
  }

  function toggleAllEditing(isEditing) {
    document.querySelectorAll("#attendance_table tbody tr").forEach((row) => {
      toggleRowEditing(row, isEditing);
    });
  }

  function saveRowChanges(row) {
    const studentNo = row.getAttribute("data-student-no");
    const dateInput = row.cells[2].querySelector("input");
    const statusSelect = row.querySelector("select");
    const statusSpan = row.querySelector("span");

    if (!dateInput || !statusSelect || !statusSpan) {
      console.error(
        "Required elements not found for student number:",
        studentNo
      );
      return;
    }

    const newDate = dateInput.value;
    const newStatus = statusSelect.value;

    fetch("../php/prof_dashboard.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        student_no: studentNo,
        class_no: document.getElementById("class_no").textContent,
        status: newStatus,
        date: newDate,
      }),
    })
      .then((response) => response.text())
      .then((responseText) => {
        if (responseText.trim() === "success") {
          row.cells[2].textContent = newDate;
          statusSpan.textContent = newStatus;
          toggleRowEditing(row, false);
          checkIfAllSaved();
        } else {
          console.error(
            "Failed to update attendance for student number:",
            studentNo
          );
        }
      })
      .catch((error) =>
        console.error(
          "Error updating attendance for student number:",
          studentNo,
          error
        )
      );
  }

  function saveAllChanges() {
    const promises = Array.from(
      document.querySelectorAll("#attendance_table tbody tr")
    ).map((row) => {
      const studentNo = row.getAttribute("data-student-no");
      const dateInput = row.cells[2].querySelector("input");
      const statusSelect = row.querySelector("select");

      if (!dateInput || !statusSelect) {
        console.error(
          "Required elements not found for student number:",
          studentNo
        );
        return Promise.resolve();
      }

      const newDate = dateInput.value;
      const newStatus = statusSelect.value;

      return fetch("../php/prof_dashboard.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          student_no: studentNo,
          class_no: document.getElementById("class_no").textContent,
          status: newStatus,
          date: newDate,
        }),
      })
        .then((response) => response.text())
        .then((responseText) => {
          if (responseText.trim() === "success") {
            row.cells[2].textContent = newDate;
            row.querySelector("span").textContent = newStatus;
            toggleRowEditing(row, false);
          } else {
            console.error(
              "Failed to update attendance for student number:",
              studentNo
            );
          }
        })
        .catch((error) =>
          console.error(
            "Error updating attendance for student number:",
            studentNo,
            error
          )
        );
    });

    Promise.all(promises)
      .then(() => {
        isEditingAll = false;
        document.getElementById("save_all_button").disabled = true;
      })
      .catch((error) => console.error("Error saving all changes:", error));
  }

  function checkIfAllSaved() {
    const allRows = Array.from(
      document.querySelectorAll("#attendance_table tbody tr")
    );
    const anyRowEditing = allRows.some(
      (row) => row.querySelector(".save-btn").style.display === "inline"
    );

    if (!anyRowEditing) {
      document.getElementById("save_all_button").disabled = true;
    }
  }
});
