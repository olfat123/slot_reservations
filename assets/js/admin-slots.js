jQuery(document).ready(function($) {
    function loadSlots() {
        $.post(ajaxurl, { action: 'get_slots' }, function(response) {
            if (response.success) {
                let slotsHtml = '';
                response.data.forEach(slot => {
                    slotsHtml += `
                        <tr>
                            <td>${slot.slot_time}</td>
                            <td>${slot.duration} minutes</td>
                            <td>
                                <select class="slot-status" data-id="${slot.id}">
                                    <option value="available" ${slot.status === 'available' ? 'selected' : ''}>Available</option>
                                    <option value="reserved" ${slot.status === 'reserved' ? 'selected' : ''}>Reserved</option>
                                </select>
                            </td>
                            <td>
                                <button class="remove-slot" data-id="${slot.id}">Remove</button>
                            </td>
                        </tr>`;
                });
                $('#slot-list').html(slotsHtml);
            }
        });
    }

    $(document).on('click', '.remove-slot', function() {
        if (confirm("Are you sure you want to remove this slot?")) {
            $.post(ajaxurl, {
                action: 'remove_slot',
                slot_id: $(this).data('id'),
                nonce: calendarData.nonce
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadSlots();
                }
            });
        }
    });

    $(document).on('change', '.slot-status', function() {
        const slotId = $(this).data('id');
        const newStatus = $(this).val();

        $.post(ajaxurl, {
            action: 'update_slot_status',
            slot_id: slotId,
            new_status: newStatus,
            nonce: calendarData.nonce
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
            }
        });
    });

    // Initialize Dialog Modal
    $("#slot-modal").dialog({
        autoOpen: false,
        modal: true,
        width: 400,
    });

    // Initialize jQuery UI DateTime Picker
    $("#slot-time").datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: "HH:mm",
        controlType: 'select',
        oneLine: true,
        stepMinute: 15
    });

    // Open Modal on Button Click
    $("#add-slot").click(function() {
        $("#slot-modal").dialog("open");
    });

    // Handle Save Slot Button Click
    $("#save-slot").click(function() {
        let slotTime = $("#slot-time").val();
        let slotDuration = $("#slot-duration").val();

        if (!slotTime || !slotDuration) {
            alert("Please select a valid date, time, and duration.");
            return;
        }

        $.ajax({
            url: calendarData.ajax_url,
            type: "POST",
            data: {
                action: "add_slot",
                nonce: calendarData.nonce,
                slot_time: slotTime,
                slot_duration: slotDuration
            },
            success: function(response) {
                if (response.success) {
                    alert("Slot added successfully!");
                    location.reload();
                } else {
                    alert("Error: " + response.data.message);
                }
            }
        });

        $("#slot-modal").dialog("close");
    });

    loadSlots();
});