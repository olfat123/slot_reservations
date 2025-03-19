jQuery(document).ready(function($) {
    function loadSlots() {
        $.post(ajaxurl, { action: 'get_slots' }, function(response) {
            if (response.success) {
                let slotsHtml = '';
                response.data.forEach(slot => {
                    slotsHtml += `
                        <tr>
                            <td>${slot.slot_time}</td>
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

    $('#add-slot').click(function() {
        const slotTime = prompt("Enter slot time (YYYY-MM-DD HH:MM:SS):");
        if (slotTime) {
            $.post(ajaxurl, {
                action: 'add_slot',
                slot_time: slotTime,
                nonce: calendarData.nonce
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadSlots();
                }
            });
        }
    });

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

    loadSlots();
});
