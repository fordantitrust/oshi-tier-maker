# Installation Guide

## สารบัญ

- [PHP Version](#php-version)
- [Static Version (CF)](#static-version-cf)
- [ทดสอบหลังติดตั้ง](#ทดสอบหลังติดตั้ง)
- [Troubleshooting](#troubleshooting)

---

## PHP Version

ไฟล์อยู่ที่ `php/`

### Requirements

| Component | Minimum | หมายเหตุ |
|---|---|---|
| PHP | 8.2 | ตรวจด้วย `php -v` |
| Web server | — | Apache / Nginx / Caddy / `php -S` |

### ขั้นตอนติดตั้ง

#### Apache (XAMPP / WAMP / LAMP)

1. **copy โฟลเดอร์ `php/` ไปไว้ใน document root**

   ```
   # XAMPP (Windows)
   C:\xampp\htdocs\oshi-tier\

   # XAMPP (macOS)
   /Applications/XAMPP/htdocs/oshi-tier/

   # Ubuntu / Debian
   /var/www/html/oshi-tier/
   ```

2. **ตั้ง permission ของ `uploads/`**

   ```bash
   # Linux / macOS
   chmod 755 php/uploads/

   # ถ้ายังเขียนไม่ได้
   chmod 777 php/uploads/
   ```

   Windows (XAMPP): ไม่ต้องตั้ง permission — เขียนได้เลย

4. **เปิดใน browser**

   ```
   http://localhost/oshi-tier/
   ```

---

#### Nginx

1. copy โฟลเดอร์ `php/` ไปไว้ที่ต้องการ เช่น `/var/www/oshi-tier/`

2. เพิ่ม server block ใน Nginx config:

   ```nginx
   server {
       listen 80;
       server_name oshi-tier.example.com;
       root /var/www/oshi-tier;
       index index.php;

       location / {
           try_files $uri $uri/ =404;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }

       # ป้องกัน PHP ใน uploads/
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

---

#### PHP Built-in Server (development)

ไม่ต้องติดตั้ง web server เพิ่ม — ใช้ได้เลยสำหรับทดสอบ:

```bash
cd php/
php -S localhost:8080
```

เปิด `http://localhost:8080`

> **หมายเหตุ**: Built-in server ไม่ควรใช้ใน production

---

## Static Version (CF)

ไฟล์อยู่ที่ `cf/index.html` — ไม่ต้องมี backend ใดๆ

### Requirements

- Modern browser: Chrome 90+ / Firefox 88+ / Safari 15+ / Edge 90+
- ต้องการ HTTPS หรือ localhost (IndexedDB ถูก block บน `file://` ใน Chrome)

### ตัวเลือกการ Deploy

#### Cloudflare Pages

```bash
# ติดตั้ง Wrangler
npm install -g wrangler

# Login
wrangler login

# Deploy
wrangler pages deploy cf/ --project-name=oshi-tier
```

---

#### GitHub Pages

1. สร้าง repository บน GitHub
2. push ไฟล์ขึ้นไป:

   ```bash
   git init
   git add cf/
   git commit -m "initial commit"
   git remote add origin https://github.com/<user>/<repo>.git
   git push -u origin main
   ```

3. ไปที่ **Settings → Pages → Source** เลือก branch `main` และโฟลเดอร์ `/cf`
4. เข้าถึงได้ที่ `https://<user>.github.io/<repo>/`

---

#### Netlify

**วิธีที่ 1 — Drag & Drop (ง่ายที่สุด):**
1. เปิด [app.netlify.com](https://app.netlify.com)
2. ลากโฟลเดอร์ `cf/` ไปวางในหน้า Deploy

**วิธีที่ 2 — CLI:**
```bash
npm install -g netlify-cli
netlify deploy --dir=cf/ --prod
```

---

#### Vercel

```bash
npm install -g vercel
vercel cf/
```

---

#### VPS / Shared Hosting (Apache)

```bash
# copy ไฟล์ขึ้น server
scp cf/index.html user@server:/var/www/html/oshi-tier/

# หรือใช้ FTP อัพโหลด cf/index.html ไปที่ public_html/oshi-tier/
```

เข้าถึงได้ที่ `https://example.com/oshi-tier/`

---

#### Local Development (ทดสอบแบบไม่มี server)

**ใช้ PHP built-in server:**
```bash
php -S localhost:8080 -t cf/
```

**ใช้ Python:**
```bash
# Python 3
python -m http.server 8080 --directory cf/
```

**ใช้ Node.js:**
```bash
npx serve cf/
```

เปิด `http://localhost:8080` — IndexedDB จะทำงานได้ปกติ

> ⚠️ **อย่าเปิดด้วย `file://` ใน Chrome** — IndexedDB จะถูก block  
> Firefox เปิดด้วย `file://` ได้ปกติ

---

## ทดสอบหลังติดตั้ง

### PHP Version
- [ ] เปิดหน้าแสดงผลได้ ไม่มี error
- [ ] อัพโหลดรูปได้ ไม่มี error "บันทึกไฟล์ไม่สำเร็จ"
- [ ] รูปที่อัพโหลดปรากฏใน `php/uploads/`
- [ ] ลากรูปลง tier ได้
- [ ] กด "สร้างภาพ PNG" ดาวน์โหลดไฟล์ได้
- [ ] Export ZIP ได้ — ไฟล์มี `state.json` และโฟลเดอร์ `images/`
- [ ] Import ZIP กลับได้ — รูปและ tier config กลับมาครบ
- [ ] รีเฟรชหน้า — state restore กลับมาครบ

### Static Version (CF)
- [ ] เปิดหน้าแสดงผลได้
- [ ] อัพโหลดรูปได้ — progress bar storage อัพเดท
- [ ] ลากรูปลง tier ได้
- [ ] กด "สร้างภาพ PNG" ดาวน์โหลดไฟล์ได้
- [ ] Export ZIP ได้
- [ ] Import ZIP กลับได้
- [ ] รีเฟรชหน้า — รูปโหลดกลับมาจาก IndexedDB

---

## Troubleshooting

### PHP Version

**`upload.php` ส่งกลับ error "บันทึกไฟล์ไม่สำเร็จ"**
```bash
# ตรวจ permission
ls -la php/uploads/
chmod 755 php/uploads/

# ตรวจว่า folder มีอยู่จริง
mkdir -p php/uploads/
```

**รูปไม่แสดงหลัง upload**
- ตรวจ browser console สำหรับ 404 error
- ตรวจ web server ว่า serve ไฟล์จาก `uploads/` ได้

**Canvas ไม่สร้างภาพ (ภาพเป็นสีพื้น ไม่มีรูป)**
- อาจเกิดจาก CORS ถ้า `uploads/` อยู่คนละ origin — ในกรณีปกติ (same origin) ไม่มีปัญหา

---

### Static Version (CF)

**IndexedDB ไม่ทำงาน / รูปหายหลัง refresh**
- ตรวจสอบว่าเปิดผ่าน `http://` ไม่ใช่ `file://` (หรือใช้ Firefox)
- ตรวจสอบว่าเบราว์เซอร์ไม่ได้อยู่ใน Incognito / Private mode
- ตรวจ browser console: `indexedDB` ต้องไม่ undefined

**Storage เต็ม / รูปเพิ่มไม่ได้**
- กด Export ZIP ก่อนเพื่อสำรองข้อมูล
- กด "ล้างทั้งหมด" เพื่อล้าง IndexedDB
- Import ZIP กลับมาใหม่

**Export ZIP ว่างเปล่า / ไม่มีรูป**
- ตรวจสอบว่ารูปถูก restore จาก IndexedDB แล้ว (ไม่ใช่แค่แสดง error icon)

**Canvas tainted error ใน console**
- ไม่ควรเกิดกับ static version เพราะ image src เป็น `blob://` (same-origin เสมอ)
- ถ้าเกิด ให้ตรวจสอบว่าไม่ได้โหลดรูปจาก external URL
