# Oshi Tier Maker

**Version 0.4.0rc1**

สร้าง tier list รูปภาพสไตล์ idol fandom ออกมาเป็นไฟล์ PNG — ออกแบบมาโดยเน้น **ความเป็นส่วนตัวของผู้ใช้เป็นหลัก**

### 🔒 Privacy First

**Static version** (`cf/`) — รูปและข้อมูลทั้งหมดอยู่ในเบราว์เซอร์ของคุณเท่านั้น ไม่มีการส่งข้อมูลใดๆ ออกไปยัง server ภายนอก การสร้างภาพ PNG ทำในเบราว์เซอร์ทั้งหมด ไม่มี backend ไม่มี analytics ไม่มี tracking

**PHP version** (`php/`) — รูปที่อัพโหลดถูกเก็บบน server ที่คุณควบคุมเองเท่านั้น ไม่ผ่านบริการภายนอก ข้อมูล tier และ layout ทั้งหมดเก็บใน browser ของผู้ใช้ผ่าน localStorage

---

## Versions

| | PHP Version | Static Version |
|---|---|---|
| **Directory** | `php/` | `cf/` |
| **Entry point** | `php/index.php` | `cf/index.html` |
| **Upload storage** | Server filesystem (`uploads/`) | IndexedDB (browser) |
| **Image generation** | Canvas API (client-side) | Canvas API (client-side) |
| **Server required** | PHP 8.2 | ไม่ต้องมี server |
| **Cross-device** | ✅ ผ่าน server หรือ ZIP | ZIP เท่านั้น |
| **Storage limit** | Disk space ของ server | ~50 MB ต่อ browser |

---

## Features (ทั้งสอง version)

- อัพโหลดรูปได้หลายไฟล์พร้อมกัน (คลิกหรือ drag & drop)
- **Tier แบบกำหนดเอง** — เพิ่ม / ลบ tier ได้ไม่จำกัด
- **แก้ไขชื่อ tier** — คลิกที่ชื่อเพื่อแก้ inline
- **เปลี่ยนสี tier** — คลิกพื้นหลัง label เลือกได้ 8 สี
- ลากรูปจัดใน tier ได้อิสระ ย้ายข้าม tier ได้
- เลือกจำนวน **รูปต่อแถว** (3–8) ก่อนสร้างภาพ
- สร้างไฟล์ **PNG** ด้วย Canvas API (client-side) — cover-crop อัตโนมัติ
- **Auto-save** ลงใน `localStorage` ทุกครั้งที่มีการเปลี่ยนแปลง
- **Auto-restore** เมื่อเปิดหน้าใหม่ รวมถึงชื่อ tier, สี, และตำแหน่งรูป
- **Export ZIP** — สำรองรูป + state ทั้งหมดเป็นไฟล์ .zip
- **Import ZIP** — โหลดข้อมูลกลับมา (รองรับย้ายข้ามเครื่อง)
- **คู่มือการใช้งาน in-app** — banner คลิกได้ที่ด้านบนหน้า เปิด modal อธิบายทุก feature

---

## Requirements

### PHP Version
| Component | Version |
|---|---|
| PHP | 8.2+ |
| Web server | Apache / Nginx / Caddy / `php -S` |

### Static Version (CF)
- Modern browser: Chrome 90+ / Firefox 88+ / Safari 15+ / Edge 90+
- ต้องการ HTTPS หรือ `localhost` — IndexedDB ถูก block บน `file://` ใน Chrome
- ไม่ต้องมี server หรือ backend ใดๆ

---

## Installation

### PHP Version

#### Apache (XAMPP / WAMP / LAMP)

1. copy โฟลเดอร์ `php/` ไปไว้ใน document root

   ```
   # XAMPP (Windows)
   C:\xampp\htdocs\oshi-tier\

   # XAMPP (macOS)
   /Applications/XAMPP/htdocs/oshi-tier/

   # Ubuntu / Debian
   /var/www/html/oshi-tier/
   ```

2. ตั้ง permission ของ `uploads/`

   ```bash
   # Linux / macOS
   chmod 755 php/uploads/
   # ถ้ายังเขียนไม่ได้
   chmod 777 php/uploads/
   ```

   Windows (XAMPP): ไม่ต้องตั้ง permission — เขียนได้เลย

3. เปิด `http://localhost/oshi-tier/`

#### Nginx

1. copy โฟลเดอร์ `php/` ไปไว้ที่ต้องการ เช่น `/var/www/oshi-tier/`

2. เพิ่ม server block:

   ```nginx
   server {
       listen 80;
       server_name oshi-tier.example.com;
       root /var/www/oshi-tier;
       index index.php;

       location / { try_files $uri $uri/ =404; }

       location ~ \.php$ {
           fastcgi_pass unix:/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location /uploads/ {
           location ~ \.php$ { deny all; }
       }
   }
   ```

3. ตั้ง permission และ restart:

   ```bash
   chmod 755 /var/www/oshi-tier/uploads/
   sudo nginx -t && sudo systemctl reload nginx
   ```

#### PHP Built-in Server (development)

```bash
cd php/
php -S localhost:8080
```

เปิด `http://localhost:8080` — ไม่ควรใช้ใน production

---

### Static Version (CF)

ไฟล์เดียว (`cf/index.html`) วางได้บน static hosting ใดก็ได้

#### Cloudflare Pages
```bash
npm install -g wrangler
wrangler login
wrangler pages deploy cf/ --project-name=oshi-tier
```

#### GitHub Pages
```bash
git init && git add cf/
git commit -m "initial commit"
git remote add origin https://github.com/<user>/<repo>.git
git push -u origin main
```
จากนั้นไปที่ **Settings → Pages → Source** เลือก branch `main` โฟลเดอร์ `/cf`

#### Netlify
```bash
# CLI
npm install -g netlify-cli
netlify deploy --dir=cf/ --prod
```
หรือ Drag & Drop โฟลเดอร์ `cf/` ที่ [app.netlify.com](https://app.netlify.com)

#### Vercel
```bash
npm install -g vercel
vercel cf/
```

#### VPS / Shared Hosting
```bash
scp cf/index.html user@server:/var/www/html/oshi-tier/
# หรืออัพโหลดผ่าน FTP ไปที่ public_html/oshi-tier/
```

#### Local Development
```bash
php -S localhost:8080 -t cf/   # PHP
python -m http.server 8080 --directory cf/   # Python 3
npx serve cf/                  # Node.js
```

> ⚠️ **อย่าเปิดด้วย `file://` ใน Chrome** — IndexedDB จะถูก block
> Firefox รองรับ `file://` ได้ปกติ

---

### ทดสอบหลังติดตั้ง

**PHP Version**
- [ ] เปิดหน้าแสดงผลได้ ไม่มี error
- [ ] อัพโหลดรูปได้ ไม่มี error "บันทึกไฟล์ไม่สำเร็จ"
- [ ] รูปที่อัพโหลดปรากฏใน `php/uploads/`
- [ ] ลากรูปลง tier และสร้างภาพ PNG ได้
- [ ] Export ZIP และ Import กลับมาครบ
- [ ] รีเฟรชหน้า — state restore กลับมาครบ

**Static Version (CF)**
- [ ] เปิดหน้าแสดงผลได้
- [ ] อัพโหลดรูปได้ — progress bar storage อัพเดท
- [ ] ลากรูปลง tier และสร้างภาพ PNG ได้
- [ ] Export ZIP และ Import กลับมาครบ
- [ ] รีเฟรชหน้า — รูปโหลดกลับมาจาก browser storage

---

## Project Structure

```
oshi-tier/               ← project root
├── php/                 ← PHP version
│   ├── index.php        หน้าหลัก UI + Canvas generation
│   ├── upload.php       รับ AJAX upload → server filesystem
│   └── uploads/
│       └── .htaccess    กัน PHP execution และ directory listing
├── cf/                  ← Static version (ไม่ต้องมี server)
│   └── index.html       หน้าเดียว ครบทุกอย่าง
├── CLAUDE.md
└── README.md
```

---

## Usage

1. **อัพโหลดรูป** — คลิกหรือลากไฟล์มาวางที่ช่อง upload
2. **จัด tier** — ลากรูปจาก pool ลงใน tier ที่ต้องการ
3. **จัดการ tier**
   - คลิกชื่อ tier → แก้ไข inline → กด Enter หรือคลิกออก
   - คลิกพื้นหลัง (สีทึบ) → เลือกสีจาก color picker
   - ปุ่ม × มุมบนขวา (แสดงเมื่อ hover) → ลบ tier
   - ปุ่ม **＋ เพิ่ม tier** → สร้าง tier ใหม่
4. **เลือกรูปต่อแถว** — เลือก 3–8
5. กด **สร้างภาพ PNG** → ดาวน์โหลด `oshi-tier.png` ทันที

### Export / Import ZIP
- **Export** — บันทึกรูปทั้งหมด + tier config เป็นไฟล์ `.zip`
- **Import** — โหลด `.zip` กลับมา (รองรับไฟล์จากทั้งสอง version)
- ใช้ Import/Export เพื่อย้ายข้อมูลข้ามเครื่องหรือระหว่าง version

---

## Storage Details

### PHP Version
- รูปถูก **บันทึกบนเซิร์ฟเวอร์** ใน `uploads/` ด้วยชื่อ `hex(random_bytes(16))`
- `localStorage` เก็บเฉพาะ metadata (tier config, filenames, perRow)
- Export ZIP จะ fetch รูปจาก server มาแพ็คใน browser

### Static Version (CF)
- รูปถูก **บันทึกใน IndexedDB** ของเบราว์เซอร์เท่านั้น
- ไม่มีข้อมูลใดส่งออกไปยัง server ภายนอก
- Quota สูงสุด 50 MB (แสดง progress bar พร้อมเตือนเมื่อใกล้เต็ม)

---

## localStorage & IndexedDB Caveats

### localStorage (ทั้งสอง version)

localStorage ใช้เก็บ **metadata เท่านั้น** — ชื่อ tier, สี, ลำดับรูป, perRow — ไม่เก็บ binary ใดๆ

| สถานการณ์ | ผลที่เกิด |
|---|---|
| ล้าง site data / Clear cookies | tier config หาย แต่รูปยังอยู่ (PHP: บน server, CF: ใน IndexedDB) |
| Incognito / Private mode | localStorage ทำงานได้ แต่ข้อมูลหายทันทีที่ปิด window |
| เปิดสองแท็บพร้อมกัน | แต่ละแท็บ share state เดียวกัน — บันทึกทับกันได้ถ้าแก้พร้อมกัน |
| เปลี่ยน domain / port | localStorage แยกกัน — state ไม่ข้ามกัน |

### IndexedDB (Static version เท่านั้น)

IndexedDB ใช้เก็บ **image blobs** ทั้งหมด — เป็นหัวใจหลักของ static version

| สถานการณ์ | ผลที่เกิด |
|---|---|
| รีเฟรช / ปิด-เปิดแท็บ | รูปยังอยู่ — โหลดกลับมาอัตโนมัติ ✅ |
| Incognito / Private mode | IndexedDB ทำงาน แต่**รูปหายทั้งหมดเมื่อปิด window** ⚠️ |
| ล้าง site data / Clear cache | รูปหายทั้งหมด — ไม่สามารถกู้คืนได้ ⚠️ |
| เปิดด้วย `file://` (Chrome) | IndexedDB ถูก block — รูปไม่ถูกบันทึก ❌ |
| เปิดด้วย `file://` (Firefox) | ทำงานได้ปกติ ✅ |
| Safari (ITP) | IndexedDB ทำงานได้ แต่ quota อาจถูกจำกัดและล้างอัตโนมัติถ้าไม่ได้ใช้นานกว่า 7 วัน ⚠️ |
| Storage เต็ม (> 50 MB) | แจ้งเตือนและบล็อกการเพิ่มรูปใหม่ |

> **คำแนะนำ**: ใช้ **Export ZIP เป็นประจำ** โดยเฉพาะก่อนล้าง browser data หรือถ้าใช้บน Safari — รูปที่อยู่ในเบราว์เซอร์เท่านั้นไม่มี backup อัตโนมัติ

---

## ZIP Schema

```
oshi-tier-export.zip
├── state.json     tier config + file ID mappings
└── images/
    ├── <uuid>.jpg
    ├── <uuid>.png
    └── ...
```

```json
{
  "tiers": [
    { "id": "kami", "name": "Kami", "color": "#F08080", "files": ["abc.jpg"] },
    { "id": "oshi", "name": "Oshi", "color": "#FFB347", "files": ["def.png"] }
  ],
  "pool": ["ghi.webp"],
  "perRow": 6,
  "savedAt": 1750000000000
}
```

**PHP version import**: re-uploads แต่ละรูปผ่าน `upload.php` → ได้ filename ใหม่ → remap references ใน state อัตโนมัติ

**CF version import**: restore blob ลง IndexedDB โดยตรง — filename/ID คงเดิม ไม่ต้อง remap

---

## Generated Image Spec

| Property | Value |
|---|---|
| Format | PNG |
| Photo size | 110 × 145 px (cover-crop) |
| Label width | 140 px |
| Gap | 4 px |
| Canvas width | `140 + perRow × 110 + (perRow+1) × 4` px |
| Tier color | กำหนดจาก UI — 8 สีให้เลือก |
| Font | System font stack (Segoe UI / Noto Sans Thai / Arial) |
| Generation | Browser Canvas API — ไม่ต้องพึ่ง server |

---

## Security (PHP Version)

- ตรวจ MIME type จริงด้วย `finfo` (ไม่เชื่อ extension ที่ส่งมา)
- บันทึกชื่อไฟล์เป็น `hex(random_bytes(16))` ไม่ใช้ชื่อเดิม
- `basename()` กัน path traversal ทุก endpoint
- `uploads/.htaccess` บล็อก PHP execution และ directory listing
- จำกัดขนาดไฟล์ 10 MB / ไฟล์

---

## Troubleshooting

### PHP Version

**อัพโหลดไม่ได้ — "บันทึกไฟล์ไม่สำเร็จ"**
```bash
ls -la php/uploads/
chmod 755 php/uploads/
mkdir -p php/uploads/   # ถ้า folder ไม่มี
```

**รูปไม่แสดงหลัง upload**
- ตรวจ browser console สำหรับ 404 error
- ตรวจ web server ว่า serve ไฟล์จาก `uploads/` ได้

**ภาพ PNG เป็นสีพื้น ไม่มีรูป**
- อาจเกิดจาก CORS ถ้า `uploads/` อยู่คนละ origin — ในกรณีปกติ (same origin) ไม่มีปัญหา

---

### Static Version (CF)

**รูปหายหลัง refresh**
- ตรวจสอบว่าเปิดผ่าน `http://` ไม่ใช่ `file://` (Chrome block IndexedDB บน file://)
- ตรวจสอบว่าไม่ได้อยู่ใน Incognito / Private mode
- ตรวจ browser console: `indexedDB` ต้องไม่ undefined

**Storage เต็ม / รูปเพิ่มไม่ได้**
1. กด Export ZIP เพื่อสำรองข้อมูลก่อน
2. กด "ล้างทั้งหมด" เพื่อล้าง storage
3. Import ZIP กลับมาใหม่

**Export ZIP ว่างเปล่า / ไม่มีรูป**
- ตรวจสอบว่ารูปถูก restore จาก browser storage แล้ว (ไม่ใช่แค่แสดง broken icon)

---

## Changelog

### 0.4.0rc1 — 2026-06-25
- รองรับ 3 ภาษา: **TH / EN / JP** — ครอบคลุม UI labels, toast, confirm dialogs, overlay, help modal
- Language switcher ปุ่ม TH/EN/JP ใต้ header — บันทึกใน localStorage ข้าม session
- `I18N` object + `t()` function สำหรับ dynamic translation ทุก string
- Help modal content แปลเป็น 3 ภาษา ผ่าน structured `HELP` data object
- Time format ปรับตาม locale: `th-TH` / `en-US` / `ja-JP`
- Font stack เพิ่ม Hiragino Sans (JP) และ Noto Sans Thai (TH)

### 0.3.0rc1 — 2026-06-25
- **แยกเป็น 2 version**: PHP (`php/`) และ Static (`cf/`) ใน project เดียว
- **ย้าย image generation จาก PHP GD → Canvas API** (client-side, ทั้งสอง version)
- **ลบ GD dependency** ออกทั้งหมด — PHP version ต้องการแค่ PHP 8.2 + web server
- **Static version**: IndexedDB เก็บรูปใน browser, ไม่มี server upload เลย
- **Export/Import ZIP** (JSZip) — ย้ายข้อมูลข้ามเครื่องได้, รองรับ cross-version
- **Quota check 50 MB** พร้อม progress bar (static version)
- **คู่มือการใช้งาน in-app** — help banner + modal ครอบคลุมทุก feature

### 0.2.0rc1 — 2026-06-25
- เพิ่ม / ลบ tier ได้ไม่จำกัด
- แก้ไขชื่อ tier แบบ inline
- เปลี่ยนสี tier ผ่าน color picker
- localStorage schema เปลี่ยนเป็น `tiers[]` พร้อม migration จาก v0.1

### 0.1.0rc1 — 2026-06-25
- Initial release candidate
- Upload + PHP GD generate + Auto-save localStorage
