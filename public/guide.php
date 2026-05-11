<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/lang.php';

$lang = currentLang();
$pageTitle = 'AI Sayohatchi Gid';
require_once 'includes/seo.php';
renderMeta(['title' => $pageTitle . ' | Silk Road Explorer']);
require_once 'includes/layout_header.php';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<!-- Marked.js for Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
:root{
  --gold:#C8922A;
  --gold-lt:#DEB85A;
  --gold-pale:#F7E9C8;
  --brown:#1A0D04;
  --brown-mid:#2E1508;
  --brown-panel:#160B02;
  --cream:#FAF6EE;
  --cream-mid:#F0E8D8;
  --text-main:#1C0D04;
  --text-muted:#8A7260;
  --text-faint:#C4A882;
  --border:rgba(200,146,42,0.14);
  --border-mid:rgba(200,146,42,0.28);
}

.ai-app {
  display:flex;
  height:calc(100vh - 80px); /* Adjust for header */
  margin-top: 80px;
  overflow:hidden;
  font-family:'DM Sans',sans-serif;
  background:var(--cream);
  color:var(--text-main);
  box-sizing: border-box;
}

.ai-app * { box-sizing: border-box; }

/* ═══════════════ SIDEBAR ═══════════════ */
.sidebar{
  width:288px;flex-shrink:0;
  background:var(--brown-panel);
  display:flex;flex-direction:column;
  position:relative;overflow:hidden;
}
@media (max-width: 768px) {
  .sidebar { display: none; }
}

.sidebar-bg{
  position:absolute;inset:0;pointer-events:none;
  background-image:
    radial-gradient(circle at 50% 0%, rgba(200,146,42,0.12) 0%, transparent 55%),
    radial-gradient(circle at 50% 100%, rgba(200,146,42,0.06) 0%, transparent 40%);
}

.sidebar-pattern{
  position:absolute;inset:0;pointer-events:none;opacity:0.035;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Cpath d='M30 0 L35 10 L45 5 L40 15 L55 15 L45 22 L50 35 L38 30 L35 45 L30 32 L25 45 L22 30 L10 35 L15 22 L5 15 L20 15 L15 5 L25 10 Z' fill='%23C8922A'/%3E%3C/svg%3E");
  background-size:60px 60px;
}

.sidebar-top{
  padding:36px 26px 0;
  display:flex;flex-direction:column;align-items:center;
  position:relative;z-index:1;
}

.ornament{margin-bottom:20px}

.brand{align-self:flex-start;padding:0 2px}

.brand-name{
  font-family:'Cormorant Garamond',serif;
  font-size:30px;font-weight:600;
  color:var(--gold-lt);letter-spacing:0.02em;line-height:1;
}

.brand-name span{color:var(--gold);font-style:italic}

.brand-tagline{
  font-size:9.5px;font-weight:400;letter-spacing:0.2em;
  color:var(--gold);text-transform:uppercase;margin-top:4px;
}

.brand-desc{
  font-size:12.5px;font-weight:300;color:var(--text-faint);
  margin-top:14px;line-height:1.6;
}

.divider{
  height:1px;
  background:linear-gradient(to right,transparent,var(--border-mid),transparent);
  margin:22px 26px;
  position:relative;z-index:1;
}

.section-label{
  font-size:9px;font-weight:500;letter-spacing:0.16em;
  color:var(--gold);text-transform:uppercase;
  padding:0 26px 10px;position:relative;z-index:1;
}

.prompt-list{
  padding:0 16px;display:flex;flex-direction:column;gap:5px;
  position:relative;z-index:1;
}

.prompt-item{
  background:rgba(200,146,42,0.05);
  border:1px solid rgba(200,146,42,0.14);
  border-radius:12px;padding:11px 14px;
  cursor:pointer;transition:all 0.18s ease;
  display:flex;gap:11px;align-items:flex-start;
}

.prompt-item:hover{
  background:rgba(200,146,42,0.1);
  border-color:rgba(200,146,42,0.3);
  transform:translateX(2px);
}

.prompt-icon{
  width:30px;height:30px;
  background:rgba(200,146,42,0.12);
  border-radius:8px;display:flex;align-items:center;
  justify-content:center;flex-shrink:0;margin-top:1px;
}

.prompt-icon svg{width:15px;height:15px;stroke:var(--gold-lt);stroke-width:1.5;fill:none;stroke-linecap:round;stroke-linejoin:round}

.prompt-text strong{
  display:block;font-size:12.5px;font-weight:500;
  color:#EAD8B4;line-height:1.3;
}

.prompt-text span{
  display:block;font-size:11px;font-weight:300;
  color:var(--text-faint);margin-top:2px;
}

.sidebar-footer{
  margin-top:auto;padding:18px 26px 22px;
  border-top:1px solid rgba(200,146,42,0.1);
  position:relative;z-index:1;
}

.powered-badge{
  display:flex;align-items:center;gap:10px;
  background:rgba(200,146,42,0.06);
  border:1px solid rgba(200,146,42,0.14);
  border-radius:10px;padding:10px 12px;
}

.powered-icon{
  width:30px;height:30px;background:rgba(200,146,42,0.15);
  border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
}

.powered-icon svg{width:15px;height:15px;stroke:var(--gold-lt);stroke-width:1.5;fill:none}

.powered-text small{
  display:block;font-size:9px;letter-spacing:0.12em;
  color:var(--text-faint);text-transform:uppercase;margin-bottom:2px;
}

.powered-text strong{
  font-size:12px;color:var(--gold-lt);font-weight:500;
}

/* ═══════════════ CHAT AREA ═══════════════ */
.chat-area{
  flex:1;display:flex;flex-direction:column;
  background:var(--cream);position:relative;overflow:hidden;
}

.chat-bg{
  position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(ellipse 60% 50% at 15% 85%, rgba(200,146,42,0.05) 0%, transparent 100%),
    radial-gradient(ellipse 40% 30% at 85% 15%, rgba(200,146,42,0.04) 0%, transparent 100%);
}

.chat-header{
  padding:16px 36px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  position:relative;z-index:10;
  background:rgba(250,246,238,0.85);
  backdrop-filter:blur(12px);
}

.chat-header-left{display:flex;align-items:center;gap:10px}

.status-dot{
  width:7px;height:7px;background:var(--gold);
  border-radius:50%;animation:glow 2.5s ease-in-out infinite;
}

@keyframes glow{
  0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(200,146,42,0.4)}
  50%{opacity:0.6;box-shadow:0 0 0 4px rgba(200,146,42,0)}
}

.chat-header-title{
  font-family:'Cormorant Garamond',serif;
  font-size:15px;font-weight:500;
  color:var(--text-muted);letter-spacing:0.04em;
}

.new-chat-btn{
  display:flex;align-items:center;gap:6px;
  font-size:12px;font-weight:400;color:var(--text-faint);
  background:transparent;border:1px solid var(--border);
  border-radius:8px;padding:6px 12px;cursor:pointer;
  transition:all 0.15s ease;font-family:'DM Sans',sans-serif;
}

.new-chat-btn:hover{border-color:var(--border-mid);color:var(--text-muted)}

.new-chat-btn svg{width:13px;height:13px;stroke:currentColor;stroke-width:1.5;fill:none}

.messages{
  flex:1;overflow-y:auto;
  padding:36px 44px;
  display:flex;flex-direction:column;gap:22px;
  position:relative;z-index:1;
}

.messages::-webkit-scrollbar{width:3px}
.messages::-webkit-scrollbar-track{background:transparent}
.messages::-webkit-scrollbar-thumb{background:rgba(200,146,42,0.18);border-radius:2px}

.msg{
  display:flex;gap:13px;
  max-width:740px;
  animation:slideUp 0.28s ease;
}

@keyframes slideUp{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1;transform:translateY(0)}
}

.msg.user{flex-direction:row-reverse;align-self:flex-end;max-width:580px}

.msg-avatar{
  width:34px;height:34px;border-radius:10px;
  flex-shrink:0;display:flex;align-items:center;
  justify-content:center;margin-top:3px;
}

.msg-avatar.ai-av{
  background:var(--brown-panel);
  border:1px solid rgba(200,146,42,0.22);
}

.msg-avatar.user-av{
  background:linear-gradient(135deg,var(--gold) 0%,#A06E1A 100%);
}

.msg-avatar svg{width:16px;height:16px;stroke-width:1.5;fill:none;stroke-linecap:round;stroke-linejoin:round}

.msg-avatar.ai-av svg{stroke:var(--gold-lt)}
.msg-avatar.user-av svg{stroke:rgba(255,255,255,0.9)}

.msg-body{flex:1;min-width:0;} /* min-width 0 fixes overflow in flex */

.msg-name{
  font-size:11px;font-weight:500;letter-spacing:0.06em;
  color:var(--text-faint);margin-bottom:6px;text-transform:uppercase;
}

.msg.user .msg-name{text-align:right}

.bubble{
  padding:14px 18px;font-size:14.5px;line-height:1.68;
}

.ai-bubble{
  background:white;
  border:1px solid rgba(200,146,42,0.15);
  border-radius:3px 16px 16px 16px;
  color:var(--text-main);
  box-shadow:0 1px 4px rgba(28,13,4,0.06);
}

/* AI Markdown Styles */
.ai-bubble p { margin-bottom: 0.75em; }
.ai-bubble p:last-child { margin-bottom: 0; }
.ai-bubble h1, .ai-bubble h2, .ai-bubble h3 { font-family: 'Cormorant Garamond', serif; font-weight: 600; color: var(--gold); margin-top: 1.2em; margin-bottom: 0.5em; }
.ai-bubble h3 { font-size: 1.15rem; }
.ai-bubble ul { list-style-type: none; padding-left: 0.5rem; margin-bottom: 0.75em; }
.ai-bubble ul li { position: relative; padding-left: 1.2rem; margin-bottom: 0.25rem; }
.ai-bubble ul li::before { content: "✦"; position: absolute; left: 0; color: var(--gold); font-size: 0.8em; top: 0.15em; }
.ai-bubble ol { padding-left: 1.2rem; margin-bottom: 0.75em; }
.ai-bubble ol li { margin-bottom: 0.25rem; padding-left: 0.5rem; }
.ai-bubble ol li::marker { color: var(--gold); font-weight: bold; }
.ai-bubble strong{ font-weight:600;color:var(--gold-lt); }

.user-bubble{
  background:var(--brown-panel);
  border-radius:16px 3px 16px 16px;
  color:#ECD8B0;font-size:14px;
  border:1px solid rgba(200,146,42,0.12);
}

.msg-time{
  font-size:10.5px;color:var(--text-faint);
  margin-top:5px;padding:0 2px;
}

.msg.user .msg-time{text-align:right}

/* ── INPUT AREA ── */
.input-area{
  padding:18px 44px 24px;
  background:rgba(250,246,238,0.92);
  backdrop-filter:blur(12px);
  border-top:1px solid var(--border);
  position:relative;z-index:10;
}

@media (max-width: 768px) {
  .chat-header { padding: 12px 20px; }
  .messages { padding: 20px; }
  .input-area { padding: 12px 20px 20px; }
}

.input-box{
  display:flex;align-items:flex-end;gap:12px;
  background:white;
  border:1px solid var(--border-mid);
  border-radius:18px;
  padding:13px 16px;
  transition:border-color 0.2s,box-shadow 0.2s;
  box-shadow:0 2px 10px rgba(28,13,4,0.05);
}

.input-box:focus-within{
  border-color:var(--gold);
  box-shadow:0 2px 16px rgba(200,146,42,0.14);
}

.input-field{
  flex:1;border:none;outline:none;background:transparent;
  font-family:'DM Sans',sans-serif;font-size:14.5px;
  color:var(--text-main);resize:none;
  max-height:120px;min-height:22px;line-height:1.55;
  font-weight:400;
}

.input-field::placeholder{color:var(--text-faint);font-style:italic}

.send-btn{
  width:38px;height:38px;background:var(--gold);
  border:none;border-radius:11px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;transition:all 0.18s ease;
}

.send-btn:hover{background:var(--gold-lt);transform:scale(1.06)}
.send-btn:active{transform:scale(0.96)}

.send-btn svg{
  width:15px;height:15px;stroke:white;
  stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round;
}

.input-footer{
  font-size:10.5px;color:var(--text-faint);
  text-align:center;margin-top:10px;letter-spacing:0.02em;
}

/* typing indicator */
.typing-dot{
  display:inline-block;width:6px;height:6px;background:var(--gold);
  border-radius:50%;margin:0 2px;animation:typing 1.2s ease-in-out infinite;
}
.typing-dot:nth-child(2){animation-delay:0.2s}
.typing-dot:nth-child(3){animation-delay:0.4s}
@keyframes typing{
  0%,60%,100%{transform:translateY(0);opacity:0.3}
  30%{transform:translateY(-5px);opacity:1}
}
</style>

<div class="ai-app">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar">
    <div class="sidebar-bg"></div>
    <div class="sidebar-pattern"></div>

    <div class="sidebar-top">
      <div class="ornament">
        <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="36" cy="36" r="34" stroke="rgba(200,146,42,0.2)" stroke-width="0.5"/>
          <circle cx="36" cy="36" r="27" stroke="rgba(200,146,42,0.15)" stroke-width="0.5"/>
          <!-- 8-pointed star via two rotated squares -->
          <rect x="18" y="18" width="36" height="36" stroke="rgba(200,146,42,0.45)" stroke-width="0.8" fill="rgba(200,146,42,0.04)" transform="rotate(0 36 36)"/>
          <rect x="18" y="18" width="36" height="36" stroke="rgba(200,146,42,0.45)" stroke-width="0.8" fill="rgba(200,146,42,0.04)" transform="rotate(45 36 36)"/>
          <!-- 8 outer petals -->
          <circle cx="36" cy="10" r="3" fill="rgba(200,146,42,0.25)"/>
          <circle cx="36" cy="62" r="3" fill="rgba(200,146,42,0.25)"/>
          <circle cx="10" cy="36" r="3" fill="rgba(200,146,42,0.25)"/>
          <circle cx="62" cy="36" r="3" fill="rgba(200,146,42,0.25)"/>
          <circle cx="15.5" cy="15.5" r="2.5" fill="rgba(200,146,42,0.2)"/>
          <circle cx="56.5" cy="15.5" r="2.5" fill="rgba(200,146,42,0.2)"/>
          <circle cx="15.5" cy="56.5" r="2.5" fill="rgba(200,146,42,0.2)"/>
          <circle cx="56.5" cy="56.5" r="2.5" fill="rgba(200,146,42,0.2)"/>
          <!-- center -->
          <circle cx="36" cy="36" r="5" fill="rgba(200,146,42,0.15)" stroke="rgba(200,146,42,0.5)" stroke-width="0.8"/>
          <circle cx="36" cy="36" r="2.5" fill="rgba(200,146,42,0.7)"/>
        </svg>
      </div>

      <div class="brand">
        <div class="brand-name">Silk Road <span>AI</span></div>
        <div class="brand-tagline">Virtual Sayohat Gidi</div>
        <p class="brand-desc">O'zbekistonning istalgan burchagi bo'yicha sayohat rejalarini tuzing, tarixiy faktlarni o'rganing va eng yaxshi joylarni toping.</p>
      </div>
    </div>

    <div class="divider"></div>
    <div class="section-label">Tayyor so'rovlar</div>

    <div class="prompt-list">
      <div class="prompt-item" onclick="sendPromptItem('Samarqandga 2 kunlik oilaviy sayohat reja tuzing')">
        <div class="prompt-icon">
          <svg viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <div class="prompt-text">
          <strong>Samarqandga 2 kunlik reja</strong>
          <span>Oila bilan hordiq chiqarish uchun</span>
        </div>
      </div>

      <div class="prompt-item" onclick="sendPromptItem('Buxorodagi eng yaxshi milliy taomlar restoranlarini tavsiya eting')">
        <div class="prompt-icon">
          <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM8 14s1.5 2 4 2 4-2 4-2"/><path d="M9 9h.01M15 9h.01"/></svg>
        </div>
        <div class="prompt-text">
          <strong>Buxoroda qayerda ovqatlanamiz?</strong>
          <span>Eng yaxshi restoranlar</span>
        </div>
      </div>

      <div class="prompt-item" onclick="sendPromptItem('Amir Temur tarixi haqida qisqacha va qiziqarli faktlar ayting')">
        <div class="prompt-icon">
          <svg viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
        </div>
        <div class="prompt-text">
          <strong>Amir Temur tarixi</strong>
          <span>Qisqacha va qiziqarli faktlar</span>
        </div>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="powered-badge">
        <div class="powered-icon">
          <svg viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="powered-text">
          <small>Powered by</small>
          <strong>Groq AI · Llama 3.3</strong>
        </div>
      </div>
    </div>
  </aside>

  <!-- ═══ CHAT AREA ═══ -->
  <main class="chat-area">
    <div class="chat-bg"></div>

    <div class="chat-header">
      <div class="chat-header-left">
        <div class="status-dot"></div>
        <span class="chat-header-title">Yangi suhbat</span>
      </div>
      <button class="new-chat-btn" onclick="clearChat()">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Yangi suhbat
      </button>
    </div>

    <div class="messages" id="messages">
      <div class="msg" id="welcome">
        <div class="msg-avatar ai-av">
          <svg viewBox="0 0 24 24" stroke="var(--gold-lt)"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="msg-body">
          <div class="msg-name">Silk Road AI</div>
          <div class="bubble ai-bubble">
            <strong>Assalomu alaykum!</strong> Men Silk Road Explorer'ning sun'iy intellekt gidi bo'laman.<br><br>
            O'zbekiston bo'ylab sayohatingizni qanday qilib unutilmas qilishim mumkin? Samarqand, Buxoro, Xiva yoki boshqa shaharlar haqida so'rang — men yordam berishga tayyorman. ✦
          </div>
          <div class="msg-time" id="welcome-time"></div>
        </div>
      </div>
    </div>

    <div class="input-area">
      <div class="input-box">
        <textarea
          class="input-field"
          id="inputField"
          placeholder="Savolingizni yozing yoki joy nomini kiriting..."
          rows="1"
          onkeydown="handleKey(event)"
          oninput="autoResize(this)"
        ></textarea>
        <button class="send-btn" onclick="sendMessage()">
          <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2L15 22 11 13M22 2L2 9l9 4"/></svg>
        </button>
      </div>
      <p class="input-footer">Sun'iy intellekt xato qilishi mumkin · Muhim ma'lumotlarni tekshiring</p>
    </div>
  </main>
</div>

<script>
document.getElementById('welcome-time').innerText = getTime();

function getTime(){
  return new Date().toLocaleTimeString('uz',{hour:'2-digit',minute:'2-digit'});
}

function appendMsg(text, role, isRawHtml = false){
  const msgs = document.getElementById('messages');
  const isAI = role === 'ai';
  const div = document.createElement('div');
  div.className = 'msg' + (isAI ? '' : ' user');

  const avatar = `<div class="msg-avatar ${isAI?'ai-av':'user-av'}">
    ${isAI
      ? `<svg viewBox="0 0 24 24" stroke="var(--gold-lt)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>`
      : `<svg viewBox="0 0 24 24" stroke="rgba(255,255,255,0.9)" stroke-width="1.5" fill="none" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>`
    }
  </div>`;

  let formatted = text;
  if (!isRawHtml) {
      formatted = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
  }

  div.innerHTML = `
    ${isAI ? avatar : ''}
    <div class="msg-body">
      <div class="msg-name">${isAI ? 'Silk Road AI' : 'Siz'}</div>
      <div class="bubble ${isAI?'ai-bubble':'user-bubble'}">${formatted}</div>
      <div class="msg-time">${getTime()}</div>
    </div>
    ${!isAI ? avatar : ''}
  `;
  msgs.appendChild(div);
  msgs.scrollTop = msgs.scrollHeight;
}

function showTyping(){
  const msgs = document.getElementById('messages');
  const div = document.createElement('div');
  div.className = 'msg'; div.id = 'typing';
  div.innerHTML = `
    <div class="msg-avatar ai-av">
      <svg viewBox="0 0 24 24" stroke="var(--gold-lt)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
    </div>
    <div class="msg-body">
      <div class="msg-name">Silk Road AI</div>
      <div class="bubble ai-bubble" style="padding:16px 20px">
        <span class="typing-dot"></span>
        <span class="typing-dot"></span>
        <span class="typing-dot"></span>
      </div>
    </div>`;
  msgs.appendChild(div);
  msgs.scrollTop = msgs.scrollHeight;
}

function removeTyping(){
  const t = document.getElementById('typing');
  if(t) t.remove();
}

async function sendMessage(){
  const field = document.getElementById('inputField');
  const text = field.value.trim();
  if(!text) return;
  
  appendMsg(text, 'user');
  field.value = '';
  field.style.height = 'auto';
  showTyping();

  try {
      const res = await fetch('<?= BASE_URL ?>public/api/ai_chat.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ query: text })
      });
      
      const data = await res.json();
      removeTyping();
      
      if (data.error) {
          appendMsg(`<span style="color:red">Xatolik:</span> ${data.message}`, 'ai', true);
      } else {
          const parsedHtml = typeof marked !== 'undefined' ? marked.parse(data.reply) : data.reply;
          appendMsg(parsedHtml, 'ai', true);
      }
  } catch (err) {
      removeTyping();
      appendMsg(`<span style="color:red">Xatolik:</span> Server bilan ulanishda muammo yuz berdi.`, 'ai', true);
  }
}

function sendPromptItem(text){
  const field = document.getElementById('inputField');
  field.value = text;
  autoResize(field);
  field.focus();
}

function handleKey(e){
  if(e.key==='Enter' && !e.shiftKey){e.preventDefault();sendMessage()}
}

function autoResize(el){
  el.style.height='auto';
  el.style.height=Math.min(el.scrollHeight,120)+'px';
}

function clearChat(){
  const msgs = document.getElementById('messages');
  msgs.innerHTML = `
    <div class="msg" id="welcome">
      <div class="msg-avatar ai-av">
        <svg viewBox="0 0 24 24" stroke="var(--gold-lt)" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
      </div>
      <div class="msg-body">
        <div class="msg-name">Silk Road AI</div>
        <div class="bubble ai-bubble"><strong>Assalomu alaykum!</strong> Men Silk Road Explorer'ning sun'iy intellekt gidi bo'laman.<br><br>O'zbekiston bo'ylab sayohatingizni qanday qilib unutilmas qilishim mumkin? ✦</div>
        <div class="msg-time">${getTime()}</div>
      </div>
    </div>`;
}
</script>

<?php require_once 'includes/layout_footer.php'; ?>
