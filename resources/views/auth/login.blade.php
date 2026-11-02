<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | Davao Medcare</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><style>
*{box-sizing:border-box;margin:0;padding:0}
:root{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
body{font-family:'Inter',sans-serif;min-height:100vh;display:grid;place-items:center;padding:24px;color:#0f2230;position:relative;overflow:hidden;background: linear-gradient(145deg,#0b1f2e 0%,#0e2f3f 50%,#0a1e2c 100%)}
/* Animated mesh background */
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 90% 60% at 15% 10%,rgba(20,184,154,.22) 0%,transparent 55%),radial-gradient(ellipse 70% 50% at 85% 80%,rgba(14,90,160,.18) 0%,transparent 55%),radial-gradient(ellipse 50% 40% at 50% 50%,rgba(20,184,154,.07) 0%,transparent 60%);pointer-events:none}
/* Floating orbs */
body::after{content:'';position:fixed;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(20,184,154,.08) 0%,transparent 70%);top:-200px;right:-200px;pointer-events:none;animation:float 12s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(30px) scale(1.05)}}
/* Login card */
.login{position:relative;width:min(100%,430px);background:rgba(255,255,255,.06);backdrop-filter:blur(28px) saturate(1.4);-webkit-backdrop-filter:blur(28px) saturate(1.4);border:1px solid rgba(255,255,255,.12);border-radius:22px;padding:40px;box-shadow:0 0 0 1px rgba(20,184,154,.08) inset,0 8px 40px rgba(0,0,0,.4),0 40px 100px rgba(0,0,0,.2);color:#e8f4f4}
/* Top gloss line */
.login::before{content:'';position:absolute;top:0;left:20%;right:20%;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);border-radius:99px}
.brand{display:flex;align-items:center;gap:14px;margin-bottom:32px}
.mark{width:44px;height:44px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(135deg,#1dd6b8 0%,#0fa887 100%);color:#fff;font-weight:800;font-size:18px;letter-spacing:-0.04em;box-shadow:0 0 0 3px rgba(20,184,154,.2),0 6px 22px rgba(20,184,154,.45);flex-shrink:0}
.brand strong{font-size:15px;font-weight:700;letter-spacing:-0.025em;color:#e8f4f4}
.brand small{display:block;color:rgba(180,215,210,.5);margin-top:3px;font-size:11.5px;font-weight:400}
.eyebrow{font-size:9px;text-transform:uppercase;letter-spacing:2px;color:rgba(130,195,185,.5);font-weight:700}
h1{font-size:27px;font-weight:760;letter-spacing:-0.05em;margin:7px 0 5px;color:#fff}
.muted{color:rgba(160,205,205,.6);font-size:12.5px;line-height:1.5}
.field{display:grid;gap:7px;margin-top:20px}
.field label{font-size:11px;font-weight:700;letter-spacing:0.2px;color:rgba(180,215,210,.65);text-transform:uppercase;letter-spacing:.8px}
input{border:1.5px solid rgba(255,255,255,.1);border-radius:10px;padding:12px 14px;background:rgba(255,255,255,.07);font:inherit;font-size:13px;color:#e8f4f4;transition:border-color .15s,box-shadow .15s,background .15s;width:100%}
input::placeholder{color:rgba(160,205,200,.35)}
input:hover{border-color:rgba(255,255,255,.18);background:rgba(255,255,255,.1)}
input:focus{outline:none;border-color:rgba(20,184,154,.7);background:rgba(20,184,154,.06);box-shadow:0 0 0 3.5px rgba(20,184,154,.15)}
.btn{width:100%;border:none;background:linear-gradient(135deg,#0fa887 0%,#12c49e 55%,#14d4aa 100%);color:#fff;border-radius:11px;padding:13px;font-size:13.5px;font-weight:680;margin-top:26px;cursor:pointer;font-family:inherit;letter-spacing:-0.015em;box-shadow:0 4px 20px rgba(20,184,154,.4);transition:transform .2s cubic-bezier(.34,1.56,.64,1),box-shadow .2s ease}
.btn:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(20,184,154,.55)}
.btn:active{transform:translateY(0);box-shadow:0 3px 12px rgba(20,184,154,.3)}
.error{font-size:12px;color:#f08080;background:rgba(220,60,55,.15);border:1px solid rgba(220,60,55,.25);border-radius:9px;padding:10px 13px;margin-top:12px;line-height:1.4}
::-webkit-scrollbar{width:4px}::-webkit-scrollbar-thumb{background:rgba(20,184,154,.3);border-radius:99px}
</style></head><body><main class="login"><div class="brand"><img src="/images/logo.png" alt="Davao MedCare" style="width:44px;height:44px;border-radius:12px;object-fit:cover;box-shadow:0 0 0 3px rgba(20,184,154,.2),0 6px 22px rgba(20,184,154,.45);flex-shrink:0"><div><strong>Davao Medcare</strong><small>Pharma & Supplies Distribution</small></div></div><div class="eyebrow">Operations system</div><h1>Sign in</h1><p class="muted">Use your authorized staff account to continue.</p>@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif<form method="post" action="{{ route('login') }}">@csrf<div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" autocomplete="username" required autofocus value="{{ old('email') }}"></div><div class="field"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div><button class="btn">Sign in</button></form></main></body></html>
