<?php
/**
 * Dedication Line (MyBB 1.8.x)
 * A simple global dedication ticker for short messages.
 *
 * Author: JLP423
 * Version: 1.0
 */

if (!defined('IN_MYBB')) die('No direct access.');

function dedicationline_info()
{
    return [
        'name'          => 'Dedication Line',
        'description'   => 'Let users post short dedication messages displayed globally as a scrolling ticker with theming support and management panel.',
        'website'       => 'https://mybb.vn',
        'author'        => 'JLP423',
        'authorsite'    => 'https://mybb.vn',
        'version'       => '1.0',
        'codename'      => 'dedicationline',
        'compatibility' => '18*'
    ];
}

function dedicationline_install()
{
    global $db;

    if (!$db->table_exists('dedicationline')) {
        $db->write_query("
            CREATE TABLE `" . TABLE_PREFIX . "dedicationline` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `uid` INT UNSIGNED NOT NULL DEFAULT 0,
                `recipient` VARCHAR(120) NOT NULL DEFAULT '',
                `message` TEXT NOT NULL,
                `dateline` INT UNSIGNED NOT NULL DEFAULT 0,
                `anonymous` TINYINT(1) NOT NULL DEFAULT 0,
                `approved` TINYINT(1) NOT NULL DEFAULT 1,
                `expires` INT UNSIGNED NOT NULL DEFAULT 0,
                `ipaddress` VARCHAR(50) NOT NULL DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $db->write_query("
            ALTER TABLE `" . TABLE_PREFIX . "dedicationline`
                ADD INDEX (`uid`),
                ADD INDEX (`expires`),
                ADD INDEX approved_dateline (approved, dateline)
        ");
    }

    dedicationline_install_settings();
    dedicationline_install_templates();
}

function dedicationline_is_installed()
{
    global $db;
    return $db->table_exists('dedicationline');
}

function dedicationline_uninstall()
{
    global $db;
    if ($db->table_exists('dedicationline')) $db->drop_table('dedicationline');
    $db->delete_query('templategroups', "prefix='dedicationline'");
    $db->delete_query('templates', "title LIKE 'dedicationline_%'");
    $db->delete_query('settings', "name LIKE 'dedicationline_%'");
    $db->delete_query('settinggroups', "name='dedicationline'");
    rebuild_settings();
}

function dedicationline_install_settings()
{
    global $db;

    $max_disporder = (int)$db->fetch_field(
        $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder'),
        'max_disporder'
    );
    $next_disporder = $max_disporder + 1;

    $group = [
        'name'        => 'dedicationline',
        'title'       => 'Dedication Line Settings',
        'description' => 'Control plugin behavior and appearance.',
        'disporder'   => $next_disporder,
        'isdefault'   => 0
    ];
    $gid = (int)$db->insert_query('settinggroups', $group);

    $settings = [
        ['dedicationline_enable', 'Enable Dedication Line', 'Turn the dedication on or off.', 'yesno', '1', 1],
        ['dedicationline_title', 'Title', 'Text displayed before the scrolling messages.', 'text', '💌 Dedications for Today', 2],
        ['dedicationline_theme', 'Theme', 'Choose a theme for the Dedication Line.', 'select\nclassic=Classic Blue\nlove=Love (Pink/Red)\nshoutout=Shoutout (Orange)\ncoolwave=Coolwave (Cyan/Purple)\nsunset=Sunset (Warm)\ndark=Minimal Dark', 'classic', 3],
        ['dedicationline_maxdisplay', 'Max Displayed Messages', 'Maximum number of messages shown in rotation (0 = no limit).', 'numeric', '10', 4],
        ['dedicationline_speed', 'Scroll Speed (Seconds)', 'Enter duration in seconds for one full scroll cycle. Lower is faster.', 'numeric', '30', 5],
        ['dedicationline_continuous', 'Continuous Scrolling', 'Messages scroll continuously without reset.', 'yesno', '0', 6],
        ['dedicationline_scrolldir', 'Scroll Direction', 'Direction in which messages scroll.', 'select\nleft=Left to Right\nright=Right to Left', 'left', 7],

        ['dedicationline_maxlen', 'Max Message Length', 'Maximum number of characters per dedication message.', 'numeric', '200', 8],
        ['dedicationline_allowbbcode', 'Allow BBCode', 'Allow BBCode formatting in messages.', 'yesno', '1', 9],
        ['dedicationline_allowsmilies', 'Allow Smilies', 'Allow smilies in messages.', 'yesno', '1', 10],
        ['dedicationline_allowanon', 'Allow Anonymous Messages', 'Users can post without showing their name.', 'yesno', '1', 11],

        ['dedicationline_multirec', 'Allow Multiple Recipients', 'Give the ability to list multiple recipients at once.', 'yesno', '0', 12],
        ['dedicationline_maxrec', 'Max Recipients per Message', 'Limit how many users one message can be sent to.', 'numeric', '3', 13],
        ['dedicationline_viewgroups', 'Groups Allowed to View Dedications', 'Select the usergroups that can see the Dedication Line ticker and messages.', 'groupselect', '1,2,3,4,6', 14],
		['dedicationline_postgroups', 'Groups Allowed to Post Dedications', 'Select the usergroups that can submit new dedications.', 'groupselect', '2,3,4,6', 15],
        ['dedicationline_managegroups', 'ModCP Management Groups', 'Usergroups allowed to manage dedications.', 'groupselect', '4', 16],
        ['dedicationline_reveal_anon', 'Reveal Anonymous Senders to Mods', 'If enabled, moderators and allowed management groups can see who sent anonymous dedications.', 'yesno', '1', 17],

        ['dedicationline_require_approval', 'Require Approval', 'New dedications must be approved before appearing.', 'yesno', '0', 18],
        ['dedicationline_autopromo', 'Auto Promotion', 'Auto approve messages next in line.', 'yesno', '0', 19],
        ['dedicationline_autodelete', 'Auto-Delete (Hours)', 'Delete dedications older than X hours (0 = never).', 'numeric', '24', 20],
        ['dedicationline_notify_pm', 'Send PM Notifications', 'Send private messages when users receive or have their dedication status changed.', 'yesno', '0', 21],

        ['dedicationline_claimafterlogin', 'Allow Guests to Claim Dedications After Login', 'If enabled, guests who later register or log in from the same IP can claim their previous dedications as their own.', 'yesno', '1', 22],

        ['dedicationline_antiflood', 'Anti-Flood (Seconds)', 'Minimum wait time between new dedications  (0 = no wait time).', 'numeric', '30', 23],
        ['dedicationline_dailymax', 'Max Dedications per Day', 'Maximum number of dedications per 24 hours (0 = no limit).', 'numeric', '5', 24],
		['dedicationline_maxdedications', 'Max Dedications per User', 'Maximum number of dedications (approved or pending) a user or guest can have at once (0 = no limit).', 'numeric', '5', 25],
		['dedicationline_cap_mode', 'Dedication Limit Mode', 'Choose which dedications count toward the limit.', 'select\npending=Pending Only\nall=All (Pending + Approved)', 'all', 26]

    ];

    foreach ($settings as $s) {
        [$name, $title, $desc, $type, $val, $order] = $s;
        $db->insert_query('settings', [
            'name' => $name,
            'title' => $title,
            'description' => $desc,
            'optionscode' => $type,
            'value' => $val,
            'disporder' => $order,
            'gid' => $gid
        ]);
    }

    rebuild_settings();
}

function dedicationline_install_templates()
{
    global $db;

	if (!$db->fetch_field($db->simple_select('templategroups', 'gid', "prefix='dedicationline'"), 'gid')) {
		$db->insert_query('templategroups', [
			'prefix' => 'dedicationline',
			'title'  => 'Dedication Line'
		]);
	}

    $templates = [
'dedicationline_message' => <<<'HTML'
<span class="dedicationline-msg">{$from} {$lang->dedicationline_display_to} {$to_display}: {$msg}</span>
HTML,

'dedicationline_continuous' => <<<'HTML'
<style>
.dedicationline-inner {
  line-height: 1.8em;
  will-change: transform;
}
.dedicationline-track {
  display: flex;
  width: max-content;
  animation: dedication-scroll-continuous {$speed}s linear infinite;
}
.dedicationline-bar:hover .dedicationline-track,
.dedicationline-scroll:hover .dedicationline-track {
  animation-play-state: paused;
}

@keyframes dedication-scroll-continuous {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

{$scroll_keyframes}
</style>

<div class="dedicationline-bar theme-{$theme}">
  <span class="dedicationline-label">{$mybb->settings['dedicationline_title']}</span>
  <div class="dedicationline-scroll">
    <div class="dedicationline-track" id="dl-track">
      <div class="dedicationline-inner">{$dedicationline_messages}</div>
      <div class="dedicationline-inner">{$dedicationline_messages}</div>
    </div>
  </div>
</div>

<script>{$dedicationline_js}</script>
HTML,

'dedicationline_noncontinuous' => <<<'HTML'
<style>
.dedicationline-inner {
  display: inline-block;
  white-space: nowrap;
  padding-left: 100%;
  animation: dedication-scroll-fade {$speed}s linear infinite;
}
.dedicationline-bar:hover .dedicationline-inner {
  animation-play-state: paused;
}
@keyframes dedication-scroll-fade {
  0%   { transform: translateX(0%); opacity: 0; }
  5%   { opacity: 1; }
  95%  { opacity: 1; }
  100% { transform: translateX(-100%); opacity: 0; }
}

{$scroll_keyframes}
</style>

<div class="dedicationline-bar theme-{$theme}">
  <span class="dedicationline-label">{$mybb->settings['dedicationline_title']}</span>
  <div class="dedicationline-scroll">
    <div class="dedicationline-inner">{$dedicationline_messages}</div>
  </div>
</div>
<script>{$dedicationline_js}</script>
HTML,

'dedicationline_main' => <<<'HTML'
<html>
<head>
<title>{$mybb->settings['bbname']} - {$mybb->settings['dedicationline_title']}</title>
{$headerinclude}
<script src="{$mybb->settings['bburl']}/jscripts/dedicationline.js"></script>
</head>

<body>
{$header}

<div class="dedicationline-container">
  <!-- === Main View === -->
  <div class="dl-mainview">
    <div style="position:relative;">
      {$dedicationline_form}
      {$user_messages_html}
    </div>
  </div>

  <!-- === Mod View === -->
  <div class="dl-modview">
    {$dedicationline_modpanel_html}
  </div>
</div>

{$footer}
</body>
</html>

HTML,

'dedicationline_form' => <<<'HTML'
<link rel="stylesheet" href="{$mybb->asset_url}/jscripts/select2/select2.css?ver=1807">
<script type="text/javascript" src="{$mybb->asset_url}/jscripts/select2/select2.min.js?ver=1806"></script>
<script>
window.DedicationLineConfig = {
  multirec: {$mybb->settings['dedicationline_multirec']},
  maxrec: {$mybb->settings['dedicationline_maxrec']},
  lang: {
    search_users: "{$lang->dedicationline_search_users}",
    search_user: "{$lang->dedicationline_search_user}"
  }
};
</script>
{$editing_notice}
<form action="misc.php?action=dedicationline" method="post">
<table class="tborder" style="margin:20px auto;">
<tr><td class="thead" colspan="2"><strong>{$lang->dedicationline_send_a_dedication}</strong>
    {$dedicationline_manage_button}</td></tr>
<tr>
  <td class="trow1" style="width:30%;">{$lang->dedicationline_recipient}</td>
  <td class="trow1">
    <label><input type="radio" name="recipient_type" value="everyone" id="rec_all" checked> {$lang->dedicationline_everyone}</label><br>
    <label><input type="radio" name="recipient_type" value="specific" id="rec_specific"> {$lang->dedicationline_specific_user}</label>
    <div id="recipient_box" style="margin-top:6px;display:none;">
      <input type="text" class="textbox" name="recipient" id="to" size="40" value="{$recipient_value}" tabindex="1" />
	  <div class='smalltext' style='color:#666;margin:5px 0 8px;'>{$max_recipient_notice}</div>
    </div>
  </td>
</tr>
<tr>
  <td class="trow1">{$lang->dedicationline_message}</td>
  <td class="trow1">
    <textarea id="dedication_message" name="message" rows="8" cols="80" maxlength="{$mybb->settings['dedicationline_maxlen']}" required>{$edit_message}</textarea>
    <div id="msg_counter" style="font-size:12px;margin-top:4px;color:#666;">
      0 / {$mybb->settings['dedicationline_maxlen']}
    </div>
  </td>
</tr>
<tr>
    {$anon_checkbox}
    <td class="trow1" colspan="{$counter_colspan}">
        <div class="smalltext" style="text-align:center;margin-top:6px;color:#666;">
            {$dedicationline_counter_html}
        </div>
    </td>
</tr>
<tr><td class="tfoot" colspan="2" style="text-align:center;">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}">
<input type="hidden" name="edit_id" value="{$edit_id}">
<input type="submit" class="button" value="{$button_text}">
<input type="hidden" name="form_type" value="user_form">
</td></tr>
</table>
</form>
HTML,

'dedicationline_anon_checkbox' => <<<'HTML'
<td class="trow1">
    <label>
        <input type="checkbox" name="anonymous" value="1">
        {$lang->dedicationline_post_anonymously}
    </label>
</td>
HTML,

'dedicationline_user_messages' => <<<'HTML'
<br>
<table class="tborder" style="margin:20px auto;">
<tr><td class="thead" colspan="7"><strong>{$lang->dedicationline_my_dedications}</strong></td></tr>
<tr class="tcat">
  <td style="width:15%;text-align:center;">{$lang->dedicationline_as}</td>
  <td style="width:15%;text-align:center;">{$lang->dedicationline_to}</td>
  <td style="text-align:center;">{$lang->dedicationline_message}</td>
  <td style="width:15%;text-align:center;">{$lang->dedicationline_date}</td>
  <td style="width:10%;text-align:center;">{$lang->dedicationline_status}</td>
  <td style="width:15%;text-align:center;">{$lang->dedicationline_expires}</td>
  <td style="width:10%;text-align:center;">{$lang->dedicationline_actions}</td>
</tr>

  {$dedicationline_userrows}
</table>
HTML,

'dedicationline_user_row' => <<<'HTML'
<tr class="trow1 {$editing_class}">
  <td style="text-align:center;">{$from_display}</td>
  <td style="text-align:center;">{$to_display}</td>
  <td>{$msg}
    <span style="color:#d97706;font-size:12px;font-weight:600;">
    <!-- only visible when class applied -->
    {$editing_label}
  </span></td>
  <td style="text-align:center;">{$date}</td>
  <td style="text-align:center;">{$status}</td>
  <td style="text-align:center;">{$expires}</td>
  <td style="text-align:center;">
    <a href="{$edit_url}">{$edit_label}</a> |
    <a href="{$delete_url}" onclick="return confirm('{$lang->dedicationline_delete_confirm}');">
      {$lang->dedicationline_delete}
    </a>
  </td>
</tr>

HTML,

'dedicationline_modpanel' => <<<'HTML'
<form action="misc.php?action=dedicationline" method="post" class="dedicationline-modpanel">
<table class="tborder" style="margin:20px auto;">
  <tr>
	<td class="thead" colspan="7">
	  <button id="dl_backbtn" type="button" style="float:left;margin-right:10px;">{$lang->dedicationline_back}</button>
	  <strong>{$lang->dedicationline_modpanel}</strong>
	  <span style="
  float:right;
  background:linear-gradient(135deg,#16a34a,#22c55e);
  color:#fff;
  padding:4px 10px;
  border-radius:20px;
  font-size:13px;
  font-weight:600;
  box-shadow:0 1px 2px rgba(0,0,0,0.1);
">
		{$slots_available_text}
	  </span>
	</td>
  </tr>
	<tr class="tcat">
	  <td style="width:5%;text-align:center;">{$lang->dedicationline_select}</td>
	  <td style="width:15%;text-align:center;">{$lang->dedicationline_from}</td>
	  <td style="width:15%;text-align:center;">{$lang->dedicationline_to}</td>
	  <td style="text-align:center;">{$lang->dedicationline_message}</td>
	  <td style="width:15%;text-align:center;">{$lang->dedicationline_date}</td>
	  <td style="width:10%;text-align:center;">{$lang->dedicationline_status}</td>
	  <td style="width:15%;text-align:center;">{$lang->dedicationline_expires}</td>
	</tr>
  {$dedicationline_modrows}
	<tr>
	  <td class="tfoot" colspan="7" style="text-align:right;">
		<input type="hidden" name="my_post_key" value="{$mybb->post_code}">
		<button type="submit" name="modaction" value="approve" class="button">{$lang->dedicationline_approve_selected}</button>
		<button type="submit" name="modaction" value="unapprove" class="button">{$lang->dedicationline_unapprove_selected}</button>
		<button type="submit" name="modaction" value="delete" class="button" style="background:#dc2626;color:#fff;">{$lang->dedicationline_delete_selected}</button>
	  </td>
	</tr>
</table>
</form>
HTML,

'dedicationline_modpanel_row' => <<<'HTML'
<tr class="trow1">
  <td style="text-align:center;"><input type="checkbox" name="checked[]" value="{$id}"></td>
  <td style="text-align:center;">{$from_display}</td>
  <td style="text-align:center;">{$to_display}</td>
  <td>{$msg}</td>
  <td style="text-align:center;">{$date}</td>
  <td style="text-align:center;">{$status}</td>
  <td style="text-align:center;">{$expires}</td>
</tr>
HTML,

'dedicationline_modpanel_filter' => <<<'HTML'
<form method="get" action="misc.php" style="margin:10px 0; text-align:right;">
  <input type="hidden" name="action" value="dedicationline">
  <label style="font-weight:600;margin-right:4px;">{$lang->dedicationline_filter_label}</label>
  <select name="status" onchange="this.form.submit()">
    <option value="all" {$sel_all}>{$lang->dedicationline_filter_all}</option>
	<option value="pending" {$sel_pending}>{$lang->dedicationline_filter_pending}</option>
	<option value="approved" {$sel_approved}>{$lang->dedicationline_filter_approved}</option>
  </select>
  &nbsp;&nbsp;
  <label style="font-weight:600;margin-right:4px;">{$lang->dedicationline_filter_perpage}</label>
  <select name="perpage" onchange="this.form.submit()">
    <option value="10" {$sel10}>10</option>
    <option value="20" {$sel20}>20</option>
    <option value="50" {$sel50}>50</option>
  </select>
  <noscript><input type="submit" value="{$lang->dedicationline_filter_go}" class="button"></noscript>
</form>
HTML,

'dedicationline_modpanel_rpagination' => <<<'HTML'
<tr>
    <td colspan="7" class="tfoot" style="text-align:right;">
        {$multipage}
    </td>
</tr>
HTML,

'dedicationline_claim_banner' => <<<'HTML'
<div id="dedicationline-claim-banner" class="pm_alert" style="margin:10px auto;text-align:center;position:relative;">
    {$lang->sprintf($lang->dedicationline_claim_banner, $count, $claim_url)}
    <button id="dl-claim-dismiss" style="position:absolute;right:10px;bottom:0;border:none;background:none;font-size:18px;color:#555;cursor:pointer;">×</button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  DedicationLine.initClaimBanner({
    ip: '{$mybb->session->ipaddress}',
    count: {$count}
  });
});
</script>
HTML
    ];

    foreach ($templates as $title => $tpl) {
        $db->insert_query('templates', [
            'title' => $title,
            'template' => $db->escape_string($tpl),
            'sid' => '-2'
        ]);
    }
}


function dedicationline_activate()
{
    global $db, $lang;
	
    require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
    require_once MYBB_ADMIN_DIR . 'inc/functions_themes.php';
    find_replace_templatesets('index', '#' . preg_quote('{$forums}') . '#i', '{$dedicationline_block}{$forums}');
    find_replace_templatesets('header', '#' . preg_quote('{$menu_portal}') . '#i', '{$menu_portal} <li><a href="misc.php?action=dedicationline" class="dedicationline">{$lang->dedicationline_top_link}</a></li>');
    
    $stylesheet = <<<CSS
/* Load cursive font*/
@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap');

/* === Layout Wrapper === */
.dedicationline-container {
  position: relative;
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  min-height: 500px;
  overflow: visible;
}

/* === Main and Mod Views === */
.dl-mainview, .dl-modview {
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  transition: transform .45s ease, opacity .45s ease;
}

.dl-mainview {
  transform: translateX(0);
  opacity: 1;
  z-index: 2;
}
.dl-modview {
  transform: translateX(100%);
  opacity: 0;
  z-index: 1;
}

/* When active, slide left to reveal mod view */
.dedicationline-container.show-modview .dl-mainview {
  transform: translateX(-100%);
  opacity: 0;
}
.dedicationline-container.show-modview .dl-modview {
  transform: translateX(0);
  opacity: 1;
  z-index: 3;
}

/* === Manage & Back Buttons === */
#dl_togglebtn {
  display: inline-block;
  float: right;
  background: #f4f4f4;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  color: #333;
  transition: background 0.2s ease;
}
#dl_togglebtn:hover { background: #e9e9e9; }

#dl_backbtn {
  display: inline-block;
  background: #f4f4f4;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  color: #333;
  transition: background 0.2s ease;
}
#dl_backbtn:hover { background: #e9e9e9; }

.no-transition * {
  transition: none !important;
}

.dedicationline-modpanel .button {
  padding: 6px 12px;
  margin-left: 6px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s ease;
}
.dedicationline-modpanel .button:hover {
  opacity: 0.85;
}
/* Mobile fallback: fade instead of slide */
@media (max-width: 768px){
  .dl-mainview, .dl-modview {
    transition: opacity .3s ease;
    transform: none !important;
  }
  .dedicationline-container.show-modview .dl-mainview {
    opacity: 0;
    pointer-events: none;
  }
  .dedicationline-container.show-modview .dl-modview {
    opacity: 1;
  }
}

.dedicationline-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  border-radius: 10px;
  padding: 10px 16px;
  margin: 14px 0;
  font-size: 0.93rem;
  font-weight: 500;
  overflow: hidden;
  position: relative;
  color: #333;
  background: linear-gradient(135deg, #ffffff, #fafafa);
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.dedicationline-label {
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  flex-shrink: 0;
  color: #444;
  font-family: "Dancing Script", "Segoe UI", cursive;
  font-weight: 600;
  font-style: normal;
}

.dedicationline-scroll {
  flex: 1;
  position: relative;
  overflow: hidden;
  height: 1.8em;
  white-space: nowrap;
}

/* === Fade Edges (always exist) === */
.dedicationline-scroll::before,
.dedicationline-scroll::after {
  content: "";
  position: absolute;
  top: 0;
  width: 60px;
  height: 100%;
  pointer-events: none;
  z-index: 3;
  transition: opacity 0.3s ease;
}
.dedicationline-scroll::before { left: 0; }
.dedicationline-scroll::after  { right: 0; }


.dedicationline-msg {
  display: inline-block;
  margin-right: 80px;
}

.dedicationline-msg a:hover {
  text-decoration: underline;
  opacity: 0.9;
}

#logo ul.top_links a.dedicationline {
    background-position: 0 -120px;
}

/* --- Select2 fix for single Dedication Line --- */
#recipient_box .select2-container {
  width: 100% !important;
  min-width: 300px;
  box-sizing: border-box;
}
.select2-selection--multiple {
  border-radius: 6px !important;
  border: 1px solid #ccc !important;
  min-height: 38px;
  padding: 2px 4px;
}
.select2-selection__choice {
  background: #e0f2fe !important;
  border: none !important;
  color: #0c4a6e !important;
  margin-top: 3px !important;
  font-weight: 500;
}

.dl-status-badge {
  display:inline-block;
  margin-left:8px;
  padding:2px 6px;
  border-radius:4px;
  font-size:12px;
  border:1px solid transparent;
}
.dl-status-badge.approved {
  background:#dcfce7;
  color:#166534;
  border-color:#86efac;
}
.dl-status-badge.pending {
  background:#fee2e2;
  color:#991b1b;
  border-color:#fca5a5;
}

/* Highlight the currently edited dedication */
.dedicationline-editing {
  background-color: #fff8e1 !important; /* soft yellow highlight */
  transition: background-color 0.3s ease;
}
.dedicationline-editing td {
  border-bottom: 1px solid #facc15; /* light gold line for clarity */
}

/* --- Dedication Line smilies fix and alignment --- */
.dedicationline-msg img.smilie,
.dedicationline-modpanel img.smilie,
.dedicationline-user img.smilie {
  vertical-align: middle;
  height: 18px;
  width: auto;
  margin: 0 2px;
  opacity: 0.95;
  transition: opacity 0.2s ease;
}

.dedicationline-msg img.smilie:hover,
.dedicationline-modpanel img.smilie:hover,
.dedicationline-user img.smilie:hover {
  opacity: 1;
  transform: scale(1.05);
}


/* =====================================
   THEME PRESETS WITH PER-THEME FADES
   ===================================== */

/* Classic Blue — soft sky fade */
.theme-classic {
  background: linear-gradient(135deg, #e0f2fe, #f0f9ff);
  box-shadow: 0 2px 6px rgba(56,189,248,0.25);
  color: #0c4a6e;
}
.theme-classic .dedicationline-scroll::before {
  background: linear-gradient(to right, rgba(224,242,254,1), rgba(224,242,254,0));
}
.theme-classic .dedicationline-scroll::after {
  background: linear-gradient(to left, rgba(240,249,255,1), rgba(240,249,255,0));
}

/* Love — rose fade */
.theme-love {
  background: linear-gradient(135deg, #fde2e9, #fff1f3);
  box-shadow: 0 2px 6px rgba(244,114,182,0.25);
  color: #9d174d;
}
.theme-love .dedicationline-scroll::before {
  background: linear-gradient(to right, rgba(253,226,233,1), rgba(253,226,233,0));
}
.theme-love .dedicationline-scroll::after {
  background: linear-gradient(to left, rgba(255,241,243,1), rgba(255,241,243,0));
}

/* Shoutout — warm amber fade */
.theme-shoutout {
  background: linear-gradient(135deg, #fef3c7, #fff7ed);
  box-shadow: 0 2px 6px rgba(245,158,11,0.25);
  color: #92400e;
}
.theme-shoutout .dedicationline-scroll::before {
  background: linear-gradient(to right, rgba(254,243,199,1), rgba(254,243,199,0));
}
.theme-shoutout .dedicationline-scroll::after {
  background: linear-gradient(to left, rgba(255,247,237,1), rgba(255,247,237,0));
}

/* Coolwave — cyan/purple fade */
.theme-coolwave {
  background: linear-gradient(135deg, #e0f7fa, #f3e8ff);
  box-shadow: 0 2px 6px rgba(14,165,233,0.25);
  color: #065f46;
}
.theme-coolwave .dedicationline-scroll::before {
  background: linear-gradient(to right, rgba(224,247,250,1), rgba(224,247,250,0));
}
.theme-coolwave .dedicationline-scroll::after {
  background: linear-gradient(to left, rgba(243,232,255,1), rgba(243,232,255,0));
}

/* Sunset — coral fade */
.theme-sunset {
  background: linear-gradient(135deg, #ffe4e1, #fff0e6);
  box-shadow: 0 2px 6px rgba(249,115,22,0.25);
  color: #b91c1c;
}
.theme-sunset .dedicationline-scroll::before {
  background: linear-gradient(to right, rgba(255,228,225,1), rgba(255,228,225,0));
}
.theme-sunset .dedicationline-scroll::after {
  background: linear-gradient(to left, rgba(255,240,230,1), rgba(255,240,230,0));
}

/* Dark — smooth fade + better readability */
.theme-dark {
  background: linear-gradient(135deg, #1a2235, #0c1322);
  box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  color: #e6edf3;
}

.theme-dark .dedicationline-label {
  color: #f9fafb;
  text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.theme-dark .dedicationline-scroll::before,
.theme-dark .dedicationline-scroll::after {
  content: "";
  position: absolute;
  top: 0;
  width: 80px;
  height: 100%;
  pointer-events: none;
  z-index: 3;
}

/* Left fade — subtle blue glow */
.theme-dark .dedicationline-scroll::before {
  left: 0;
  background: linear-gradient(
    to right,
    rgba(23,33,52,0.9) 0%,
    rgba(23,33,52,0.6) 30%,
    rgba(23,33,52,0.3) 60%,
    rgba(23,33,52,0) 100%
  );
}

/* Right fade — smooth transparent blend */
.theme-dark .dedicationline-scroll::after {
  right: 0;
  background: linear-gradient(
    to left,
    rgba(10, 15, 25, 0.7) 0%,     /* darkest edge, anchors right side */
    rgba(10, 15, 25, 0.4) 25%,    /* gradual lift */
    rgba(10, 15, 25, 0.15) 60%,   /* subtle fade */
    rgba(10, 15, 25, 0) 100%      /* fully transparent into background */
  );
}
CSS;

    $query = $db->simple_select('themes', 'tid');
    while ($theme = $db->fetch_array($query)) {
        $record = [
            'name'        => 'dedicationline.css',
            'tid'         => (int)$theme['tid'],
            'stylesheet'  => $db->escape_string($stylesheet),
            'cachefile'   => 'dedicationline.css',
            'lastmodified'=> TIME_NOW
        ];
        $db->insert_query('themestylesheets', $record);
        cache_stylesheet($record['tid'], $record['cachefile'], $stylesheet);
        update_theme_stylesheet_list($record['tid'], false, true);
    }
}

function dedicationline_deactivate()
{
    global $db;
    require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
    require_once MYBB_ADMIN_DIR . 'inc/functions_themes.php';
    find_replace_templatesets('index', '#\{\$dedicationline_block\}#i', '');
    find_replace_templatesets('header', '#<li><a href="misc\.php\?action=dedicationline" class="dedicationline".*?</a></li>#i', '', 0);
    
    $db->delete_query('themestylesheets', "name='dedicationline.css'");
    $query = $db->simple_select('themes', 'tid');
    while ($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid'], false, true);
    }
}

$plugins->add_hook('global_start', 'dedicationline_override_mybb_engine');
$plugins->add_hook('misc_start', 'dedicationline_page');
$plugins->add_hook('global_end', 'dedicationline_build_block');
$plugins->add_hook('global_end', 'dedicationline_autopromote');
$plugins->add_hook('global_end', 'dedicationline_global_cleanup');
$plugins->add_hook('global_end', 'dedicationline_claim_prompt');
$plugins->add_hook('private_messagebit', 'dedicationline_mask_pm_sender');
$plugins->add_hook('member_do_login_end', 'dedicationline_check_guest_claim');


function dedicationline_global_cleanup()
{
    global $db, $mybb, $cache;

    // --- Run only once every 24 hours ---
    $last_cleanup = (int)$cache->read('dedicationline_last_cleanup');
    if ($last_cleanup && TIME_NOW - $last_cleanup < 86400) {
        return;
    }

    // --- Delete expired dedications ---
    $hours = (int)$mybb->settings['dedicationline_autodelete'];
    if ($hours > 0) {
        $cutoff = TIME_NOW - ($hours * 3600);
        $db->delete_query('dedicationline', "expires > 0 AND expires <= {$cutoff}");
    }

    // --- Mark this cleanup as done ---
    $cache->update('dedicationline_last_cleanup', TIME_NOW);
}

function dedicationline_autopromote()
{
    global $db, $mybb;

    $autopromo     = (int)$mybb->settings['dedicationline_autopromo'];
    $display_limit = (int)$mybb->settings['dedicationline_maxdisplay'];
    if (!$autopromo || $display_limit <= 0) return;

    // Count how many dedications are currently approved
    $current_approved = (int)$db->fetch_field(
        $db->simple_select('dedicationline', 'COUNT(id) AS total', 'approved=1'),
        'total'
    );

    $available_slots = max(0, $display_limit - $current_approved);
    if ($available_slots <= 0) return;

    // Promote oldest pending messages into available slots
    $auto_hours = (int)$mybb->settings['dedicationline_autodelete'];
    $expires_value = ($auto_hours > 0) ? (TIME_NOW + ($auto_hours * 3600)) : 0;

    $query = $db->simple_select(
        'dedicationline',
        'id',
        'approved=0',
        ['order_by' => 'dateline', 'order_dir' => 'ASC', 'limit' => $available_slots]
    );

    $ids = [];
    while ($row = $db->fetch_array($query)) {
        $ids[] = (int)$row['id'];
    }

    if (!empty($ids)) {
        $idlist = implode(',', $ids);
        $db->update_query('dedicationline', [
            'approved' => 1,
            'expires'  => $expires_value
        ], "id IN ({$idlist})");
    }
}


// ---------- PM LOGICS ----------
function dedicationline_mask_pm_sender(&$pm)
{
    global $lang;

    // Detect if this PM is from Dedication Line (by subject or pattern)
    if (
        !empty($pm['subject']) &&
        (
            str_contains($pm['subject'], $lang->dedicationline_pm_subject_new) ||
            str_contains($pm['subject'], $lang->dedicationline_pm_subject_status)
        )
    ) {
        // --- If anonymous sender ---
        if (str_contains($pm['fromusername'], $lang->dedicationline_anonymous)) {
            // Replace the linked sender with plain text "Anonymous"
            $pm['fromusername'] = $lang->dedicationline_anonymous;
            $pm['from_link'] = $lang->dedicationline_anonymous;
            $pm['fromuid'] = 0;

            // --- Hide reply link in MyBB's notification / PM bar ---
            if (isset($pm['replylink'])) {
                $pm['replylink'] = ''; // remove reply button
            }
            if (isset($pm['reply_button'])) {
                $pm['reply_button'] = ''; // remove alternative key used in some themes
            }

            // --- Optional: Remove quick reply form if visible in full PM view ---
            if (THIS_SCRIPT == 'private.php' && $pm['pmid'] > 0) {
                global $templates;
                $templates->cache['private_quickreply'] = ''; // disables inline reply box
            }
        }
    }
}

// ---------- PM HELPER ----------
function dedicationline_send_pm($to_uid, $subject, $message, $sender_uid = 0, $is_anonymous = false)
{
    global $mybb, $lang;

    if (!class_exists('PMDataHandler')) {
        require_once MYBB_ROOT . "inc/datahandlers/pm.php";
    }

    $pmhandler = new PMDataHandler("insert");

    // --- Sender setup ---
    $sender_uid  = (int)$sender_uid ?: (int)$mybb->user['uid'];
    $sender_name = $is_anonymous ? $lang->dedicationline_anonymous : htmlspecialchars_uni($mybb->user['username']);

    // --- Build PM data ---
    $pm = [
        'subject'       => $subject,
        'message'       => $message,
        'fromid'        => $is_anonymous ? 0 : $sender_uid, // still use real uid so PM belongs to the correct sender
        'toid'          => (int)$to_uid,
        'options'       => ['savecopy' => 0],
        'ipaddress'     => $mybb->session->ipaddress ?? '127.0.0.1',
        'fromusername'  => $sender_name, // visible name (masked if anonymous)
    ];

    $pmhandler->set_data($pm);
    if ($pmhandler->validate_pm()) {
        $pmhandler->insert_pm();
    }
}


// ---------- FRONTEND DISPLAY ----------
function dedicationline_build_block()
{
    global $db, $templates, $mybb, $dedicationline_block, $lang;
	
	    // ===== DEBUG: Language Load Test =====
    if (method_exists($lang, 'load')) {
        $lang->load('dedicationline', false, true);
    }

    if (!class_exists('postParser')) {
        require_once MYBB_ROOT . "inc/class_parser.php";
    }
    $parser = new postParser();

    if (empty($mybb->settings['dedicationline_enable'])) return;

	$allowed_viewgroups = array_map('intval', explode(',', (string)$mybb->settings['dedicationline_viewgroups']));
	if (!in_array((int)$mybb->user['usergroup'], $allowed_viewgroups)) {
		// hide ticker completely for disallowed viewers
		return;
	}
	$allowed_viewgroups = array_map('intval', explode(',', (string)$mybb->settings['dedicationline_viewgroups']));
	if (!in_array((int)$mybb->user['usergroup'], $allowed_viewgroups)) {
		// hide ticker completely for disallowed viewers
		return;
	}

	$max = (int)$mybb->settings['dedicationline_maxdisplay'];

	if ($max > 0) {
		$query = $db->simple_select('dedicationline', '*', "approved=1", [
			'order_by' => 'dateline',
			'order_dir' => 'DESC',
			'limit'     => $max
		]);
	} else {
		$query = $db->simple_select('dedicationline', '*', "approved=1", [
			'order_by' => 'dateline',
			'order_dir' => 'DESC'
		]);
	}


	$continuous = (int)$mybb->settings['dedicationline_continuous'];
	$speed = max(5, (int)$mybb->settings['dedicationline_speed']);
	$direction = $mybb->settings['dedicationline_scrolldir'];
	$theme = $mybb->settings['dedicationline_theme'];

	$dedicationline_messages = '';

	while ($row = $db->fetch_array($query)) {
		$id = (int)$row['id'];
		$fmt = dl_format_dedication_row($row, $parser);
		extract($fmt);

		// Clean spacing safely
		$msg = preg_replace('/\s+/', ' ', $msg);
		$msg = trim($msg);

		$from = $from_display;

		eval("\$dedicationline_messages .= \"" . $templates->get('dedicationline_message') . "\";");
	
	}
    if (trim($dedicationline_messages) === '') {
        $dedicationline_messages = $lang->dedicationline_no_dedications_yet;
    }
	// --- JS Animation Logic ---
	$dedicationline_js = <<<JS
	document.addEventListener('DOMContentLoaded', function(){
	  const track = document.getElementById('dl-track') || document.querySelector('.dedicationline-inner');
	  if (!track) return;

	  const duration = {$speed} * 1000;
	  const direction = '{$direction}'; // 'left' or 'right'
	  const baseEpoch = new Date('2025-01-01T00:00:00Z').getTime();
	  const scroll = track.closest('.dedicationline-scroll');
	  let isHovered = false;

	  function syncOffset() {
		if (isHovered) return;
		const now = Date.now();
		const offset = (now - baseEpoch) % duration;

		// Convert offset to direction
		if (direction === 'right') {
		  track.style.animationDelay = '-' + ((duration - offset) / 1000) + 's';
		} else {
		  track.style.animationDelay = '-' + (offset / 1000) + 's';
		}
	  }

	  // Initial sync
	  syncOffset();

	  // Pause/resume on visibility
	  document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
		  track.style.animationPlayState = 'paused';
		} else {
		  syncOffset();
		  track.style.animationPlayState = 'running';
		}
	  });

	  // Hover pause
	  if (scroll) {
		scroll.addEventListener('mouseenter', () => {
		  isHovered = true;
		  track.style.animationPlayState = 'paused';
		});
		scroll.addEventListener('mouseleave', () => {
		  isHovered = false;
		  track.style.animationPlayState = 'running';
		});
	  }
	});
	JS;
	if ($continuous) {
		switch ($direction) {
			case 'right':
				$scroll_keyframes = "@keyframes dedication-scroll-continuous { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }";
				break;
			default:
				$scroll_keyframes = "@keyframes dedication-scroll-continuous { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }";
		}
	} else {
		switch ($direction) {
			case 'right':
				$scroll_keyframes = "@keyframes dedication-scroll-fade { 0% { transform: translateX(-100%); opacity:0; } 5% {opacity:1;} 95% {opacity:1;} 100% { transform: translateX(0); opacity:0;} }";
				break;
			default:
				$scroll_keyframes = "@keyframes dedication-scroll-fade { 0% { transform: translateX(0); opacity:0;} 5% {opacity:1;} 95% {opacity:1;} 100% { transform: translateX(-100%); opacity:0;} }";
		}
	}

	// --- Choose template dynamically ---
	if ($continuous) {
		eval("\$dedicationline_block = \"" . $templates->get('dedicationline_continuous') . "\";");
	} else {
		eval("\$dedicationline_block = \"" . $templates->get('dedicationline_noncontinuous') . "\";");
	}

}


// ---------- HELPER: Format one dedication row (sender, recipient, message) ----------
function dl_format_dedication_row($row, $parser)
{
    global $lang, $mybb;

	// --- From user ---
	if ($row['anonymous']) {
		$from_display = $lang->dedicationline_anonymous;

		// Only reveal name in ModCP if setting enabled
		if (defined('IN_MODCP') && (int)$mybb->settings['dedicationline_reveal_anon']) {
			$manage_groups = array_map('intval', explode(',', $mybb->settings['dedicationline_managegroups']));
			$is_mod = is_moderator() || is_super_admin($mybb->user['uid']) || in_array($mybb->user['usergroup'], $manage_groups);

			if ($is_mod) {
				$real_user = get_user($row['uid']);
				if ($real_user && $real_user['uid']) {
					$real_name_link = build_profile_link(
						format_name($real_user['username'], $real_user['usergroup'], $real_user['displaygroup']),
						$real_user['uid']
					);
					$from_display .= " (" . $real_name_link . ")";
				}
			}
		}
	} else {
		$user = get_user($row['uid']);
		if ($user && $user['uid']) {
			$from_display = build_profile_link(
				format_name($user['username'], $user['usergroup'], $user['displaygroup']),
				$user['uid']
			);
		} else {
			$from_display = $lang->dedicationline_guest;
		}
	}

    // --- To users ---
    if (!empty($row['recipient'])) {
        $names = array_map('trim', explode(',', $row['recipient']));
        $linked = [];
        foreach ($names as $name) {
            $u = get_user_by_username($name, ['fields' => '*']);
            $linked[] = ($u && $u['uid'])
                ? build_profile_link(
                    format_name($u['username'], $u['usergroup'], $u['displaygroup']),
                    $u['uid']
                )
                : htmlspecialchars_uni($name);
        }
        $to_display = implode(', ', $linked);
    } else {
        $to_display = $lang->dedicationline_everyone;
    }

    // --- Message ---
    $opts = [
        'allow_html'      => 0,
        'allow_mycode'    => (int)$mybb->settings['dedicationline_allowbbcode'],
        'allow_smilies'   => (int)$mybb->settings['dedicationline_allowsmilies'],
        'nl2br'           => 1,
        'filter_badwords' => 1
    ];
    $msg = $parser->parse_message($row['message'], $opts);

    return [
        'from_display' => $from_display,
        'to_display'   => $to_display,
        'msg'          => $msg
    ];
}

// ---------- THIS IS TO REPLACE "MYBB ENGINE" TEXT TO $lang->dedicationline_system WHEN ANONYMOUS SEND PM ----------
function dedicationline_override_mybb_engine()
{
    global $lang;
    if (!isset($lang->dedicationline_anonymous))
    {
        $lang->load('dedicationline', false, true);
    }
    $lang->mybb_engine = $lang->dedicationline_system;
}

// ---------- DEDICATION SUBMISSION PAGE ----------
function dedicationline_page()
{
    global $lang, $mybb, $db, $templates, $header, $footer, $headerinclude;

	if (!class_exists('postParser')) {
		require_once MYBB_ROOT . "inc/class_parser.php";
	}
	$parser = new postParser();
	
	$maxrecipients = (int)$mybb->settings['dedicationline_maxrec'];
    if ($mybb->get_input('action') != 'dedicationline') return;
	$today_start = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	$ip = $db->escape_string($mybb->session->ipaddress);

	$allowed_postgroups = array_map('intval', explode(',', (string)($mybb->settings['dedicationline_postgroups'] ?? '')));
	$current_group = (int)$mybb->user['usergroup'];

	if (!in_array($current_group, $allowed_postgroups, true)) {
		error($lang->dedicationline_error_no_permission);
	}

	if ($mybb->settings['dedicationline_claimafterlogin'] && $mybb->user['uid'] && $mybb->get_input('claim') == 1) {
		$ip = $db->escape_string($mybb->session->ipaddress);
		$uid = (int)$mybb->user['uid'];

		// Update all guest dedications for this IP
		$db->update_query('dedicationline', ['uid' => $uid], "uid=0 AND ipaddress='{$ip}'");
		redirect('misc.php?action=dedicationline', $lang->dedicationline_claim_success);
	}

    $edit_id = 0;
    $edit_message = '';
	$edit_row = [];
    $edit_request  = (int)$mybb->get_input('edit');
    $delete_request = (int)$mybb->get_input('delete');

    // --- Handle edit/delete actions ---
    if ($mybb->user['uid'] > 0) {
        if ($delete_request) {
            verify_post_check($mybb->get_input('my_post_key'));
            $db->delete_query('dedicationline', "id={$delete_request} AND uid=".(int)$mybb->user['uid']);
            redirect('misc.php?action=dedicationline', $lang->dedicationline_success_deleted);
			exit;
        }

		if ($edit_request) {
			$edit_row = $db->fetch_array(
				$db->simple_select('dedicationline', '*', "id={$edit_request} AND uid=".(int)$mybb->user['uid'])
			);
			if (!$edit_row) {
				redirect('misc.php?action=dedicationline', $lang->dedicationline_error_invalid_id);
				exit;
			}

			$edit_id = $edit_request;
			$edit_message = htmlspecialchars_uni($edit_row['message']);
			$mybb->input['message'] = $edit_message;

			// --- Prefill recipient field when editing ---
			$recipient_value = '';
			if (!empty($edit_row['recipient'])) {
				$recipient_value = htmlspecialchars_uni(trim($edit_row['recipient']));
			}
		}
    } else {
        if ($edit_request || $delete_request) {
            error($lang->dedicationline_error_guest_edit_delete);
        }
    }

    // --- Handle submission ---
	if ($mybb->request_method == 'post') {
		$form_type = $mybb->get_input('form_type');
		if ($form_type === 'user_form') {
			verify_post_check($mybb->get_input('my_post_key'));
			$message = trim($mybb->get_input('message'));
			if (!$message) error($lang->dedicationline_error_no_message);

			$anonymous = ($mybb->settings['dedicationline_allowanon'] && $mybb->get_input('anonymous', MyBB::INPUT_INT)) ? 1 : 0;
			// Only set expiration if auto-delete is enabled AND message is approved
			$requires_approval = (int)$mybb->settings['dedicationline_require_approval'];
			$approved = $requires_approval ? 0 : 1;
			$auto_days = (int)$mybb->settings['dedicationline_autodelete'];
			$expires   = ($approved && $auto_days > 0) ? (TIME_NOW + ($auto_days * 3600)) : 0;

			$recipient_type = $mybb->get_input('recipient_type');
			$recipient_list = '';

			if ($recipient_type == 'specific') {
				$recipients_raw = trim($mybb->get_input('recipient'));
				if (!$recipients_raw) error($lang->dedicationline_error_no_recipient);

				$recipient_names = array_unique(array_filter(array_map('trim', explode(',', $recipients_raw))));
				$maxrecipients = (int)$mybb->settings['dedicationline_maxrec'];
				if ($mybb->settings['dedicationline_multirec'] && count($recipient_names) > $maxrecipients) {
					$error_message = $lang->sprintf($lang->dedicationline_error_too_many_recipients, $maxrecipients);
					error($error_message);
				}

				$valid_names = [];
				foreach ($recipient_names as $rname) {
					$user = get_user_by_username($rname, ['fields' => '*']);
					$valid_names[] = ($user && $user['uid']) ? $user['username'] : $rname;
				}
				$recipient_list = implode(', ', $valid_names);
			}

			// Edit vs new
			$edit_id = (int)$mybb->get_input('edit_id');
			if ($mybb->user['uid'] > 0 && $edit_id > 0) {
				$existing = $db->fetch_array(
					$db->simple_select('dedicationline', 'id,uid', "id={$edit_id} AND uid=".(int)$mybb->user['uid'])
				);
				if (!$existing) error_no_permission();

				$db->update_query('dedicationline', [
					'message'   => $db->escape_string($message),
					'anonymous' => $anonymous,
					'recipient' => $db->escape_string($recipient_list),
					'expires'   => $expires,
					'approved'  => $requires_approval ? 0 : 1
				], "id={$edit_id}");

				redirect('misc.php?action=dedicationline', $lang->dedicationline_success_updated);
				exit;
			} else {
				// --- Anti-flood & Daily Limits (works for both users and guests) ---
				$antiflood = (int)$mybb->settings['dedicationline_antiflood'];
				$dailymax  = (int)$mybb->settings['dedicationline_dailymax'];

				// --- Unified Anti-flood (IP + UID combo) ---
				if ($antiflood > 0) {
					$where_parts = [];
					if ($mybb->user['uid'] > 0) {
						$where_parts[] = "uid=".(int)$mybb->user['uid'];
					}
					$where_parts[] = "ipaddress='{$ip}'";
					$where = implode(' OR ', $where_parts);

					$recent = (int)$db->fetch_field(
						$db->simple_select(
							'dedicationline',
							'dateline',
							"({$where}) ORDER BY dateline DESC LIMIT 1"
						),
						'dateline'
					);

					if ($recent && TIME_NOW - $recent < $antiflood) {
						$wait = $antiflood - (TIME_NOW - $recent);
						$error_message = $lang->sprintf($lang->dedicationline_error_antiflood, $wait);
						error($error_message);
					}
				}

				// --- Daily limit check ---
				if ($dailymax > 0) {
					$where_parts = [];
					if ($mybb->user['uid'] > 0) {
						$where_parts[] = "uid=".(int)$mybb->user['uid'];
					}
					$where_parts[] = "ipaddress='{$ip}'";
					$where = implode(' OR ', $where_parts);

					$count = (int)$db->fetch_field(
						$db->simple_select(
							'dedicationline',
							'COUNT(id) AS total',
							"({$where}) AND dateline > {$today_start}"
						),
						'total'
					);

					if ($count >= $dailymax) {
						$error_message = $lang->sprintf($lang->dedicationline_error_dailymax, $dailymax);
						error($error_message);
					}
					
					// --- Dedication Limit (pending or all) ---
					$maxdedications = (int)$mybb->settings['dedicationline_maxdedications'];
					$capmode        = $mybb->settings['dedicationline_cap_mode'] ?? 'all';

					if ($maxdedications > 0) {
						if ($capmode === 'pending') {
							$where_limit = "(approved=0) AND ({$where})";
						} else {
							$where_limit = "({$where})";
						}

						$dedication_count = (int)$db->fetch_field(
							$db->simple_select(
								'dedicationline',
								'COUNT(id) AS total',
								$where_limit
							),
							'total'
						);

						if ($dedication_count >= $maxdedications) {
							if ($capmode === 'pending') {
								$error_message = $lang->sprintf(
									$lang->dedicationline_error_maxpendingclear,
									$maxdedications
								);
							} else {
								$error_message = $lang->sprintf(
									$lang->dedicationline_error_maxtotalclear,
									$maxdedications
								);
							}
							error($error_message);
						}
					}
				}

				$db->insert_query('dedicationline', [
					'uid'        => (int)$mybb->user['uid'],
					'recipient'  => $db->escape_string($recipient_list),
					'message'    => $db->escape_string($message),
					'dateline'   => TIME_NOW,
					'anonymous'  => $anonymous,
					'expires'    => $expires,
					'approved'   => $approved,
					'ipaddress'  => $db->escape_string($mybb->session->ipaddress)
				]);
				// --- Send PM only if notify is enabled and approval not required ---
				if ($mybb->settings['dedicationline_notify_pm'] && !$requires_approval && !empty($recipient_list)) {
					$recipient_names = array_map('trim', explode(',', $recipient_list));

					$is_anonymous = (bool)$anonymous;
					$sender_uid   = (int)$mybb->user['uid']; // always the real user
					$sender_name  = $is_anonymous ? $lang->dedicationline_anonymous : htmlspecialchars_uni($mybb->user['username']);
							
					foreach ($recipient_names as $rname) {
						$ruser = get_user_by_username($rname, ['fields' => 'uid']);
						if ($ruser && $ruser['uid']) {
							$subject = $lang->sprintf($lang->dedicationline_pm_subject_new, $sender_name);
							$body = $lang->sprintf(
								$lang->dedicationline_pm_body_new,
								$sender_name,
								htmlspecialchars_uni($message)
							);
							dedicationline_send_pm($ruser['uid'], $subject, $body, $sender_uid, $is_anonymous);
						}
					}
				}
				$msg = $requires_approval ? $lang->dedicationline_success_submitted : $lang->dedicationline_success_added;
				redirect('misc.php?action=dedicationline', $msg);
				exit;
			}
		}
	}
	// --- Unified daily counter (UID + IP combo) ---
	$count = 0;
	$dailymax = (int)$mybb->settings['dedicationline_dailymax'];
	$remaining = $lang->dedicationline_counter_unlimited;

	// Safe IP fallback
	$ip = $mybb->session->ipaddress ?? get_ip();
	if (empty($ip)) {
		$ip = get_ip();
	}
	$ip = $db->escape_string($ip);

	// Combine UID + IP logic
	$where_parts = [];
	if ($mybb->user['uid'] > 0) {
		$where_parts[] = "uid=".(int)$mybb->user['uid'];
	}
	$where_parts[] = "ipaddress='{$ip}'";
	$where = implode(' OR ', $where_parts);

	if ($dailymax > 0) {
		$count = (int)$db->fetch_field(
			$db->simple_select(
				'dedicationline',
				'COUNT(id) AS count',
				"({$where}) AND dateline > ".(TIME_NOW - 86400)
			),
			'count'
		);
		$remaining = max(0, $dailymax - $count);
	}

	// --- Decide which counter text to show (unified for all groups) ---
	$group_id = (int)$mybb->user['usergroup'];
	$allowed_postgroups = array_map('intval', explode(',', (string)$mybb->settings['dedicationline_postgroups']));

	if (in_array($group_id, $allowed_postgroups, true)) {
		// Group is allowed to post (includes guests if group 1 is allowed)
		$dedicationline_counter_html = $lang->sprintf(
			$lang->dedicationline_counter_user,
			$count,
			$dailymax,
			$remaining
		);
	} else {
		// Not allowed to post — no counter displayed
		$dedicationline_counter_html = '';
	}

	// Always append shared quota notice since limits are based on IP+UID
	$dedicationline_counter_html .= $lang->dedicationline_counter_shared_notice;

	// --- Build user messages ---
	$dedicationline_userrows = '';
	if ($mybb->user['uid']) {
		$query = $db->simple_select('dedicationline', '*', 'uid='.(int)$mybb->user['uid'], ['order_by' => 'dateline', 'order_dir' => 'ASC']);
		while ($row = $db->fetch_array($query)) {
			$fmt = dl_format_dedication_row($row, $parser);
			extract($fmt);

			$date = my_date('relative', $row['dateline']);
			if ($row['approved']) {
				$status = $lang->dedicationline_approved;
			} else {
				$status = $lang->dedicationline_pending;

				// Show queue position if autopromo is enabled
				if (!empty($mybb->settings['dedicationline_autopromo'])) {
					$position = (int)$db->fetch_field(
						$db->simple_select(
							'dedicationline',
							'COUNT(id) AS pos',
							"approved=0 AND dateline < {$row['dateline']}"
						),
						'pos'
					) + 1;
					$status .= " (#{$position})";
				}
			}

			$edit_url = "misc.php?action=dedicationline&edit={$row['id']}";
			$delete_url = "misc.php?action=dedicationline&delete={$row['id']}&my_post_key={$mybb->post_code}";

			// NEW VARIABLES (matching your updated columns)
			if ($row['anonymous']) {
				$from_display = $lang->dedicationline_anonymous;
			} else {
				$from_user = get_user($row['uid']);
				if ($from_user && $from_user['uid']) {
					$from_display = build_profile_link(
						format_name($from_user['username'], $from_user['usergroup'], $from_user['displaygroup']),
						$from_user['uid']
					);
				} else {
					$from_display = $lang->dedicationline_guest;
				}
			}

			if (!empty($row['recipient'])) {
				$names = array_map('trim', explode(',', $row['recipient']));
				$linked = [];

				foreach ($names as $name) {
					$u = get_user_by_username($name, ['fields' => '*']);
					if ($u && $u['uid']) {
						$linked[] = build_profile_link(
							format_name($u['username'], $u['usergroup'], $u['displaygroup']),
							$u['uid']
						);
					} else {
						$linked[] = htmlspecialchars_uni($name);
					}
				}

				$to_display = implode(', ', $linked);
			} else {
				$to_display = $lang->dedicationline_everyone;
			}

			$expires = $row['expires']
				? my_date('relative', $row['expires'])
				: $lang->dedicationline_never;

			// Edit highlight + edit indicator setup
			$editing_class = '';
			$editing_label = '';
			$edit_label = $lang->dedicationline_edit;

			if ($edit_id == $row['id']) {
				$editing_class = 'dedicationline-editing';
				$editing_label = $lang->dedicationline_editing_label;
				$edit_label = $lang->dedicationline_cancel;
				$edit_url = "misc.php?action=dedicationline";
			}

			eval("\$dedicationline_userrows .= \"" . $templates->get('dedicationline_user_row') . "\";");
		}


		if ($dedicationline_userrows === '') {
			// See if user has posted today
			$count_today = (int)$db->fetch_field(
				$db->simple_select(
					'dedicationline',
					'COUNT(id) AS count',
					'uid='.(int)$mybb->user['uid'].' AND dateline > '.$today_start
				),
				'count'
			);

			if ($count_today === 0) {
				$dedicationline_userrows = "<tr><td class='trow1' colspan='7' style='text-align:center;'>{$lang->dedicationline_no_dedications_today}</td></tr>";
			} else {
				$dedicationline_userrows = "<tr><td class='trow1' colspan='7' style='text-align:center;'>{$lang->dedicationline_no_dedications_found}</td></tr>";
			}
		}
	} else {
		$dedicationline_userrows = "<tr><td class='trow1' colspan='7' style='text-align:center;'>{$lang->dedicationline_must_login_view}</td></tr>";
	}

	eval("\$user_messages_html = \"" . $templates->get('dedicationline_user_messages') . "\";");


	// --- Restore previous recipient field value ---
	$recipient_value = '';
	if ($mybb->request_method == 'post') {
		$recipient_value = trim((string)$mybb->get_input('recipient'));
	} elseif ($edit_id > 0 && isset($edit_row) && !empty($edit_row['recipient'])) {
		$recipient_value = trim((string)$edit_row['recipient']);
	}

	$recipient_value = htmlspecialchars_uni($recipient_value);
	
	$maxrecipients = (int)$mybb->settings['dedicationline_maxrec'];
	$max_recipient_notice = '';

	if ($mybb->settings['dedicationline_multirec'] && $maxrecipients > 0) {
		$max_recipient_notice = $lang->sprintf($lang->dedicationline_max_rec_notice, $maxrecipients);
}
	// --- Moderator panel setup ---
	$dedicationline_modpanel_html = '';
	$dedicationline_manage_button = '';
	$dedicationline_modrows = '';

	$manage_groups = array_map('intval', explode(',', $mybb->settings['dedicationline_managegroups']));
	$can_moderate = is_moderator() || is_super_admin($mybb->user['uid']) || in_array($mybb->user['usergroup'], $manage_groups);

	if ($can_moderate) {

		// --- Bulk moderator actions ---
		if ($mybb->request_method == 'post' && !empty($mybb->input['modaction'])) {
			verify_post_check($mybb->get_input('my_post_key'));
			$checked = $mybb->get_input('checked', MyBB::INPUT_ARRAY);

			if (!empty($checked)) {
				$ids = array_map('intval', $checked);
				$idlist = implode(',', $ids);

				switch ($mybb->get_input('modaction')) {
					case 'approve':
						$auto_days = (int)$mybb->settings['dedicationline_autodelete'];
						$expires_value = ($auto_days > 0) ? (TIME_NOW + ($auto_days * 3600)) : 0;
						
						// --- Display limit enforcement ---
						$display_limit = (int)$mybb->settings['dedicationline_maxdisplay'];
						if ($display_limit > 0) {
							$current_approved = (int)$db->fetch_field(
								$db->simple_select('dedicationline', 'COUNT(id) AS total', 'approved=1'),
								'total'
							);
							$available_slots = max(0, $display_limit - $current_approved);

							if ($available_slots <= 0) {
								error($lang->sprintf($lang->dedicationline_error_no_slots, $display_limit));
							}

							if (count($ids) > $available_slots) {
								error($lang->sprintf($lang->dedicationline_error_not_enough_slots, $available_slots, $display_limit));
							}
						}

						// Approve and assign expiration
						$db->update_query(
							'dedicationline',
							[
								'approved' => 1,
								'expires'  => $expires_value
							],
							"id IN ({$idlist})"
						);

						// Notify recipients using the the *original sender* name (or system if anonymous)
						if ($mybb->settings['dedicationline_notify_pm']) {
							$query_pm = $db->simple_select('dedicationline', '*', "id IN ({$idlist})");
							while ($row = $db->fetch_array($query_pm)) {
								if (empty($row['recipient'])) {
									continue;
								}

								$is_anonymous = (bool)$row['anonymous'];
								$sender_uid   = $is_anonymous ? 0 : (int)$row['uid'];
								$sender_name = $lang->dedicationline_anonymous;
								if (!$is_anonymous && $sender_uid > 0) {
									$sender_user = get_user($sender_uid);
									if ($sender_user && $sender_user['uid']) {
										$sender_name = htmlspecialchars_uni($sender_user['username']);
									}
								}

								$recipient_names = array_map('trim', explode(',', $row['recipient']));
								foreach ($recipient_names as $rname) {
									$ruser = get_user_by_username($rname, ['fields' => 'uid']);
									if ($ruser && $ruser['uid']) {
										$subject = $lang->sprintf($lang->dedicationline_pm_subject_new, $sender_name);
										$body = $lang->sprintf(
											$lang->dedicationline_pm_body_new,
											$sender_name,
											htmlspecialchars_uni($row['message'])
										);
										dedicationline_send_pm(
											$ruser['uid'],
											$subject,
											$body,
											$sender_uid,
											$is_anonymous // Ensures "fromid=0" and "fromname=Anonymous"
										);
									}
								}
							}
						}
						redirect('misc.php?action=dedicationline', $lang->dedicationline_mod_approved);
						break;

						case 'unapprove':
							$db->update_query('dedicationline', [
								'approved' => 0,
								'expires'  => 0
							], "id IN ({$idlist})");

							// Notify senders via System PM
							if ($mybb->settings['dedicationline_notify_pm']) {
								$query = $db->simple_select('dedicationline', 'uid', "id IN ({$idlist})");
								while ($r = $db->fetch_array($query)) {
									$to_uid = (int)$r['uid'];
									$subject = $lang->dedicationline_pm_subject_status;
									$body    = $lang->dedicationline_pm_body_unapproved;
									dedicationline_send_pm($to_uid, $subject, $body, 0, false);
								}
							}

							redirect('misc.php?action=dedicationline', $lang->dedicationline_mod_unapproved);
							break;

					case 'delete':
						$db->delete_query('dedicationline', "id IN ({$idlist})");
						redirect('misc.php?action=dedicationline', $lang->dedicationline_mod_deleted);
						break;
				}
			} else {
				redirect('misc.php?action=dedicationline', $lang->dedicationline_mod_none);
			}
		}

		// --- Build moderator table rows with filter + pagination ---
		if (!defined('IN_MODCP')) {
			define('IN_MODCP', true);
		}

		$status_filter = $mybb->get_input('status');
		$where_filter = '1=1';
		switch ($status_filter) {
			case 'pending':
				$where_filter = 'approved=0';
				break;
			case 'approved':
				$where_filter = 'approved=1';
				break;
		}

		$per_page = (int)$mybb->get_input('perpage', MyBB::INPUT_INT);
		if ($per_page <= 0) {
			$per_page = 20; // default
		}
		$page = max(1, (int)$mybb->get_input('page', MyBB::INPUT_INT));
		$start = ($page - 1) * $per_page;

		// Count total for pagination
		$total = (int)$db->fetch_field(
			$db->simple_select('dedicationline', 'COUNT(id) AS total', $where_filter),
			'total'
		);

		$multipage = multipage($total, $per_page, $page, "misc.php?action=dedicationline&status={$status_filter}&perpage={$per_page}");

		// --- Filter + PerPage controls (displayed above the table) ---
		$sel_all      = ($status_filter == 'all' || !$status_filter) ? 'selected' : '';
		$sel_pending  = ($status_filter == 'pending') ? 'selected' : '';
		$sel_approved = ($status_filter == 'approved') ? 'selected' : '';

		$sel10  = ($per_page == 10) ? 'selected' : '';
		$sel20  = ($per_page == 20) ? 'selected' : '';
		$sel50  = ($per_page == 50) ? 'selected' : '';

		eval("\$filter_html = \"" . $templates->get('dedicationline_modpanel_filter') . "\";");

		$mod_rows = '';
		$query = $db->simple_select('dedicationline', '*', $where_filter, [
			'order_by' => 'dateline',
			'order_dir' => 'ASC',
			'limit_start' => $start,
			'limit' => $per_page
		]);

		while ($row = $db->fetch_array($query)) {
			$fmt = dl_format_dedication_row($row, $parser);
			extract($fmt);

			$id   = (int)$row['id'];
			$date = my_date('relative', $row['dateline']);
			$expires = $row['expires'] ? my_date('relative', $row['expires']) : $lang->dedicationline_never;
			if ($row['approved']) {
				$status = $lang->dedicationline_approved;
			} else {
				$status = $lang->dedicationline_pending;

				// Show queue position if autopromo is enabled
				if (!empty($mybb->settings['dedicationline_autopromo'])) {
					$position = (int)$db->fetch_field(
						$db->simple_select(
							'dedicationline',
							'COUNT(id) AS pos',
							"approved=0 AND dateline < {$row['dateline']}"
						),
						'pos'
					) + 1;
					$status .= " (#{$position})";
				}
			}

			eval("\$mod_rows .= \"" . $templates->get('dedicationline_modpanel_row') . "\";");
		}

		if (!$mod_rows) {
			$mod_rows = "<tr><td class='trow1' colspan='7' style='text-align:center;'>{$lang->dedicationline_no_dedications_found}</td></tr>";
		}

		// --- Display slot info for moderators ---
		$display_limit = (int)$mybb->settings['dedicationline_maxdisplay'];
		if ($display_limit > 0) {
			$current_approved = (int)$db->fetch_field(
				$db->simple_select('dedicationline', 'COUNT(id) AS total', 'approved=1'),
				'total'
			);
			$available_slots = max(0, $display_limit - $current_approved);
			$slots_available_text = $lang->sprintf($lang->dedicationline_slots_available, $available_slots, $display_limit);
		} else {
			$slots_available_text = $lang->dedicationline_unlimited_slots;
		}

		// --- Combine pagination, filter and rows ---
		if (!empty($multipage)) {
			eval("\$pagination_row = \"" . $templates->get('dedicationline_modpanel_pagination') . "\";");
		} else {
			$pagination_row = '';
		}

		$dedicationline_modrows = $mod_rows . $pagination_row;
		$dedicationline_manage_button = $lang->dedicationline_manage;

		// --- Merge filter + table in final template ---
		$dedicationline_modpanel_html = $filter_html;
		eval("\$dedicationline_modpanel_html .= \"" . $templates->get('dedicationline_modpanel') . "\";");

	}

	// --- Render final page ---
	$button_text = ($edit_id > 0) ? $lang->dedicationline_update : $lang->dedicationline_send;
	$editing_notice = ($edit_id > 0)
		? $lang->dedicationline_editing_notice
		: "";

	// --- Anonymous checkbox and counter layout ---
	$anon_checkbox = '';
	$counter_colspan = 2;

	if (!empty($mybb->settings['dedicationline_allowanon'])) {
		// Only build it when allowed
		eval("\$anon_checkbox = \"" . $templates->get('dedicationline_anon_checkbox') . "\";");
		$counter_colspan = 1;
	}
	
	eval("\$dedicationline_form = \"" . $templates->get('dedicationline_form') . "\";");
	eval("\$dedicationline_page = \"" . $templates->get('dedicationline_main') . "\";");
	output_page($dedicationline_page);
}

// ---------- GUEST CLAIMING DEDICATIONS AFTER SIGN UP WITH NEW ACCOUNT/SIGN IN ----------
function dedicationline_check_guest_claim()
{
    global $mybb, $db;
    $uid = (int)$mybb->user['uid'];
    $ip  = $db->escape_string($mybb->session->ipaddress);
    $cutoff = TIME_NOW - 3600;

    $count = $db->fetch_field(
        $db->simple_select('dedicationline','COUNT(id) AS total',"uid=0 AND ipaddress='{$ip}' AND dateline>{$cutoff}"),
        'total'
    );

    if ($count > 0) {
        $_SESSION['dedicationline_claimable'] = $count;
    }
}

// ---------- GUEST CLAIM PROMPT ----------

function dedicationline_claim_prompt()
{
    global $mybb, $db, $lang, $templates;

    if (method_exists($lang, 'load')) {
        $lang->load('dedicationline');
    }

    // Only show when logged in (we show the banner to the user who can claim)
    if (!$mybb->user['uid'] || !$mybb->settings['dedicationline_claimafterlogin']) {
        return;
    }

    $ip = $db->escape_string($mybb->session->ipaddress ?: get_ip());
    $hours = (int)$mybb->settings['dedicationline_autodelete'];
    $expire_cutoff = $hours > 0 ? TIME_NOW - ($hours * 3600) : 0;

    // Count guest dedications from this IP that are still valid
    $where = "uid=0 AND ipaddress='{$ip}'";
    if ($expire_cutoff > 0) {
        $where .= " AND (expires = 0 OR expires > " . TIME_NOW . ")";
    }

    $count = (int)$db->fetch_field(
        $db->simple_select('dedicationline', 'COUNT(id) AS total', $where),
        'total'
    );

    // --- Only proceed if feature is on and count > 0 ---
    if ($mybb->settings['dedicationline_claimafterlogin'] && $mybb->user['uid'] > 0 && $count > 0) {
        $claim_url = 'misc.php?action=dedicationline&claim=1';
        eval("\$banner_html = \"" . $templates->get('dedicationline_claim_banner') . "\";");
        $GLOBALS['header'] .= $banner_html;
    }
}

?>