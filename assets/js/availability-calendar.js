document.addEventListener("DOMContentLoaded", function () {
    console.log(typeof FullCalendar);

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
            document.getElementById("slot_id").value = info.event.id;
            alert("Selected slot: " + info.event.title);
        }
    });

    calendar.render();
});
