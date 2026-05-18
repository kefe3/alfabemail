<div id="adminChatWidget" style="display:none;">
  <!-- Chat toggle button -->
  <button id="chatToggleBtn"
    style="position:fixed;bottom:82px;right:24px;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#4f46e5);color:white;display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;box-shadow:0 6px 20px rgba(124,58,237,0.4);z-index:9997;border:none;transition:transform 0.2s;"
    onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"
    onclick="toggleChat()" title="Admin Sohbet">
    💬
  </button>

  <!-- Chat panel -->
  <div id="chatPanel"
    style="display:none;position:fixed;bottom:144px;right:24px;width:380px;max-width:calc(100vw - 48px);height:520px;max-height:calc(100vh - 200px);background:white;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.25);z-index:9999;overflow:hidden;flex-direction:column;border:2px solid #e5e7eb;">
    
    <!-- Header -->
    <div style="background:linear-gradient(135deg,#7c3aed,#4f46e5);padding:16px 20px;color:white;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
      <div>
        <span style="font-weight:800;font-size:16px;">💬 Admin Sohbet</span>
        <div id="onlineCount" style="font-size:12px;opacity:0.8;margin-top:2px;"></div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button onclick="loadAllMessages()" style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:32px;height:32px;color:white;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;" title="Yenile">🔄</button>
        <button onclick="toggleChat()" style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:32px;height:32px;color:white;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">✕</button>
      </div>
    </div>

    <!-- Online admins strip -->
    <div id="onlineAdminsStrip" style="padding:8px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;min-height:20px;"></div>

    <!-- Messages -->
    <div id="chatMessages" style="flex:1;overflow-y:auto;padding:12px 16px;background:#f3f4f6;display:flex;flex-direction:column;gap:8px;"></div>

    <!-- Input -->
    <div style="padding:12px 16px;border-top:1px solid #e5e7eb;background:white;flex-shrink:0;display:flex;gap:8px;">
      <input id="chatInput" type="text" placeholder="Mesajını yaz..." maxlength="1000"
        style="flex:1;padding:10px 14px;border:2px solid #d1d5db;border-radius:12px;font-size:14px;outline:none;color:#1a202c;"
        onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendChatMessage();}" />
      <button onclick="sendChatMessage()" id="chatSendBtn"
        style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:white;border:none;border-radius:12px;padding:10px 18px;font-size:14px;font-weight:700;cursor:pointer;transition:opacity 0.2s;white-space:nowrap;"
        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Gönder</button>
    </div>
  </div>
</div>

<script>
let chatLastId = 0;
let chatPollInterval = null;
let chatOpen = false;

function toggleChat() {
  chatOpen = !chatOpen;
  document.getElementById('chatPanel').style.display = chatOpen ? 'flex' : 'none';
  if (chatOpen) {
    loadAllMessages();
    loadOnlineAdmins();
    startChatPolling();
    setTimeout(() => {
      const msgs = document.getElementById('chatMessages');
      msgs.scrollTop = msgs.scrollHeight;
      document.getElementById('chatInput').focus();
    }, 300);
  } else {
    stopChatPolling();
  }
}

function startChatPolling() {
  stopChatPolling();
  chatPollInterval = setInterval(() => {
    pollNewMessages();
    loadOnlineAdmins();
  }, 5000);
}

function stopChatPolling() {
  if (chatPollInterval) {
    clearInterval(chatPollInterval);
    chatPollInterval = null;
  }
}

async function loadAllMessages() {
  const container = document.getElementById('chatMessages');
  container.innerHTML = '<div style="text-align:center;color:#9ca3af;padding:20px;font-size:14px;">Mesajlar yükleniyor...</div>';
  try {
    const res = await fetch('/admin-chat/messages');
    const data = await res.json();
    if (data.all_messages) {
      chatLastId = data.all_messages.length > 0 ? data.all_messages[data.all_messages.length - 1].id : 0;
      renderMessages(data.all_messages);
    }
  } catch(e) {
    container.innerHTML = '<div style="text-align:center;color:#ef4444;padding:20px;font-size:14px;">⚠️ Mesajlar yüklenemedi</div>';
  }
}

async function pollNewMessages() {
  try {
    const res = await fetch('/admin-chat/messages?last_id=' + chatLastId);
    const data = await res.json();
    if (data.messages && data.messages.length > 0) {
      chatLastId = data.messages[data.messages.length - 1].id;
      appendMessages(data.messages);
    }
  } catch(e) {}
}

async function loadOnlineAdmins() {
  try {
    const res = await fetch('/admin-chat/online');
    const data = await res.json();
    const strip = document.getElementById('onlineAdminsStrip');
    const countEl = document.getElementById('onlineCount');
    if (data.admins && data.admins.length > 0) {
      strip.innerHTML = data.admins.map(a =>
        '<span style="display:flex;align-items:center;gap:4px;font-size:13px;color:#374151;background:white;padding:4px 10px;border-radius:20px;border:1px solid #d1d5db;">' +
        '<span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;"></span>' +
        a.name +
        '</span>'
      ).join('');
      countEl.textContent = data.admins.length + ' çevrimiçi';
    } else {
      strip.innerHTML = '<span style="font-size:13px;color:#9ca3af;">Çevrimiçi admin yok</span>';
      countEl.textContent = 'Çevrimiçi yok';
    }
  } catch(e) {}
}

function renderMessages(messages) {
  const container = document.getElementById('chatMessages');
  if (messages.length === 0) {
    container.innerHTML = '<div style="text-align:center;color:#9ca3af;padding:30px 20px;font-size:14px;">💬 Henüz mesaj yok. İlk mesajı sen yaz!</div>';
    return;
  }
  container.innerHTML = messages.map(m => createMessageHtml(m)).join('');
  container.scrollTop = container.scrollHeight;
}

function appendMessages(messages) {
  const container = document.getElementById('chatMessages');
  const emptyMsg = container.querySelector('div[style*="text-align:center"]');
  if (emptyMsg) container.innerHTML = '';
  messages.forEach(m => {
    container.insertAdjacentHTML('beforeend', createMessageHtml(m));
  });
  container.scrollTop = container.scrollHeight;
}

function createMessageHtml(m) {
  const isMine = m.user_id === {{ auth()->id() }};
  const align = isMine ? 'flex-end' : 'flex-start';
  const bg = isMine ? 'background:#7c3aed;color:white;border-radius:16px 16px 4px 16px;' : 'background:white;color:#1a202c;border-radius:16px 16px 16px 4px;border:1px solid #e5e7eb;';
  const nameDisplay = isMine ? '' : '<div style="font-size:11px;color:#6b7280;margin-bottom:2px;margin-left:4px;">' + m.name + '</div>';
  return '<div style="display:flex;flex-direction:column;align-items:' + align + ';max-width:80%;">' +
    nameDisplay +
    '<div style="' + bg + 'padding:8px 14px;font-size:14px;line-height:1.4;word-wrap:break-word;">' +
    escapeHtml(m.message) +
    '</div>' +
    '<div style="font-size:10px;color:#9ca3af;margin-top:2px;' + (isMine ? 'margin-right:4px;' : 'margin-left:4px;') + '">' + m.created_at + '</div>' +
    '</div>';
}

async function sendChatMessage() {
  const input = document.getElementById('chatInput');
  const btn = document.getElementById('chatSendBtn');
  const text = input.value.trim();
  if (!text) return;

  input.disabled = true;
  btn.disabled = true;
  btn.textContent = '...';

  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch('/admin-chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf || '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ message: text })
    });
    if (res.ok) {
      input.value = '';
      await loadAllMessages();
    }
  } catch(e) {}
  finally {
    input.disabled = false;
    btn.disabled = false;
    btn.textContent = 'Gönder';
    input.focus();
  }
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}
</script>
