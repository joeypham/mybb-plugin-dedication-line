<?php
/**
 * Dedication Line language file (Vietnamese)
 * Author: JLP423
 */

// === General UI ===
$l['dedicationline_title'] = '💌 Dòng Nhắn Gửi Hôm Nay';
$l['dedicationline_top_link'] = "Dòng Nhắn Gửi";
$l['dedicationline_send_a_dedication'] = 'Gửi Lời Nhắn';
$l['dedicationline_recipient'] = 'Người nhận:';
$l['dedicationline_message'] = 'Nội dung';
$l['dedicationline_post_anonymously'] = 'Gửi ẩn danh';
$l['dedicationline_everyone'] = 'Mọi người';
$l['dedicationline_specific_user'] = 'Người dùng cụ thể';
$l['dedicationline_search_user'] = 'Tìm người dùng...';
$l['dedicationline_search_users'] = 'Tìm người dùng...';
$l['dedicationline_max_rec_notice'] = 'Bạn có thể chọn tối đa {1} người.';
$l['dedicationline_send'] = 'Gửi Lời Nhắn';
$l['dedicationline_update'] = 'Cập Nhật';
$l['dedicationline_editing_notice'] = '<div style="text-align:center;margin:10px auto;padding:10px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;border-radius:6px;font-size:13px;font-weight:500;">
⚠️ Bạn đang chỉnh sửa lời nhắn của mình. Sau khi lưu, lời nhắn sẽ quay lại trạng thái chờ duyệt trước khi hiển thị lại.
</div>';
$l['dedicationline_editing_label'] = '(Đang chỉnh sửa...)';
$l['dedicationline_cancel'] = 'Hủy';
$l['dedicationline_edit'] = "Chỉnh sửa";
$l['dedicationline_delete'] = "Xóa";
$l['dedicationline_manage'] = '<button id="dl_togglebtn" type="button" style="float:right;">Quản lý</button>';
$l['dedicationline_back'] = '← Quay lại';
$l['dedicationline_display_to'] = '→';
$l['dedicationline_modpanel'] = 'Bảng Quản Lý';

// === Table Headings ===
$l['dedicationline_my_dedications'] = 'Lời Nhắn Của Tôi';
$l['dedicationline_select'] = 'Chọn';
$l['dedicationline_as'] = 'Dưới tên';
$l['dedicationline_from'] = 'Từ';
$l['dedicationline_to'] = 'Gửi đến';
$l['dedicationline_date'] = 'Ngày';
$l['dedicationline_status'] = 'Trạng thái';
$l['dedicationline_expires'] = 'Hết hạn';
$l['dedicationline_actions'] = 'Hành động';
$l['dedicationline_message_col'] = 'Nội dung';

// === Status / Labels ===
$l['dedicationline_approved'] = '<span style="color:green;">Đã duyệt</span>';
$l['dedicationline_pending'] = '<span style="color:gray;">Đang chờ</span>';
$l['dedicationline_never'] = 'Không bao giờ';
$l['dedicationline_anonymous'] = 'Ẩn danh';
$l['dedicationline_guest'] = 'Khách';
$l['dedicationline_system'] = 'Hệ thống';
$l['dedicationline_no_dedications_yet'] = '<span class="dedicationline-msg">Chưa có lời nhắn nào. Hãy là người đầu tiên!</span>';

// --- PM Notifications ---
$l['dedicationline_pm_subject_new'] = "{1} đã gửi cho bạn một lời nhắn!";
$l['dedicationline_pm_body_new'] = "{1} đã gửi cho bạn một lời nhắn:\n\n\"{2}\"";
$l['dedicationline_pm_subject_status'] = "Trạng thái lời nhắn của bạn đã thay đổi";
$l['dedicationline_pm_body_approved']  = "Lời nhắn của bạn đã được duyệt và hiện hiển thị công khai!";
$l['dedicationline_pm_body_unapproved'] = "Lời nhắn của bạn đã bị ẩn bởi người quản lý.";

// === User Table Messages ===
$l['dedicationline_no_dedications_today'] = 'Bạn chưa gửi lời nhắn nào hôm nay.';
$l['dedicationline_no_dedications_found'] = 'Không tìm thấy lời nhắn nào.';
$l['dedicationline_must_login_view'] = 'Bạn cần đăng nhập để xem các lời nhắn của mình.';

// === Counters / Limits ===
$l['dedicationline_counter_user'] = 'Bạn đã gửi <strong>{1}</strong> / <strong>{2}</strong> lời nhắn hôm nay. Còn lại {3}.';
$l['dedicationline_counter_unlimited'] = '∞';
$l['dedicationline_remaining'] = 'Bạn còn {1} lượt gửi hôm nay.';
$l['dedicationline_counter_shared_notice'] = '<div class="smalltext" style="margin-top:4px;color:#777;font-size:11px;">Giới hạn hàng ngày được chia sẻ giữa tài khoản khách và người dùng đăng nhập trên cùng thiết bị.</div>';
$l['dedicationline_error_maxtotalclear'] = "Bạn đã có {1} lời nhắn (đã duyệt hoặc đang chờ). Vui lòng đợi đến khi một số lời nhắn hết hạn hoặc bị xóa trước khi thêm mới.";
$l['dedicationline_error_maxpendingclear'] = "Bạn đã có {1} lời nhắn đang chờ duyệt. Vui lòng đợi cho đến khi được duyệt hoặc xóa bớt trước khi gửi thêm.";

// === Form Validation / Errors ===
$l['dedicationline_error_no_permission'] = 'Bạn không có quyền hạn để gửi lời nhắn.';
$l['dedicationline_error_no_message'] = 'Vui lòng nhập nội dung.';
$l['dedicationline_error_no_recipient'] = 'Vui lòng chọn ít nhất một người nhận.';
$l['dedicationline_error_antiflood'] = 'Vui lòng đợi {1} giây nữa trước khi gửi thêm.';
$l['dedicationline_error_dailymax'] = 'Bạn đã đạt giới hạn {1} lời nhắn mỗi ngày. Vui lòng thử lại vào ngày mai.';
$l['dedicationline_error_too_many_recipients'] = 'Bạn chỉ có thể gửi tối đa {1} người nhận cùng lúc.';
$l['dedicationline_error_guest_edit_delete'] = 'Khách không thể chỉnh sửa hoặc xóa lời nhắn.';
$l['dedicationline_error_invalid_id'] = 'ID lời nhắn không hợp lệ hoặc bạn không có quyền truy cập.';

// === Success Messages ===
$l['dedicationline_success_added'] = 'Lời nhắn của bạn đã được thêm!';
$l['dedicationline_success_submitted'] = 'Lời nhắn của bạn đã được gửi để chờ duyệt.';
$l['dedicationline_success_updated'] = 'Lời nhắn của bạn đã được cập nhật.';
$l['dedicationline_success_deleted'] = 'Lời nhắn của bạn đã bị xóa.';

// === Moderator Panel ===
$l['dedicationline_approve_selected'] = 'Duyệt mục đã chọn';
$l['dedicationline_unapprove_selected'] = 'Hủy duyệt mục đã chọn';
$l['dedicationline_delete_selected'] = 'Xóa mục đã chọn';
$l['dedicationline_delete_confirm'] = 'Bạn có chắc chắn muốn xóa lời nhắn này không?';
$l['dedicationline_mod_approved'] = 'Các lời nhắn đã chọn đã được duyệt và đặt thời gian hết hạn.';
$l['dedicationline_mod_unapproved'] = 'Các lời nhắn đã chọn đã bị hủy duyệt và xóa thời gian hết hạn.';
$l['dedicationline_mod_deleted'] = 'Các lời nhắn đã chọn đã bị xóa.';
$l['dedicationline_mod_none'] = 'Chưa chọn mục nào.';
$l['dedicationline_mod_no_dedications'] = 'Không có lời nhắn nào được tìm thấy.';
$l['dedicationline_error_no_slots'] = 'Không thể duyệt thêm lời nhắn nào ngay bây giờ (giới hạn hiển thị: {1}). Vui lòng đợi cho đến khi các lời nhắn cũ hết hạn.';
$l['dedicationline_error_not_enough_slots'] = 'Chỉ còn {1} chỗ trống (giới hạn: {2}). Vui lòng bỏ chọn bớt hoặc đợi thêm chỗ trống.';
$l['dedicationline_slots_available'] = 'Chỗ trống: {1} / {2}';
$l['dedicationline_unlimited_slots'] = 'Chỗ trống: Không giới hạn';
$l['dedicationline_filter_label'] = 'Lọc:';
$l['dedicationline_filter_all'] = 'Tất cả';
$l['dedicationline_filter_pending'] = 'Chờ duyệt';
$l['dedicationline_filter_approved'] = 'Đã duyệt';
$l['dedicationline_filter_perpage'] = 'Hiển thị mỗi trang:';
$l['dedicationline_filter_go'] = 'Xem';

// === Misc ===
$l['dedicationline_back_to_main'] = 'Quay lại trang chính';
$l['dedicationline_manage_tooltip'] = 'Mở bảng quản lý';
$l['dedicationline_guest_enabled'] = 'Đã bật chức năng gửi cho khách.';
$l['dedicationline_needs_login'] = 'Vui lòng đăng nhập để tiếp tục.';
$l['dedicationline_claim_banner'] = "Chúng tôi tìm thấy {1} lời nhắn bạn đã gửi khi chưa đăng nhập. <a href='{2}'><strong>Nhấn để liên kết ngay</strong></a>.";
$l['dedicationline_claim_success'] = "Các lời nhắn của bạn khi là khách đã được liên kết vào tài khoản!";
