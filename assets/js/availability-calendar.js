document.addEventListener("DOMContentLoaded", function () {

    if (typeof FullCalendar === "undefined") {
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
        eventContent: function (arg) {
            // Create custom event content
            let eventTitle = document.createElement("div");
            eventTitle.classList.add("fc-event-title");
            eventTitle.innerText = arg.event.title; // Show only title (time range)

            return { domNodes: [eventTitle] };
        },
        eventClick: function (info) {
            if (info.event.extendedProps.reserved) {
                // alert("This slot is already reserved. Please choose another one.");
                return; // Prevent selection of reserved slot
            }
            document.getElementById("slot_id").value = info.event.id;
            // Remove 'selected' class from all events
            document.querySelectorAll('.fc-event').forEach(event => {
                event.classList.remove('selected');
            });
            info.el.classList.remove('available');
            info.el.classList.add('selected');

        }
    });

    calendar.render();
});
