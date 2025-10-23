<?php
/**
 * Dedication Line language file
 * Author: JLP423
 */

// === General UI ===
$l['dedicationline_title'] = '💌 Dedications for Today';
$l['dedicationline_top_link'] = "Dedication Line";
$l['dedicationline_send_a_dedication'] = 'Send a Dedication';
$l['dedicationline_recipient'] = 'Recipient:';
$l['dedicationline_message'] = 'Message';
$l['dedicationline_post_anonymously'] = 'Post anonymously';
$l['dedicationline_everyone'] = 'Everyone';
$l['dedicationline_specific_user'] = 'Specific User';
$l['dedicationline_search_user'] = 'Search for a user...';
$l['dedicationline_search_users'] = 'Search for user(s)...';
$l['dedicationline_max_rec_notice'] = 'You can select up to {1} users.';
$l['dedicationline_send'] = 'Send Dedication';
$l['dedicationline_update'] = 'Update Dedication';
$l['dedicationline_editing_notice'] = '<div style="text-align:center;margin:10px auto;padding:10px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;border-radius:6px;font-size:13px;font-weight:500;">
⚠️ You are editing your dedication. Once saved, it will be sent back for approval before appearing again.
</div>';
$l['dedicationline_editing_label'] = '(Editing...)';
$l['dedicationline_cancel'] = 'Cancel';
$l['dedicationline_edit'] = "Edit";
$l['dedicationline_delete'] = "Delete";
$l['dedicationline_manage'] = '<button id="dl_togglebtn" type="button" style="float:right;">Manage</button>';
$l['dedicationline_back'] = '← Back';
$l['dedicationline_display_to'] = '→';
$l['dedicationline_modpanel'] = 'Moderation Panel';

// === Table Headings ===
$l['dedicationline_my_dedications'] = 'My Dedications';
$l['dedicationline_select'] = 'Select';
$l['dedicationline_as'] = 'As';
$l['dedicationline_from'] = 'From';
$l['dedicationline_to'] = 'To';
$l['dedicationline_date'] = 'Date';
$l['dedicationline_status'] = 'Status';
$l['dedicationline_expires'] = 'Expires';
$l['dedicationline_actions'] = 'Actions';
$l['dedicationline_message_col'] = 'Message';

// === Status / Labels ===
$l['dedicationline_approved'] = '<span style="color:green;">Approved</span>';
$l['dedicationline_pending'] = '<span style="color:gray;">Pending</span>';
$l['dedicationline_never'] = 'Never';
$l['dedicationline_anonymous'] = 'Anonymous';
$l['dedicationline_guest'] = 'Guest';
$l['dedicationline_system'] = 'System';
$l['dedicationline_no_dedications_yet'] = '<span class="dedicationline-msg">No dedications yet. Be the first!!!</span>';
// --- PM Notifications ---
$l['dedicationline_pm_subject_new'] = "{1} has sent you a dedication!";
$l['dedicationline_pm_body_new'] = "{1} has sent you a dedication:\n\n\"{2}\"";
$l['dedicationline_pm_subject_status'] = "Your dedication’s status changed";
$l['dedicationline_pm_body_approved']  = "Your dedication has been approved and is now visible to everyone!";
$l['dedicationline_pm_body_unapproved'] = "Your dedication has been unapproved and hidden by a moderator.";

// === User Table Messages ===
$l['dedicationline_no_dedications_today'] = 'You haven’t sent any dedications today yet.';
$l['dedicationline_no_dedications_found'] = 'No dedications found.';
$l['dedicationline_must_login_view'] = 'You must be logged in to view your dedications.';

// === Counters / Limits ===
$l['dedicationline_counter_user'] = 'You have posted <strong>{1}</strong> of <strong>{2}</strong> dedications today. {3} remaining.';
$l['dedicationline_counter_unlimited'] = '∞';
$l['dedicationline_remaining'] = 'You have {1} remaining today.';
$l['dedicationline_counter_shared_notice'] = '<div class="smalltext" style="margin-top:4px;color:#777;font-size:11px;">Daily limit is shared between guest and logged-in users on the same device.</div>';
$l['dedicationline_error_maxtotalclear'] = "You already have {1} dedications (approved or pending). Please wait until some are removed or expire before adding new ones.";
$l['dedicationline_error_maxpendingclear'] = "You already have {1} pending dedications awaiting approval. Please wait until some are approved or removed before submitting new ones.";


// === Form Validation / Errors ===
$l['dedicationline_error_no_permission'] = 'You do not have permission to send dedications.';
$l['dedicationline_error_no_message'] = 'Please enter a message.';
$l['dedicationline_error_no_recipient'] = 'Please enter at least one recipient.';
$l['dedicationline_error_antiflood'] = 'Please wait {1} more seconds before posting another dedication.';
$l['dedicationline_error_dailymax'] = 'You have reached your daily limit of {1} dedications. Please try again tomorrow.';
$l['dedicationline_error_too_many_recipients'] = 'You can only send to up to {1} recipients at once.';
$l['dedicationline_error_guest_edit_delete'] = 'Guests cannot edit or delete dedications.';
$l['dedicationline_error_invalid_id'] = 'Invalid or unauthorized dedication ID.';

// === Success Messages ===
$l['dedicationline_success_added'] = 'Your dedication has been added!';
$l['dedicationline_success_submitted'] = 'Your dedication has been submitted for approval.';
$l['dedicationline_success_updated'] = 'Your dedication has been updated.';
$l['dedicationline_success_deleted'] = 'Your dedication has been deleted.';

// === Moderator Panel ===
$l['dedicationline_approve_selected'] = 'Approve Selected';
$l['dedicationline_unapprove_selected'] = 'Unapprove Selected';
$l['dedicationline_delete_selected'] = 'Delete Selected';
$l['dedicationline_delete_confirm'] = 'Are you sure you want to delete this dedication?';
$l['dedicationline_mod_approved'] = 'Selected dedications approved and expiration set.';
$l['dedicationline_mod_unapproved'] = 'Selected dedications unapproved and expiration removed.';
$l['dedicationline_mod_deleted'] = 'Selected dedications deleted.';
$l['dedicationline_mod_none'] = 'No items selected.';
$l['dedicationline_mod_no_dedications'] = 'No dedications found.';
$l['dedicationline_error_no_slots'] = 'No more dedications can be approved right now (display limit: {1}). Please wait for older ones to expire.';
$l['dedicationline_error_not_enough_slots'] = 'Only {1} more dedications can be approved (limit: {2}). Please uncheck some items or wait until space is available.';
$l['dedicationline_slots_available'] = 'Slots available: {1} / {2}';
$l['dedicationline_unlimited_slots'] = 'Slots: Unlimited';
$l['dedicationline_filter_label'] = 'Filter:';
$l['dedicationline_filter_all'] = 'All';
$l['dedicationline_filter_pending'] = 'Pending';
$l['dedicationline_filter_approved'] = 'Approved';
$l['dedicationline_filter_perpage'] = 'Per page:';
$l['dedicationline_filter_go'] = 'Go';


// === Misc ===
$l['dedicationline_back_to_main'] = 'Back to main page';
$l['dedicationline_manage_tooltip'] = 'Open management panel';
$l['dedicationline_guest_enabled'] = 'Guest posting is enabled.';
$l['dedicationline_needs_login'] = 'Please log in to continue.';
$l['dedicationline_claim_banner'] = "We found {1} dedications you posted as a guest. <a href='{2}'><strong>Claim them now</strong></a>.";
$l['dedicationline_claim_success'] = "Your guest dedications have been linked to your account!";
