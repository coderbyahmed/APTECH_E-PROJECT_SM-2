<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — SOUND Group</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif; background: linear-gradient(160deg, #0d0d14 0%, #1a0a2e 35%, #2d1052 65%, #1e0838 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; -webkit-font-smoothing: antialiased; position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; top: -20%; left: -15%; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, rgba(168,85,247,0.15) 0%, transparent 70%); pointer-events: none; }
        body::after { content: ''; position: absolute; bottom: -25%; right: -10%; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(236,72,153,0.12) 0%, transparent 70%); pointer-events: none; }
        .error-container { position: relative; z-index: 2; text-align: center; max-width: 460px; padding: 2rem; }
        .error-code { font-size: 8rem; font-weight: 800; line-height: 1; letter-spacing: -0.04em; background: linear-gradient(135deg, rgba(168,85,247,0.4) 0%, rgba(139,92,246,0.2) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1rem; }
        .error-icon { display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; border-radius: 20px; background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.2); margin-bottom: 1.5rem; }
        .error-icon svg { width: 36px; height: 36px; color: #fbbf24; }
        .error-heading { font-size: 1.75rem; font-weight: 700; color: #ffffff; letter-spacing: -0.025em; margin-bottom: 0.75rem; line-height: 1.2; }
        .error-desc { font-size: 0.9375rem; color: rgba(196,181,225,0.6); line-height: 1.7; margin-bottom: 2.5rem; }
        .error-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; border-radius: 12px; border: none; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%); color: #ffffff; font-family: inherit; font-size: 0.9375rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(124,58,237,0.25); }
        .error-btn:hover { box-shadow: 0 4px 16px rgba(124,58,237,0.35); transform: translateY(-1px); }
        .error-btn:active { transform: translateY(0); }
        .error-btn svg { width: 18px; height: 18px; }
        .error-footer { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899, #a855f7, #7c3aed); background-size: 200% 100%; opacity: 0.6; }
        @media (max-width: 768px) { .error-code { font-size: 5rem; } .error-heading { font-size: 1.375rem; } .error-desc { font-size: 0.875rem; } .error-icon { width: 60px; height: 60px; border-radius: 16px; } .error-icon svg { width: 28px; height: 28px; } }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </div>
        <h1 class="error-heading">Page Not Found</h1>
        <p class="error-desc">The page you're looking for doesn't exist or may have been moved.</p>
        <button onclick="goBack()" class="error-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Go Back
        </button>
    </div>
    <div class="error-footer"></div>
    <script>
        function goBack() {
            if (window.history.length > 1) { window.history.back(); } else { window.location.href = 'frontend/admin/authentication/login.php'; }
        }
    </script>
</body>
</html>
