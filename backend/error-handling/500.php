<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Something Went Wrong — SOUND Group</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif; background: linear-gradient(160deg, #0d0d14 0%, #1a0a2e 35%, #2d1052 65%, #1e0838 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; -webkit-font-smoothing: antialiased; position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; top: -20%; left: -15%; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, rgba(168,85,247,0.15) 0%, transparent 70%); pointer-events: none; }
        body::after { content: ''; position: absolute; bottom: -25%; right: -10%; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(236,72,153,0.12) 0%, transparent 70%); pointer-events: none; }
        .error-container { position: relative; z-index: 2; text-align: center; max-width: 460px; padding: 2rem; }
        .error-code { font-size: 8rem; font-weight: 800; line-height: 1; letter-spacing: -0.04em; background: linear-gradient(135deg, rgba(168,85,247,0.4) 0%, rgba(139,92,246,0.2) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1rem; }
        .error-icon { display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; border-radius: 20px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.2); margin-bottom: 1.5rem; }
        .error-icon svg { width: 36px; height: 36px; color: #f87171; }
        .error-heading { font-size: 1.75rem; font-weight: 700; color: #ffffff; letter-spacing: -0.025em; margin-bottom: 0.75rem; line-height: 1.2; }
        .error-desc { font-size: 0.9375rem; color: rgba(196,181,225,0.6); line-height: 1.7; margin-bottom: 2.5rem; }
        .error-btn-group { display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap; }
        .error-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%); color: #ffffff; font-family: inherit; font-size: 0.9375rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(124,58,237,0.25); }
        .error-btn:hover { box-shadow: 0 4px 16px rgba(124,58,237,0.35); transform: translateY(-1px); }
        .error-btn:active { transform: translateY(0); }
        .error-btn svg { width: 18px; height: 18px; }
        .error-btn--secondary { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); box-shadow: none; }
        .error-btn--secondary:hover { background: rgba(255,255,255,0.12); box-shadow: none; }
        .error-footer { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899, #a855f7, #7c3aed); background-size: 200% 100%; opacity: 0.6; }
        @media (max-width: 768px) { .error-code { font-size: 5rem; } .error-heading { font-size: 1.375rem; } .error-desc { font-size: 0.875rem; } .error-icon { width: 60px; height: 60px; border-radius: 16px; } .error-icon svg { width: 28px; height: 28px; } .error-btn-group { flex-direction: column; } .error-btn { width: 100%; justify-content: center; } }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h1 class="error-heading">Something Went Wrong</h1>
        <p class="error-desc">We're having trouble processing your request right now. Please try again in a moment.</p>
        <div class="error-btn-group">
            <button onclick="window.location.reload()" class="error-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                Try Again
            </button>
            <button onclick="goBack()" class="error-btn error-btn--secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Go Back
            </button>
        </div>
    </div>
    <div class="error-footer"></div>
    <script>
        function goBack() {
            if (window.history.length > 1) { window.history.back(); } else { window.location.href = '/Aptech_E_Project_02/sound_management/frontend/admin/authentication/login.php'; }
        }
    </script>
</body>
</html>
