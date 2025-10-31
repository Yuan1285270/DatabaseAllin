<!-- 🤖 AI 小視窗元件 -->
<iframe id="chat-frame" src="http://140.134.53.57/~D1285270/chat.php"></iframe>
<button id="chat-button" onclick="toggleChat()">💬</button>

<style>
  #chat-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #1976d2;
    color: white;
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    font-size: 24px;
    cursor: pointer;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  #chat-frame {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 380px;
    height: 500px;
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    z-index: 9998;
  }

  @media (max-width: 500px) {
    #chat-frame {
      width: 95vw;
      height: 90vh;
      bottom: 5vh;
      right: 2.5vw;
    }
  }
</style>

<script>
let chatOpen = false;
function toggleChat() {
  const iframe = document.getElementById("chat-frame");
  if (chatOpen) {
    iframe.style.display = "none";
  } else {
    iframe.style.display = "block";
  }
  chatOpen = !chatOpen;
}
</script>
