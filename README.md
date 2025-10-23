# 💌 Dedication Line (MyBB 1.8.x)

**Version:** 1.0  
**Author:** [JLP423](https://mybb.vn)  
**License:** MIT  
**Compatibility:** MyBB 1.8.x (tested on PHP 8.4+)

---

## 🌟 Overview

**Dedication Line** lets your forum members post short, personal dedication messages that appear globally as a scrolling ticker.  
It adds a warm, community-driven touch to your forum while moderators maintain full control through a built-in management panel.

Admins can easily move the ticker anywhere by placing this code in any template:
{$dedicationline_block}


For example, you can add it to the `index`, `header`, or even a custom portal template.

---

## ✨ Features

- 🎞️ Scrolling ticker bar with six built-in color themes  
- 🔁 Continuous or non-continuous scroll animations  
- 🕵️ Anonymous messages with optional moderator visibility  
- 📬 Private-message notifications when dedications are sent or approved  
- 🧩 Integrated ModCP panel with bulk actions (approve, unapprove, delete)  
- 📑 Pagination and filters for pending or approved lists  
- ⚙️ Instant auto-promotion of queued messages into open display slots  
- 🧹 Daily cleanup of expired dedications (runs automatically once per 24 hours)  
- 🕒 Anti-flood and daily posting limits (per user and per IP)  
- 🙋 Guest dedications with claim-after-login support  
- 🎨 Automatic template + CSS installation for all themes  

---

## 🛠️ Installation

1. Upload everything inside the **`UPLOAD/`** folder to your MyBB root directory.  
2. In the **Admin CP → Plugins**, install and activate **“Dedication Line.”**  
3. Configure options under **Configuration → Dedication Line Settings.**  
4. The ticker appears automatically above the forum list.  
   - To move it elsewhere, insert `{$dedicationline_block}` into any template.

---

## ⚙️ Configuration Highlights

- Maximum displayed messages and scroll speed  
- Direction (left / right) and continuous-mode toggle  
- Auto-promotion and auto-deletion intervals  
- PM notifications and anonymous-posting control  
- Group permissions and moderator access  
- Anti-flood interval and daily user limits  

---

## 🧱 Technical Notes

- Fully compatible with **PHP 8.4+**  
- Uses **InnoDB**, **utf8mb4**, and indexed columns for performance  
- Cleanup runs automatically once per day (throttled)  
- Templates install globally (`sid = -2`)  
- Clean uninstall removes tables, templates, and settings  

---

## 🎨 Included Themes

- **Classic Blue** – calm and clean  
- **Love** – pink / red gradient  
- **Shoutout** – warm amber tone  
- **Coolwave** – cyan / purple blend  
- **Sunset** – soft coral look  
- **Dark** – minimal and modern  

---

## 👤 Author

**JLP423**  
🌐 [https://mybb.vn](https://mybb.vn)  
📧 support@mybb.vn
