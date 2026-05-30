<?php
$pageTitle = 'Travel – The Riyadh Dispatch';
require_once __DIR__ . '/../../includes/header.php';
?>

<main class="main-content page-narrow">
  <div class="page-header">
    <div class="post-card-meta" style="margin-bottom:10px">
      <span class="post-date">May 18, 2026</span>
      <span class="post-tag">Travel</span>
    </div>
    <h1>A Weekend in Jeddah's Al-Balad District</h1>
  </div>

  <!-- DOM-Based XSS vulnerable widget -->
  <div class="welcome-widget">
    <div class="widget-header">👋 Welcome, traveller</div>
    <div id="welcome-output" class="widget-body">Loading your greeting…</div>
  </div>

  <div class="search-box" style="margin-bottom:36px;">
    <label class="input-label">Enter your name for a personalised welcome:</label>
    <div class="hash-row">
      <input type="text" id="hashInput" class="search-input" placeholder="Your name"/>
      <button class="search-btn" id="applyHash">Update</button>
    </div>
  </div>

  <article class="blog-post" style="border-top:1px solid var(--border); padding-top:32px;">
    <p>I arrived in Jeddah on a Thursday evening, just as the golden light was fading over the Red Sea corniche. Al-Balad, the old city, was already alive — families strolling, vendors selling fresh fruit, the air thick with the smell of frankincense and grilled fish.</p>
    <p>Al-Balad is a UNESCO World Heritage Site, and once you're inside it, you understand why. The architecture alone is worth the trip: centuries-old coral-stone buildings with intricately carved wooden mashrabiya screens on every window, designed to let air in and keep curious eyes out.</p>
    <blockquote>"Jeddah is the gateway to the Hijaz. But Al-Balad is the door within the door — the city that remembers."</blockquote>
    <p>I spent Saturday morning getting hopelessly lost between Sharia Al-Alawi and the old souqs. Every wrong turn reveals a crumbling wall painted with murals, a cat sleeping in a doorway, a coffee shop that hasn't changed its sign since the 1970s.</p>
    <p>If you're planning a visit, stay at least two nights. One day is not enough, and you'll want a full evening to walk the corniche after dark when the city finally cools down.</p>
  </article>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
// ⚠️ VULNERABLE: hash read and injected with innerHTML (DOM-Based XSS)
function renderGreeting() {
  const hash  = decodeURIComponent(window.location.hash.slice(1));
  const el    = document.getElementById('welcome-output');
  if (!hash) {
    el.textContent = 'Add your name to the URL hash to personalise this greeting.';
    return;
  }
  el.innerHTML = 'Hello, <strong>' + hash + '</strong>! Welcome to The Riyadh Dispatch.';
}

renderGreeting();
window.addEventListener('hashchange', renderGreeting);

document.getElementById('applyHash').addEventListener('click', function () {
  window.location.hash = encodeURIComponent(document.getElementById('hashInput').value);
});
</script>
