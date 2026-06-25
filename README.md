# Oshi Tier Maker

**Version 0.3.0rc1**

สร้าง tier list รูปภาพแบบกำหนดเองได้ ออกมาเป็นไฟล์ PNG — มี 2 version ให้เลือกใช้

---

## Versions

| | PHP Version | Cloudflare Serverless |
|---|---|---|
| **Directory** | `oshi-tier/php/` | `oshi-tier/cf/` |
| **Entry point** | `php/index.php` | `cf/index.html` |
| **Upload storage** | Server filesystem (`uploads/`) | IndexedDB (browser) |
| **Image generation** | Canvas API (client-side) | Canvas API (client-side) |
| **Server required** | PHP 8.2 + GD | ไม่ต้องมี server |
| **Cross-device** | ✅ ผ่าน server หรือ ZIP | ZIP เท่านั้น |
| **Quota** | Disk space ของ server | ~50 MB ต่อ browser |

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

---

## Requirements

### PHP Version
| Component | Version |
|---|---|
| PHP | 8.2+ |
| Web server | Apache / Nginx / `php -S` |

### Cloudflare Serverless Version
- Modern browser (Chrome 90+, Firefox 88+, Safari 15+)
- รองรับ IndexedDB และ Canvas API
- ไม่ต้องมี server หรือ backend ใดๆ

---

## Installation

### PHP Version
1. วางโฟลเดอร์ `oshi-tier/php/` ลงใน document root (หรือ symlink/alias ชี้มาที่โฟลเดอร์นี้)

2. ตรวจสอบ GD extension
   ```bash
   php -r "echo extension_loaded('gd') ? 'GD OK' : 'GD missing';"
   ```

3. ตรวจสอบ permission ของ `php/uploads/`
   ```bash
   chmod 755 php/uploads/   # Linux/macOS
   ```

4. เปิด `http://localhost/php/` (หรือตาม path ที่ตั้งค่า web server)

### Cloudflare Serverless Version
1. deploy โฟลเดอร์ `oshi-tier/cf/` บน static hosting
   - Cloudflare Pages: `npx wrangler pages deploy cf/`
   - หรือเปิด `cf/index.html` ตรงๆ ในเบราว์เซอร์ (file://)

2. ไม่ต้องตั้งค่าอะไรเพิ่มเติม

---

## Project Structure

```
oshi-tier/               ← project root
├── php/                 ← PHP version
│   ├── index.php        หน้าหลัก UI + Canvas generation
│   ├── upload.php       รับ AJAX upload → server filesystem
│   └── uploads/
│       └── .htaccess    กัน PHP execution และ directory listing
├── cf/                  ← Cloudflare serverless version
│   └── index.html       หน้าเดียว ครบทุกอย่าง (IndexedDB + Canvas)
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
- รูปถูก **บันทึกบนเซิร์ฟเวอร์** ใน `uploads/` ด้วยชื่อ hex random
- `localStorage` เก็บเฉพาะ metadata (tier config, filenames, perRow)
- Export ZIP จะ fetch รูปจากเซิร์ฟเวอร์มาแพ็ค

### Cloudflare Serverless Version
- รูปถูก **บันทึกใน IndexedDB** ของเบราว์เซอร์เท่านั้น
- ไม่มีข้อมูลใดส่งออกไปยัง server ภายนอก
- Quota สูงสุด 50 MB (แสดง progress bar)
- รูปไม่คงอยู่ถ้าล้าง browser data หรือใช้ Private/Incognito

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
// state.json
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
| Photo size | 110 × 145 px (cover-crop, client-side Canvas) |
| Label width | 140 px |
| Gap | 4 px |
| Canvas width | `140 + perRow × 110 + (perRow+1) × 4` px |
| Tier color | กำหนดจาก UI — 8 สีให้เลือก |
| Font | System font stack (Segoe UI / Noto Sans Thai / Arial) |
| Generation | Browser Canvas API — ไม่ต้องพึ่ง server |

---

## Security (PHP Version)

- ตรวจ MIME type จริงด้วย `finfo`
- บันทึกชื่อไฟล์เป็น `hex(random_bytes(16))`
- `basename()` กัน path traversal
- `uploads/.htaccess` บล็อก PHP execution
- จำกัดขนาดไฟล์ 10 MB / ไฟล์

---

## Changelog

### 0.3.0rc1 — 2026-06-25
- **แยกเป็น 2 version**: PHP (`oshi-tier/`) และ Cloudflare serverless (`oshi-tier-cf/`)
- **ย้าย image generation จาก PHP GD → Canvas API** (client-side, ทั้งสอง version)
- **CF version**: IndexedDB เก็บรูปใน browser, ไม่มี server upload เลย
- **Export/Import ZIP** (JSZip) — ย้ายข้อมูลข้ามเครื่องได้
- **Quota check 50 MB** พร้อม progress bar (CF version)
- **คำเตือน storage** บอกผู้ใช้ว่ารูปอยู่ที่ไหน (server หรือ browser)
- **CF Import**: quota check ก่อน import, ไม่ต้อง remap IDs

### 0.2.0rc1 — 2026-06-25
- เพิ่ม / ลบ tier ได้ไม่จำกัด
- แก้ไขชื่อ tier แบบ inline
- เปลี่ยนสี tier ผ่าน color picker
- localStorage schema เปลี่ยนเป็น `tiers[]` พร้อม migration จาก v0.1

### 0.1.0rc1 — 2026-06-25
- Initial release candidate
- Upload + PHP GD generate + Auto-save localStorage
