<template>
  <header class="header">
    <div class="header-left">
      <div class="logo">Task<span>Flow</span></div>
      <div class="logo-sub">Task Management API</div>
    </div>
    <div class="header-right">
      <div class="live-dot"></div>
      <span class="live-label">Laravel API</span>
    </div>
  </header>
</template>

<style scoped>
.header {
  position: sticky; top: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 36px;
  height: 60px;
  background: rgba(8, 8, 16, 0.88);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--border);
}

.header-left { display: flex; align-items: baseline; gap: 14px; }

.logo {
  font-family: var(--font-ui);
  font-size: 1.25rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: var(--text);
}
.logo span { color: var(--accent); }

.logo-sub {
  font-size: 0.65rem;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--muted);
}

.header-right {
  display: flex; align-items: center; gap: 8px;
}

.live-dot {
  width: 7px; height: 7px;
  background: var(--low);
  border-radius: 50%;
  animation: pulse 2s ease infinite;
}

.live-label {
  font-size: 0.68rem;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
}
</style>