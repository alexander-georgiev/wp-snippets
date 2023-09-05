<?php 
public function add_ticket_note() {

        $do_check = check_ajax_referer('tzn-support-system', 'nonce', false);
        if (!$do_check) {
            die('Nonce value cannot be verified.');
        }

        $post_id = (int) $_POST['post_id'];
        $type = (int) $_POST['type'];
        $msg = wp_kses($_POST['msg'], 'post');
        $data = [];
        $append_data[] = array('date' => wp_resolve_post_date(date("Y-m-d H:i:s")), 'content' => $msg, 'author' => get_current_user_id(), 'type' => $type);

        $notes = get_post_meta($post_id, 'ticket_notes', true);
        if (!empty($notes)) {
            $data = [...$notes, ...$append_data];
            $latest_index = count($notes);
        } else {
            $latest_index = 0;
            $data = $append_data;
        }
        if (!empty($data)) $update = update_post_meta($post_id, 'ticket_notes', $data);
        if ($update) {
            wp_send_json_success(array(
                'note' => $this->ticket_note_template($append_data[0], $latest_index),
            ));
        }
        wp_die();
    }
   add_action("wp_ajax_add_ticket_note", [$this, 'add_ticket_note']);
  
?>
jQuery(document).ready(function ($) { 
 function add_note() {
        $('#postbox-ticket_notes').on('click', '.add_note', function () {
            var container = $(this).closest('#postbox-ticket_notes'),
                post_id = $(this).closest('form').find('input[name="item"]').val(),
                type = container.find('#order_note_type').find(":selected").val(),
                msg = $(this).closest('form').find('#add_order_note').val(),
                self = $(this);

            $.ajax({
                url: tznSupport.ajaxurl,
                type: "POST",
                dataType: 'JSON',
                data: {
                    action: 'add_ticket_note', //this value is first parameter of add_action
                    post_id: post_id,
                    type: type,
                    msg: msg,
                    nonce: tznSupport.nonce
                },
                beforeSend: function () {
                    $('.ticket-note').addClass('fading');
                },
                success: function (res) {
                    let note = res.data.note;
                    container.find('.inside').prepend(note);
                },
                complete: function () {
                    $('.ticket-note').removeClass('fading');
                },
            });
        });
    }
    add_note();
}
<?php 
public function delete_ticket_note() {

        $do_check = check_ajax_referer('tzn-support-system', 'nonce', false);
        if (!$do_check) {
            die('Nonce value cannot be verified.');
        }

        $post_id = (int) $_POST['post_id'];
        $type = (int) $_POST['type'];

        $ticket_notes = get_post_meta($post_id, 'ticket_notes', true);

        //all
        if ($type === 1) {
            $delete = delete_post_meta($post_id, 'ticket_notes');
            if ($delete) {
                wp_send_json_success(array(
                    'action' => 'delete all',
                ));
            }
        }
        //specific
        if ($type === 0) {
            $index = $_POST['note_index'];
            unset($ticket_notes[$index]);
            $delete = update_post_meta($post_id, 'ticket_notes', $ticket_notes);

            if ($delete)  wp_send_json_success(array(
                'action' => 'delete',
            ));
        }

        wp_die();
    } 

  add_action("wp_ajax_delete_ticket_note", [$this, 'delete_ticket_note']);
       ?>
function delete_note() {
        $('#postbox-ticket_notes').on('click', '.delete-note', function () {
            var post_id = $(this).closest('form').find('input[name="item"]').val(),
                type = ($(this).hasClass('delete-notes') ? 1 : 0),
                index = $(this).data('note'),
                self = $(this),
                note = (type == 0 ? $(this).closest('.ticket-note') : $('.ticket-note'));
            $.ajax({
                url: tznSupport.ajaxurl,
                type: "POST",
                dataType: 'JSON',
                data: {
                    action: 'delete_ticket_note', //this value is first parameter of add_action
                    post_id: post_id,
                    type: type,
                    note_index: index,
                    nonce: tznSupport.nonce
                },
                beforeSend: function () {
                    note.addClass('fading');
                },
                success: function (res) {
                    note.remove();
                    // if (res.data.action === 'delete all') $('.ticket-note').remove();
                    // if (res.data.action === 'delete') note.remove();
                },
                complete: function () {
                    note.removeClass('fading');
                },
            });
        });
    }
    delete_note();
});
