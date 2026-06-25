<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Oshi Tier Maker</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background:#0d0d1a; color:#dde; font-family:'Segoe UI','Noto Sans Thai',Tahoma,sans-serif; min-height:100vh; padding-bottom:40px; }

/* ── Header ── */
header { text-align:center; padding:32px 16px 20px; }
header h1 { font-size:34px; font-weight:800; letter-spacing:-.5px; background:linear-gradient(135deg,#ff8c8c 0%,#ffb347 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:6px; }
header p { color:#666; font-size:14px; }

/* ── Layout ── */
.container { max-width:1000px; margin:0 auto; padding:0 16px; }

/* ── Storage notice ── */
.storage-notice { display:flex; align-items:center; gap:10px; background:rgba(255,183,71,.07); border:1px solid rgba(255,183,71,.2); border-radius:10px; padding:10px 14px; font-size:13px; color:#888; margin-bottom:20px; }
.storage-notice strong { color:#ffb347; }

/* ── Upload zone ── */
.upload-zone { border:2px dashed #2e2e4a; border-radius:14px; padding:36px 20px; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; background:#13132b; margin-bottom:20px; user-select:none; }
.upload-zone:hover,.upload-zone.dragover { border-color:#ffb347; background:#18183a; }
.upload-zone input[type="file"] { display:none; }
.upload-icon { font-size:38px; margin-bottom:10px; }
.upload-zone .title { font-size:16px; margin-bottom:4px; }
.upload-zone .hint { color:#555; font-size:13px; }

/* ── Section ── */
.section { margin-bottom:20px; }
.section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.section-label { font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#666; }

/* ── Photo zones ── */
.photo-zone { display:flex; flex-wrap:wrap; gap:6px; padding:8px; border-radius:8px; min-height:90px; transition:outline .15s; }
.photo-zone.drag-over { outline:2px dashed #ffb347; outline-offset:-2px; }
#pool { background:#13132b; border:1px solid #1e1e38; }
.tier-zone { flex:1; background:rgba(255,255,255,.04); min-height:160px; }
.empty-hint { color:#333; font-size:13px; padding:16px; width:100%; text-align:center; pointer-events:none; align-self:center; }

/* ── Tier container ── */
.tier-container { border:1px solid #1e1e38; border-radius:10px; overflow:hidden; }
.tier-row { display:flex; border-bottom:2px solid rgba(0,0,0,.35); }
#tier-list .tier-row:last-child { border-bottom:none; }

/* ── Tier label ── */
.tier-label { position:relative; width:100px; flex-shrink:0; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:28px 6px 18px; min-height:160px; cursor:pointer; transition:filter .15s; }
.tier-label:hover { filter:brightness(1.07); }
.tier-name { font-weight:800; font-size:18px; color:#2a2a2a; text-align:center; text-shadow:0 1px 0 rgba(255,255,255,.3); word-break:break-word; line-height:1.3; padding:3px 4px; border-radius:4px; cursor:text; transition:background .15s; }
.tier-name:hover { background:rgba(0,0,0,.1); }
.tier-name-input { width:88%; background:rgba(255,255,255,.9); border:2px solid rgba(0,0,0,.25); border-radius:4px; padding:3px 5px; font-size:14px; font-weight:700; color:#222; text-align:center; outline:none; }
.btn-del-tier { position:absolute; top:4px; right:4px; width:20px; height:20px; border-radius:50%; background:rgba(0,0,0,.3); color:#fff; border:none; font-size:13px; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .15s,background .15s; padding:0; }
.tier-row:hover .btn-del-tier { opacity:1; }
.btn-del-tier:hover { background:rgba(220,50,50,.85) !important; }
.color-hint { position:absolute; bottom:5px; font-size:10px; color:rgba(0,0,0,.45); opacity:0; transition:opacity .15s; pointer-events:none; letter-spacing:.3px; }
.tier-row:hover .color-hint { opacity:1; }

/* ── Add tier button ── */
.btn-add-tier { display:flex; align-items:center; justify-content:center; gap:6px; width:100%; padding:11px; background:transparent; border:none; border-top:1px solid #1e1e38; color:#555; font-size:14px; cursor:pointer; transition:color .2s,background .2s; }
.btn-add-tier:hover { color:#ffb347; background:rgba(255,179,71,.06); }

/* ── Color picker popup ── */
.color-picker-popup { position:fixed; display:grid; grid-template-columns:repeat(4,1fr); gap:5px; padding:8px; background:#1a1a30; border:1px solid #2e2e4a; border-radius:10px; z-index:500; box-shadow:0 6px 24px rgba(0,0,0,.65); }
.color-swatch { width:30px; height:30px; border-radius:6px; cursor:pointer; border:2px solid transparent; transition:border-color .12s,transform .1s; }
.color-swatch:hover { transform:scale(1.18); border-color:rgba(255,255,255,.5); }
.color-swatch.selected { border-color:#fff; }

/* ── Photo thumbnails ── */
.photo-item { position:relative; width:100px; height:130px; border-radius:5px; overflow:hidden; cursor:grab; flex-shrink:0; background:#1e1e38; transition:transform .12s,box-shadow .12s; }
.photo-item:hover { transform:scale(1.04); box-shadow:0 4px 16px rgba(0,0,0,.5); }
.photo-item:active { cursor:grabbing; transform:scale(1.02); }
.photo-item img { width:100%; height:100%; object-fit:cover; display:block; pointer-events:none; }
.photo-item .btn-remove { position:absolute; top:3px; right:3px; width:22px; height:22px; border-radius:50%; background:rgba(0,0,0,.75); color:#fff; border:none; font-size:15px; line-height:22px; text-align:center; cursor:pointer; opacity:0; transition:opacity .15s; padding:0; }
.photo-item:hover .btn-remove { opacity:1; }
.photo-item.is-loading { display:flex; align-items:center; justify-content:center; font-size:26px; animation:pulse 1s ease-in-out infinite alternate; }
@keyframes pulse { from{opacity:.5} to{opacity:1} }

/* ── Buttons ── */
.btn-sm { background:transparent; border:1px solid #2e2e4a; color:#666; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:12px; transition:all .2s; white-space:nowrap; }
.btn-sm:hover { border-color:#ff8c8c; color:#ff8c8c; }
.btn-generate { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:16px; background:linear-gradient(135deg,#ff8c8c 0%,#ffb347 100%); border:none; border-radius:12px; color:#fff; font-size:18px; font-weight:700; cursor:pointer; margin-top:24px; transition:opacity .2s,transform .1s; letter-spacing:.3px; }
.btn-generate:hover { opacity:.9; transform:translateY(-1px); }
.btn-generate:active { transform:translateY(0); }
.btn-generate:disabled { opacity:.5; cursor:not-allowed; transform:none; }

/* ── Per-row selector ── */
.perrow-group { display:flex; align-items:center; gap:10px; background:#13132b; border:1px solid #1e1e38; border-radius:10px; padding:12px 16px; margin-top:20px; }
.perrow-group > label { font-size:14px; color:#aaa; white-space:nowrap; }
.perrow-pills { display:flex; gap:6px; flex-wrap:wrap; }
.perrow-pills input[type="radio"] { display:none; }
.perrow-pills label { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; border:1px solid #2e2e4a; background:#0d0d1a; color:#888; font-size:14px; font-weight:600; cursor:pointer; transition:all .15s; user-select:none; }
.perrow-pills input[type="radio"]:checked + label { background:linear-gradient(135deg,#ff8c8c,#ffb347); border-color:transparent; color:#fff; }
.perrow-pills label:hover { border-color:#ffb347; color:#ffb347; }

/* ── Save status ── */
.save-status { font-size:12px; color:#555; opacity:0; transition:opacity .3s; white-space:nowrap; }
.save-status.visible { opacity:1; }

/* ── Loading overlay ── */
#overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.72); z-index:999; flex-direction:column; align-items:center; justify-content:center; gap:18px; }
#overlay.active { display:flex; }
#overlay p { color:#aaa; font-size:14px; }
.spinner { width:50px; height:50px; border:4px solid #2e2e4a; border-top-color:#ffb347; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

/* ── Toast ── */
#toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px); background:#1e1e38; border:1px solid #ff8c8c; color:#ff8c8c; padding:10px 22px; border-radius:8px; font-size:14px; transition:transform .25s; z-index:1000; white-space:nowrap; }
#toast.show { transform:translateX(-50%) translateY(0); }
</style>
</head>
<body>

<header>
    <h1>✦ Oshi Tier Maker</h1>
    <p>อัพโหลดรูป → ลากจัด tier → สร้างภาพ PNG</p>
</header>

<div class="container">

    <!-- Server storage notice -->
    <div class="storage-notice">
        📁&nbsp; รูปที่อัพโหลดจะถูก<strong>บันทึกบนเซิร์ฟเวอร์</strong>ที่ให้บริการอยู่เท่านั้น — ใช้ <strong>Export ZIP</strong> เพื่อสำรองข้อมูลไว้กับตัวเอง
    </div>

    <!-- Upload zone -->
    <div id="upload-zone" class="upload-zone">
        <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="upload-icon">🖼️</div>
        <p class="title">คลิกหรือลากไฟล์มาวางที่นี่</p>
        <p class="hint">JPG · PNG · GIF · WebP &nbsp;|&nbsp; สูงสุด 10 MB / ไฟล์</p>
    </div>

    <!-- Pool -->
    <div class="section">
        <div class="section-header">
            <span class="section-label">รูปที่ยังไม่ได้จัด tier</span>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span id="save-status" class="save-status"></span>
                <button id="btn-export" class="btn-sm">📦 Export</button>
                <label class="btn-sm" style="cursor:pointer;">📂 Import<input type="file" id="import-input" accept=".zip" style="display:none"></label>
                <button id="btn-clear-all" class="btn-sm">ล้างทั้งหมด</button>
            </div>
        </div>
        <div id="pool" class="photo-zone">
            <p class="empty-hint">อัพโหลดรูปก่อน แล้วลากไปวางใน tier ด้านล่าง</p>
        </div>
    </div>

    <!-- Tier list -->
    <div class="section">
        <div class="section-header">
            <span class="section-label">Tier List</span>
            <span style="font-size:11px;color:#444;">คลิกชื่อ = แก้ไข &nbsp;|&nbsp; คลิกพื้นหลัง = เปลี่ยนสี</span>
        </div>
        <div class="tier-container">
            <div id="tier-list"></div>
            <button id="btn-add-tier" class="btn-add-tier">＋ เพิ่ม tier</button>
        </div>
    </div>

    <!-- Per-row -->
    <div class="perrow-group">
        <label>รูปต่อแถว&nbsp;:</label>
        <div class="perrow-pills">
            <input type="radio" name="perrow" id="pr3" value="3"><label for="pr3">3</label>
            <input type="radio" name="perrow" id="pr4" value="4"><label for="pr4">4</label>
            <input type="radio" name="perrow" id="pr5" value="5"><label for="pr5">5</label>
            <input type="radio" name="perrow" id="pr6" value="6" checked><label for="pr6">6</label>
            <input type="radio" name="perrow" id="pr7" value="7"><label for="pr7">7</label>
            <input type="radio" name="perrow" id="pr8" value="8"><label for="pr8">8</label>
        </div>
    </div>

    <button id="btn-generate" class="btn-generate"><span>🎨</span><span>สร้างภาพ PNG</span></button>

</div>

<div id="overlay"><div class="spinner"></div><p id="overlay-msg">กำลังทำงาน...</p></div>
<div id="toast"></div>

<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script>
(function () {
'use strict';

// ── Constants ─────────────────────────────────────────────────────────────────
const STORAGE_KEY   = 'oshi-tier-v1';
const PALETTE       = ['#F08080','#FFB347','#FFE566','#90EE90','#87CEEB','#DDA0DD','#FFB6C1','#98D8C8'];
const DEFAULT_TIERS = [{ id:'kami', name:'Kami', color:'#F08080' }, { id:'oshi', name:'Oshi', color:'#FFB347' }];
const CANVAS_CFG    = { LW:140, PW:110, PH:145, G:4 };

// ── State ─────────────────────────────────────────────────────────────────────
let tiersConfig = DEFAULT_TIERS.map(t => ({ ...t }));
let draggedEl   = null;
let colorPicker = null;

// ── DOM ───────────────────────────────────────────────────────────────────────
const pool       = document.getElementById('pool');
const tierListEl = document.getElementById('tier-list');
const overlay    = document.getElementById('overlay');
const overlayMsg = document.getElementById('overlay-msg');
const saveStatus = document.getElementById('save-status');

// ── Boot ──────────────────────────────────────────────────────────────────────
init();

function init() {
    setupUploadZone();
    setupZoneDrop(pool);
    document.getElementById('btn-add-tier').addEventListener('click', addTier);
    document.getElementById('btn-clear-all').addEventListener('click', onClearAll);
    document.getElementById('btn-generate').addEventListener('click', onGenerate);
    document.getElementById('btn-export').addEventListener('click', onExport);
    document.getElementById('import-input').addEventListener('change', e => {
        if (e.target.files[0]) onImport(e.target.files[0]);
        e.target.value = '';
    });
    document.querySelectorAll('input[name="perrow"]').forEach(r => r.addEventListener('change', saveState));
    document.addEventListener('click', () => closeColorPicker());
    if (!restoreState()) tiersConfig.forEach(renderTierRow);
}

// ── Tier management ───────────────────────────────────────────────────────────
function addTier() {
    const used  = new Set(tiersConfig.map(t => t.color));
    const color = PALETTE.find(c => !used.has(c)) ?? PALETTE[tiersConfig.length % PALETTE.length];
    const tier  = { id:'tier-'+Date.now(), name:'Tier '+(tiersConfig.length+1), color };
    tiersConfig.push(tier);
    const { nameSpan } = renderTierRow(tier);
    saveState();
    startInlineEdit(tier, nameSpan);
}

function deleteTier(id) {
    if (tiersConfig.length <= 1) { showToast('ต้องมี tier อย่างน้อย 1 tier'); return; }
    const zone   = document.getElementById('zone-'+id);
    const photos = zone ? [...zone.querySelectorAll('.photo-item')] : [];
    if (photos.length > 0 && !confirm(`ลบ tier นี้? รูป ${photos.length} รูปจะกลับไปที่ pool`)) return;
    photos.forEach(p => appendPhoto(pool, p));
    tiersConfig = tiersConfig.filter(t => t.id !== id);
    document.querySelector(`.tier-row[data-tier-id="${id}"]`)?.remove();
    syncPoolHint();
    saveState();
}

function renderTierRow(tier) {
    const row  = document.createElement('div');
    row.className = 'tier-row'; row.dataset.tierId = tier.id;

    const label = document.createElement('div');
    label.className = 'tier-label'; label.style.background = tier.color; label.title = 'คลิกพื้นหลังเพื่อเปลี่ยนสี';
    label.addEventListener('click', e => { if (e.target === label || e.target === colorHint) showColorPicker(tier, label, e); });

    const nameSpan = document.createElement('span');
    nameSpan.className = 'tier-name'; nameSpan.textContent = tier.name; nameSpan.title = 'คลิกเพื่อแก้ไขชื่อ';
    nameSpan.addEventListener('click', e => { e.stopPropagation(); startInlineEdit(tier, nameSpan); });

    const delBtn = document.createElement('button');
    delBtn.className = 'btn-del-tier'; delBtn.type = 'button'; delBtn.textContent = '×'; delBtn.title = 'ลบ tier นี้';
    delBtn.addEventListener('click', e => { e.stopPropagation(); deleteTier(tier.id); });

    const colorHint = document.createElement('span');
    colorHint.className = 'color-hint'; colorHint.textContent = '🎨 เปลี่ยนสี';

    label.append(nameSpan, delBtn, colorHint);

    const zone = document.createElement('div');
    zone.className = 'photo-zone tier-zone'; zone.id = 'zone-'+tier.id; setupZoneDrop(zone);

    row.append(label, zone);
    tierListEl.appendChild(row);
    return { row, label, nameSpan, zone };
}

function startInlineEdit(tier, nameSpan) {
    const input = document.createElement('input');
    input.type = 'text'; input.className = 'tier-name-input';
    input.value = tier.name; input.maxLength = 24;
    const commit = () => { const v=input.value.trim(); if(v) tier.name=v; nameSpan.textContent=tier.name; input.replaceWith(nameSpan); saveState(); };
    input.addEventListener('keydown', e => { if(e.key==='Enter'){e.preventDefault();commit();} if(e.key==='Escape') input.replaceWith(nameSpan); });
    input.addEventListener('blur', commit);
    input.addEventListener('click', e => e.stopPropagation());
    nameSpan.replaceWith(input); input.focus(); input.select();
}

// ── Color picker ──────────────────────────────────────────────────────────────
function showColorPicker(tier, labelEl, ev) {
    ev?.stopPropagation(); closeColorPicker();
    const picker = document.createElement('div'); picker.className = 'color-picker-popup';
    PALETTE.forEach(color => {
        const sw = document.createElement('div');
        sw.className = 'color-swatch'+(color===tier.color?' selected':''); sw.style.background=color; sw.title=color;
        sw.addEventListener('click', e => { e.stopPropagation(); tier.color=color; labelEl.style.background=color; closeColorPicker(); saveState(); });
        picker.appendChild(sw);
    });
    document.body.appendChild(picker); colorPicker = picker;
    const r = labelEl.getBoundingClientRect();
    picker.style.top  = (r.bottom+4)+'px';
    picker.style.left = r.left+'px';
}
function closeColorPicker() { colorPicker?.remove(); colorPicker=null; }

// ── Drop zones ────────────────────────────────────────────────────────────────
function setupZoneDrop(zone) {
    zone.addEventListener('dragover', e => { e.preventDefault(); if(draggedEl) zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('drag-over'); if(draggedEl){ appendPhoto(zone,draggedEl); saveState(); } });
}

// ── Upload ────────────────────────────────────────────────────────────────────
function setupUploadZone() {
    const uz = document.getElementById('upload-zone'), fi = document.getElementById('file-input');
    uz.addEventListener('click', () => fi.click());
    fi.addEventListener('change', e => { processFiles(e.target.files); fi.value=''; });
    uz.addEventListener('dragenter', e => e.preventDefault());
    uz.addEventListener('dragover',  e => { e.preventDefault(); if(!draggedEl) uz.classList.add('dragover'); });
    uz.addEventListener('dragleave', () => uz.classList.remove('dragover'));
    uz.addEventListener('drop', e => { e.preventDefault(); uz.classList.remove('dragover'); if(e.dataTransfer.files.length) processFiles(e.dataTransfer.files); });
}
function processFiles(files) { Array.from(files).forEach(f => { if(/^image\/(jpeg|png|gif|webp)$/.test(f.type)) uploadFile(f); }); }

async function uploadFile(file) {
    const ph = makeLoadingEl(); appendPhoto(pool, ph);
    const fd = new FormData(); fd.append('image', file);
    try {
        const json = await fetch('upload.php',{method:'POST',body:fd}).then(r=>r.json());
        if (json.success) { ph.replaceWith(makePhotoEl(json.filename,'uploads/'+json.filename)); saveState(); }
        else { ph.remove(); showToast('อัพโหลดล้มเหลว: '+json.error); }
    } catch(e) { ph.remove(); showToast('เกิดข้อผิดพลาด: '+e.message); }
}

// ── Element factories ─────────────────────────────────────────────────────────
function makeLoadingEl() { const d=document.createElement('div'); d.className='photo-item is-loading'; d.textContent='⏳'; return d; }

function makePhotoEl(filename, src) {
    const div = document.createElement('div');
    div.className='photo-item'; div.draggable=true; div.dataset.filename=filename;
    const img = document.createElement('img'); img.src=src; img.alt=''; img.draggable=false;
    img.addEventListener('error', () => { div.remove(); syncPoolHint(); saveState(); });
    const btn = document.createElement('button'); btn.className='btn-remove'; btn.type='button'; btn.textContent='×'; btn.title='ลบรูป';
    btn.addEventListener('click', e => { e.stopPropagation(); div.remove(); syncPoolHint(); saveState(); });
    div.append(img, btn);
    div.addEventListener('dragstart', e => { draggedEl=div; e.dataTransfer.effectAllowed='move'; requestAnimationFrame(()=>div.style.opacity='.4'); });
    div.addEventListener('dragend',   () => { div.style.opacity=''; draggedEl=null; });
    return div;
}

// ── Pool helpers ──────────────────────────────────────────────────────────────
function appendPhoto(zone, el) { zone.querySelectorAll('.empty-hint').forEach(h=>h.remove()); zone.appendChild(el); if(zone===pool) syncPoolHint(); }
function syncPoolHint() { if(!pool.querySelector('.photo-item')&&!pool.querySelector('.empty-hint')){ const p=document.createElement('p'); p.className='empty-hint'; p.textContent='อัพโหลดรูปก่อน แล้วลากไปวางใน tier ด้านล่าง'; pool.appendChild(p); } }
function collectFilenames(zone) { return zone ? [...zone.querySelectorAll('.photo-item[data-filename]')].map(el=>el.dataset.filename) : []; }
function getAllFileIds(state) { return [...new Set([ ...(state.pool??[]), ...(state.tiers??[]).flatMap(t=>t.files??[]) ])]; }

// ── Clear all ─────────────────────────────────────────────────────────────────
function onClearAll() {
    const total = document.querySelectorAll('.photo-item').length;
    if(!total) return;
    if(!confirm(`ต้องการลบรูปทั้งหมด ${total} รูป?\n(ข้อมูลที่บันทึกไว้จะถูกลบด้วย)`)) return;
    document.querySelectorAll('.photo-item').forEach(el=>el.remove());
    syncPoolHint(); clearSavedState();
}

// ── Generate PNG (Canvas API) ─────────────────────────────────────────────────
async function onGenerate() {
    const tiers = tiersConfig
        .map(t => ({ name:t.name, color:t.color, files:collectFilenames(document.getElementById('zone-'+t.id)) }))
        .filter(t => t.files.length > 0);
    if(!tiers.length) { showToast('ลากรูปไปวางใน tier ก่อนนะ'); return; }

    const perRow = parseInt(document.querySelector('input[name="perrow"]:checked').value, 10);
    showOverlay('กำลังสร้างภาพ...');
    document.getElementById('btn-generate').disabled = true;
    try {
        const canvas = await buildTierCanvas(tiers, perRow, fn => Promise.resolve('uploads/'+fn));
        const blob   = await canvasToBlob(canvas);
        dlBlob(blob, 'oshi-tier.png');
    } catch(e) {
        showToast('สร้างภาพไม่สำเร็จ: '+e.message);
    } finally {
        hideOverlay();
        document.getElementById('btn-generate').disabled = false;
    }
}

// ── Export ZIP ────────────────────────────────────────────────────────────────
async function onExport() {
    const state = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? 'null');
    const ids   = state ? getAllFileIds(state) : [];
    if(!ids.length) { showToast('ไม่มีข้อมูลสำหรับ export'); return; }

    showOverlay(`กำลัง export (0/${ids.length})...`);
    try {
        const zip  = new JSZip();
        zip.file('state.json', JSON.stringify(state, null, 2));
        const imgs = zip.folder('images');
        for (const [i, id] of ids.entries()) {
            overlayMsg.textContent = `กำลัง export (${i+1}/${ids.length})...`;
            try { imgs.file(id, await fetch('uploads/'+id).then(r=>r.blob())); } catch {}
        }
        const content = await zip.generateAsync({ type:'blob', compression:'DEFLATE', compressionOptions:{level:6} });
        dlBlob(content, 'oshi-tier-export.zip');
        showToast('Export สำเร็จ');
    } catch(e) {
        showToast('Export ล้มเหลว: '+e.message);
    } finally { hideOverlay(); }
}

// ── Import ZIP ────────────────────────────────────────────────────────────────
async function onImport(file) {
    showOverlay('กำลังอ่านไฟล์ ZIP...');
    try {
        const zip       = await JSZip.loadAsync(file);
        const stateFile = zip.file('state.json');
        if(!stateFile) throw new Error('ไม่พบ state.json ในไฟล์ ZIP');

        const state     = JSON.parse(await stateFile.async('string'));
        const imgFolder = zip.folder('images');
        if(!imgFolder)  throw new Error('ไม่พบโฟลเดอร์ images ในไฟล์ ZIP');

        const uploads = [];
        imgFolder.forEach((path, f) => { if(!f.dir) uploads.push([path.split('/').pop(), f]); });

        const idMap = {};
        for (const [i, [oldId, zipFile]] of uploads.entries()) {
            overlayMsg.textContent = `กำลังอัพโหลด (${i+1}/${uploads.length})...`;
            const blob = await zipFile.async('blob');
            const fd   = new FormData();
            fd.append('image', new File([blob], oldId, { type: mimeFromExt(oldId) }));
            try {
                const json = await fetch('upload.php',{method:'POST',body:fd}).then(r=>r.json());
                if(json.success) idMap[oldId] = json.filename;
            } catch {}
        }

        const newState = remapState(state, idMap);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(newState));
        showToast('Import สำเร็จ กำลัง reload...');
        setTimeout(() => location.reload(), 900);
    } catch(e) {
        showToast('Import ล้มเหลว: '+e.message);
        hideOverlay();
    }
}

function remapState(state, idMap) {
    const r = id => idMap[id] ?? id;
    return { ...state, pool:(state.pool??[]).map(r), tiers:(state.tiers??[]).map(t=>({...t,files:(t.files??[]).map(r)})) };
}
function mimeFromExt(name) {
    return ({ jpg:'image/jpeg', jpeg:'image/jpeg', png:'image/png', gif:'image/gif', webp:'image/webp' })[name.split('.').pop().toLowerCase()] ?? 'image/jpeg';
}

// ── Canvas generation helpers ─────────────────────────────────────────────────
async function buildTierCanvas(tiers, perRow, getSrc) {
    const { LW, PW, PH, G } = CANVAS_CFG;
    const th = n => Math.max(1,Math.ceil(n/perRow))*PH + (Math.max(1,Math.ceil(n/perRow))+1)*G;
    const W  = LW + perRow*PW + (perRow+1)*G;
    const H  = tiers.reduce((s,t) => s+th(t.files.length), 0);

    const canvas = document.createElement('canvas'); canvas.width=W; canvas.height=H;
    const ctx    = canvas.getContext('2d');

    let y = 0;
    for (const tier of tiers) {
        const h = th(tier.files.length);
        ctx.fillStyle = tier.color; ctx.fillRect(0,y,W,h);

        ctx.save();
        ctx.fillStyle = 'rgba(0,0,0,0.65)';
        ctx.font = 'bold 20px "Segoe UI","Noto Sans Thai",Arial,sans-serif';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        drawWrappedText(ctx, tier.name, LW/2, y+h/2, LW-10, 26);
        ctx.restore();

        for (let i=0; i<tier.files.length; i++) {
            const row=Math.floor(i/perRow), col=i%perRow;
            const px=LW+G+col*(PW+G), py=y+G+row*(PH+G);
            try {
                const src = await getSrc(tier.files[i]);
                const img = await loadImg(src);
                const {sx,sy,sW,sH} = coverCrop(img.naturalWidth, img.naturalHeight, PW, PH);
                ctx.drawImage(img,sx,sy,sW,sH,px,py,PW,PH);
            } catch {}
        }
        y += h;
    }
    return canvas;
}

function drawWrappedText(ctx, text, cx, cy, maxW, lineH) {
    if (ctx.measureText(text).width <= maxW) { ctx.fillText(text,cx,cy); return; }
    const words = text.split(' '); const lines = []; let cur='';
    for (const w of words) { const t=cur?cur+' '+w:w; if(ctx.measureText(t).width>maxW&&cur){lines.push(cur);cur=w;}else cur=t; }
    if(cur) lines.push(cur);
    const startY = cy-(lines.length-1)*lineH/2;
    lines.forEach((l,i)=>ctx.fillText(l,cx,startY+i*lineH));
}

function loadImg(src) { return new Promise((res,rej) => { const img=new Image(); img.onload=()=>res(img); img.onerror=rej; img.src=src; }); }

function coverCrop(sw,sh,dw,dh) {
    const sr=sw/sh, dr=dw/dh;
    if (sr>dr) { const sW=Math.round(sh*dr); return {sx:Math.round((sw-sW)/2),sy:0,sW,sH:sh}; }
    else       { const sH=Math.round(sw/dr); return {sx:0,sy:Math.round((sh-sH)/2),sW:sw,sH}; }
}

function canvasToBlob(canvas) { return new Promise((res,rej)=>canvas.toBlob(b=>b?res(b):rej(new Error('toBlob failed')),'image/png')); }
function dlBlob(blob, name)   { const u=URL.createObjectURL(blob),a=document.createElement('a'); a.href=u; a.download=name; document.body.appendChild(a); a.click(); a.remove(); setTimeout(()=>URL.revokeObjectURL(u),1000); }

// ── Overlay helpers ───────────────────────────────────────────────────────────
function showOverlay(msg) { overlayMsg.textContent=msg; overlay.classList.add('active'); }
function hideOverlay()    { overlay.classList.remove('active'); }

// ── Persistence ───────────────────────────────────────────────────────────────
function saveState() {
    const perRowEl = document.querySelector('input[name="perrow"]:checked');
    const state = {
        tiers:   tiersConfig.map(t => ({ ...t, files:collectFilenames(document.getElementById('zone-'+t.id)) })),
        pool:    collectFilenames(pool),
        perRow:  parseInt(perRowEl?.value??'6',10),
        savedAt: Date.now(),
    };
    try { localStorage.setItem(STORAGE_KEY,JSON.stringify(state)); updateSaveStatus(state.savedAt); } catch {}
}

function clearSavedState() {
    try { localStorage.removeItem(STORAGE_KEY); } catch {}
    saveStatus.textContent=''; saveStatus.classList.remove('visible');
}

function restoreState() {
    let state;
    try { state=JSON.parse(localStorage.getItem(STORAGE_KEY)??'null'); } catch { return false; }
    if(!state) return false;

    // Migrate v0.1 format
    if (!state.tiers && (state.kami!==undefined||state.oshi!==undefined)) {
        state.tiers = [];
        if(state.kami) state.tiers.push({id:'kami',name:'Kami',color:'#F08080',files:state.kami});
        if(state.oshi) state.tiers.push({id:'oshi',name:'Oshi',color:'#FFB347',files:state.oshi});
    }

    if(!state.tiers?.length && !state.pool?.length) return false;

    if(state.tiers?.length) {
        tiersConfig = state.tiers.map(({id,name,color})=>({id,name,color}));
        tiersConfig.forEach(tier => {
            const {zone} = renderTierRow(tier);
            (state.tiers.find(t=>t.id===tier.id)?.files??[]).forEach(fn=>appendPhoto(zone,makePhotoEl(fn,'uploads/'+fn)));
        });
    }
    (state.pool??[]).forEach(fn=>appendPhoto(pool,makePhotoEl(fn,'uploads/'+fn)));
    if(state.perRow){ const r=document.querySelector(`input[name="perrow"][value="${state.perRow}"]`); if(r) r.checked=true; }
    if(state.savedAt) updateSaveStatus(state.savedAt);

    const total=(state.pool?.length??0)+(state.tiers?.reduce((s,t)=>s+(t.files?.length??0),0)??0);
    if(total>0) showToast(`โหลดข้อมูลที่บันทึกไว้ · ${total} รูป · บันทึกเมื่อ ${fmtTime(state.savedAt)}`);
    return true;
}

function updateSaveStatus(ts) { saveStatus.textContent=`💾 บันทึกแล้ว · ${fmtTime(ts)}`; saveStatus.classList.add('visible'); }
function fmtTime(ts) { return new Date(ts).toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'}); }

// ── Toast ─────────────────────────────────────────────────────────────────────
let toastTimer;
function showToast(msg) { const t=document.getElementById('toast'); t.textContent=msg; t.classList.add('show'); clearTimeout(toastTimer); toastTimer=setTimeout(()=>t.classList.remove('show'),4000); }

})();
</script>
</body>
</html>
