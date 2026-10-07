(function(){
var b=document.getElementById('burger'),m=document.getElementById('mnav');
if(b&&m){b.addEventListener('click',function(){var o=m.classList.toggle('open');b.setAttribute('aria-expanded',o);});m.addEventListener('click',function(e){if(e.target.tagName==='A')m.classList.remove('open');});}
var hrs=document.getElementById('rut-h');
if(hrs){var n=2,rate=449,minus=document.getElementById('rut-m'),plus=document.getElementById('rut-p');
function u(){var t=n*rate;hrs.textContent=n;document.getElementById('rut-b').textContent=t.toLocaleString('sv-SE')+' kr';document.getElementById('rut-d').textContent='−'+(t/2).toLocaleString('sv-SE')+' kr';document.getElementById('rut-a').textContent=(t/2).toLocaleString('sv-SE')+' kr';}
minus.addEventListener('click',function(){n=Math.max(1,n-1);u();});plus.addEventListener('click',function(){n=Math.min(12,n+1);u();});u();}
var f=document.getElementById('contact-form');
if(f){try{var p=new URLSearchParams(location.search).get('tjanst');if(p){var s=f.querySelector('[name=service]');for(var i=0;i<s.options.length;i++){if(s.options[i].value.toLowerCase()===p.toLowerCase()){s.selectedIndex=i;break;}}}}catch(e){}
function toast(t,bad){var o=document.querySelector('.toast');if(o)o.remove();var d=document.createElement('div');d.className='toast'+(bad?' bad':'');d.textContent=t;document.body.appendChild(d);setTimeout(function(){d.remove();},5000);}
f.addEventListener('submit',function(e){e.preventDefault();if(f.querySelector('[name=_gotcha]').value)return;var ok=true;['name','phone','email'].forEach(function(k){var el=f.querySelector('[name='+k+']');el.classList.remove('err');if(!el.value.trim()||(k==='email'&&el.value.indexOf('@')<0)||(k==='phone'&&el.value.trim().length<6)){el.classList.add('err');ok=false;}});
var g=f.querySelector('[name=gdpr]');if(!g.checked){ok=false;g.focus();}if(!ok){toast('Fyll i namn, telefon, e-post och godkänn integritetspolicyn.',true);return;}
var btn=f.querySelector('button[type=submit]'),txt=btn.textContent;btn.disabled=true;btn.textContent='Skickar…';
fetch(f.action,{method:'POST',body:new FormData(f),headers:{Accept:'application/json'}}).then(function(r){return r.ok?r.json():Promise.reject();}).then(function(){toast('Tack! Vi har tagit emot din förfrågan och återkommer inom 24 timmar.');f.reset();}).catch(function(){toast('Något gick fel. Ring oss gärna på 010 300 10 32.',true);}).finally(function(){btn.disabled=false;btn.textContent=txt;});});}
})();
(function(){
var c=document.getElementById('cmp');if(!c)return;
var r=c.querySelector('input'),aft=c.querySelector('.aft'),hnd=c.querySelector('.hnd'),bef=c.querySelector('.bef'),cap=document.getElementById('ba-cap');
function set(v){aft.style.clipPath='inset(0 0 0 '+v+'%)';hnd.style.left=v+'%';}
r.addEventListener('input',function(){set(r.value);});set(50);
document.querySelectorAll('.thumbs button').forEach(function(b){b.addEventListener('click',function(){document.querySelectorAll('.thumbs button').forEach(function(x){x.classList.remove('on');});b.classList.add('on');bef.src=b.dataset.b;aft.src=b.dataset.a;bef.alt='Före: '+b.dataset.t;aft.alt='Efter: '+b.dataset.t;if(cap)cap.textContent=b.dataset.t;r.value=50;set(50);});});
})();
(function(){
var items=[].slice.call(document.querySelectorAll('[data-lb]'));if(!items.length)return;
var lb=document.createElement('div');lb.className='lb';lb.innerHTML='<button class="lb-x" aria-label="Stäng">×</button><button class="lb-p" aria-label="Föregående">‹</button><button class="lb-n" aria-label="Nästa">›</button><div class="lb-c"><div class="lb-m"></div><div class="lb-cap"></div></div>';document.body.appendChild(lb);
var m=lb.querySelector('.lb-m'),cap=lb.querySelector('.lb-cap'),cur=0;
function vis(){return items.filter(function(a){return a.offsetParent!==null;});}
function show(i){var list=vis();if(!list.length)return;cur=(i+list.length)%list.length;var a=list[cur];m.innerHTML='';
if(a.dataset.lb==='video'){var v=document.createElement('video');v.src=a.getAttribute('href');v.controls=true;v.autoplay=true;v.muted=true;v.loop=true;v.playsInline=true;v.setAttribute('playsinline','');var pi=a.querySelector('img');if(pi)v.poster=pi.currentSrc||pi.src;m.appendChild(v);}
else{var im=document.createElement('img');im.src=a.getAttribute('href');im.alt=a.dataset.cap||'';m.appendChild(im);}
cap.textContent=a.dataset.cap||'';lb.classList.add('open');document.body.classList.add('lb-lock');}
function close(){lb.classList.remove('open');document.body.classList.remove('lb-lock');m.innerHTML='';}
items.forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();show(vis().indexOf(a));});});
lb.querySelector('.lb-x').addEventListener('click',close);lb.querySelector('.lb-p').addEventListener('click',function(){show(cur-1);});lb.querySelector('.lb-n').addEventListener('click',function(){show(cur+1);});
lb.addEventListener('click',function(e){if(e.target===lb)close();});
document.addEventListener('keydown',function(e){if(!lb.classList.contains('open'))return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')show(cur-1);if(e.key==='ArrowRight')show(cur+1);});
var tabs=document.querySelectorAll('.gtabs button');
tabs.forEach(function(b){b.addEventListener('click',function(){tabs.forEach(function(x){x.classList.remove('on');});b.classList.add('on');var f=b.dataset.f;document.querySelectorAll('.g-item').forEach(function(g){g.classList.toggle('hide',!(f==='alla'||g.dataset.cat===f||(f==='fore-efter'&&g.dataset.ba==='1')||(f==='video'&&g.dataset.lb==='video')));});});});
if(location.hash==='#video'){var vb=document.querySelector('.gtabs [data-f=video]');if(vb)vb.click();}
})();
(function(){
var rm=matchMedia('(prefers-reduced-motion: reduce)').matches,io='IntersectionObserver' in window;
var els=document.querySelectorAll('.card,.why>div,.step,.plan,.ts,.ba-item,.g-item,.reel-item,.box,.split,.cta,.two .pbox,.rel a,.mg-item,.pt,.faq details');
if(io&&!rm){var ob=new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){x.target.classList.add('in');ob.unobserve(x.target);}});},{rootMargin:'0px 0px -6% 0px'});els.forEach(function(e){var k=0,s=e.previousElementSibling;while(s&&k<8){k++;s=s.previousElementSibling;}e.style.transitionDelay=(k%8)*60+'ms';e.classList.add('rv');ob.observe(e);});}
var st=document.querySelectorAll('.stats b');
if(st.length&&io&&!rm){var so=new IntersectionObserver(function(en){en.forEach(function(x){if(!x.isIntersecting)return;so.unobserve(x.target);var el=x.target,m=el.textContent.match(/^([\d\s]+)(.*)$/);if(!m)return;var n=parseInt(m[1].replace(/\s/g,''),10),suf=m[2],t0=performance.now(),d=1500;(function f(t){var p=Math.min(1,(t-t0)/d);p=1-Math.pow(1-p,3);el.textContent=Math.round(n*p).toLocaleString('sv-SE')+suf;if(p<1)requestAnimationFrame(f);})(t0);});},{threshold:.5});st.forEach(function(b){so.observe(b);});}
var h=document.querySelector('.hdr');function hs(){if(h)h.classList.toggle('sc',scrollY>20);}addEventListener('scroll',hs,{passive:true});hs();
document.querySelectorAll('video[autoplay]').forEach(function(v){v.muted=true;v.defaultMuted=true;var p=v.play();if(p&&p.catch)p.catch(function(){});if(io)new IntersectionObserver(function(en){en.forEach(function(x){if(x.isIntersecting){var q=v.play();if(q&&q.catch)q.catch(function(){});}else v.pause();});},{threshold:.05}).observe(v);});
document.addEventListener('visibilitychange',function(){if(!document.hidden)document.querySelectorAll('video[autoplay]').forEach(function(v){var q=v.play();if(q&&q.catch)q.catch(function(){});});});
if(!rm&&matchMedia('(hover:hover)').matches){document.querySelectorAll('a[data-lb=video]').forEach(function(a){var v,t;a.addEventListener('mouseenter',function(){t=setTimeout(function(){if(v||a.querySelector('video'))return;v=document.createElement('video');v.src=a.getAttribute('href');v.muted=true;v.loop=true;v.playsInline=true;v.preload='metadata';v.className='pv';a.appendChild(v);var q=v.play();if(q&&q.catch)q.catch(function(){});},150);});a.addEventListener('mouseleave',function(){clearTimeout(t);if(v){v.pause();v.remove();v=null;}});});}
var bt=document.createElement('button');bt.className='totop';bt.type='button';bt.setAttribute('aria-label','Till toppen');bt.textContent='↑';document.body.appendChild(bt);bt.addEventListener('click',function(){scrollTo({top:0,behavior:rm?'auto':'smooth'});});addEventListener('scroll',function(){bt.classList.toggle('show',scrollY>700);},{passive:true});
var lb=document.querySelector('.lb');if(lb){var sx=0;lb.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;},{passive:true});lb.addEventListener('touchend',function(e){var dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>60)lb.querySelector(dx<0?'.lb-n':'.lb-p').click();});}
var hf=document.querySelector('.hf-bg');if(hf&&!rm&&matchMedia('(min-width:900px)').matches){addEventListener('scroll',function(){var y=scrollY;if(y<innerHeight)hf.style.transform='translateY('+(y*.25)+'px) scale(1.06)';},{passive:true});hf.style.transform='scale(1.06)';}
})();
(function(){
var vids=[].slice.call(document.querySelectorAll('video.av'));
if(vids.length&&'IntersectionObserver' in window){var io=new IntersectionObserver(function(en){en.forEach(function(x){var v=x.target,a=v.parentNode;if(x.isIntersecting){if(!v.src){v.src=v.dataset.src;v.load();}var p=v.play();if(p&&p.catch)p.catch(function(){});if(a)a.classList.add('playing');}else{v.pause();if(a)a.classList.remove('playing');}});},{rootMargin:'120px 0px',threshold:.15});vids.forEach(function(v){io.observe(v);});}
else vids.forEach(function(v){v.src=v.dataset.src;v.play();});
document.querySelectorAll('.macc').forEach(function(b){b.addEventListener('click',function(){var o=b.nextElementSibling.classList.toggle('open');b.setAttribute('aria-expanded',o);});});
document.querySelectorAll('.dd>a').forEach(function(a){a.addEventListener('click',function(e){if(matchMedia('(hover:none)').matches&&!a.parentNode.classList.contains('op')){e.preventDefault();a.parentNode.classList.add('op');a.setAttribute('aria-expanded','true');}});});
})();
