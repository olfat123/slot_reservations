document.addEventListener("DOMContentLoaded", function () {

    if (typeof FullCalendar === "undefined") {
        console.error("FullCalendar.js is not loaded.");
        return;
    }

    let calendarEl = document.getElementById("calendar");

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        selectable: true,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek"
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            jQuery.ajax({
                url: calendarData.ajax_url,
                type: "POST",
                data: {
                    action: "fetch_available_slots",
                    nonce: calendarData.nonce
                },
                success: function (response) {
                    if (response.success) {
                        successCallback(response.data);
                    } else {
                        failureCallback();
                    }
                }
            });
        },
        eventClick: function (info) {
            if (info.event.extendedProps.reserved) {
                alert("This slot is already reserved. Please choose another one.");
                return; // Prevent selection of reserved slot
            }
            document.getElementById("slot_id").value = info.event.id;
            alert("Selected slot: " + info.event.title);
        }
    });

    calendar.render();

    document.getElementById("reservation-form").addEventListener("submit", function (event) {
        let isValid = true;
        let requiredFields = ["name", "email", "slot_id"];

        requiredFields.forEach(function (field) {
            let input = document.getElementById(field);
            if (!input || input.value.trim() === "") {
                isValid = false;
                input.style.border = "2px solid red"; // Highlight empty field
            } else {
                input.style.border = ""; // Reset border if valid
            }
        });

        if (!isValid) {
            event.preventDefault(); // Stop form submission
            alert("Please fill in all required fields.");
        }
    });
});
