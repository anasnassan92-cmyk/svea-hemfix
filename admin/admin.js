(function(){
var b=document.getElementById('burger'),s=document.getElementById('side');
if(b&&s){b.addEventListener('click',function(){s.classList.toggle('open');});s.addEventListener('click',function(e){if(e.target===s)s.classList.remove('open');});}
function toast(t,bad){var o=document.querySelector('.toast');if(o)o.remove();var d=document.createElement('div');d.className='toast'+(bad?' bad':'');d.textContent=t;document.body.appendChild(d);setTimeout(function(){d.remove();},3500);}
window.toast=toast;
document.querySelectorAll('[data-confirm]').forEach(function(el){el.addEventListener('click',function(e){if(!confirm(el.getAttribute('data-confirm')))e.preventDefault();});});
document.querySelectorAll('[data-autosubmit]').forEach(function(el){el.addEventListener('change',function(){el.form.submit();});});
document.querySelectorAll('.copy').forEach(function(el){el.addEventListener('click',function(){var t=el.getAttribute('data-copy')||el.textContent;navigator.clipboard.writeText(t.trim()).then(function(){toast('Kopierat');});});});
/* quick status change via fetch */
document.querySelectorAll('select[data-status]').forEach(function(sel){sel.addEventListener('change',function(){var fd=new FormData();fd.append('_csrf',document.body.getAttribute('data-csrf'));fd.append('id',sel.getAttribute('data-status'));fd.append('status',sel.value);fetch('/admin/leads.php?ajax=status',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(j){if(j.ok){toast('Status uppdaterad');var p=sel.closest('tr')&&sel.closest('tr').querySelector('.pill');if(p){p.className='pill '+j.cls;p.textContent=j.label;}}else toast('Kunde inte spara',true);}).catch(function(){toast('Kunde inte spara',true);});});});
/* slug preview + counters in post editor */
var title=document.getElementById('p-title'),slug=document.getElementById('p-slug');
if(title&&slug){title.addEventListener('input',function(){if(slug.getAttribute('data-auto')==='1'){slug.value=title.value.toLowerCase().replace(/[åä]/g,'a').replace(/ö/g,'o').replace(/é/g,'e').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'').slice(0,90);}});slug.addEventListener('input',function(){slug.setAttribute('data-auto','0');});}
document.querySelectorAll('[data-count]').forEach(function(el){var o=document.getElementById(el.getAttribute('data-count'));function u(){o.textContent=el.value.length+' tecken';}el.addEventListener('input',u);u();});
var html=document.getElementById('p-html'),prev=document.getElementById('p-prev');
if(html&&prev){var t;html.addEventListener('input',function(){clearTimeout(t);t=setTimeout(function(){prev.innerHTML=html.value;},400);});}
document.querySelectorAll('[data-tab]').forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();var g=a.getAttribute('data-tab');document.querySelectorAll('[data-tab]').forEach(function(x){x.classList.toggle('on',x===a);});document.querySelectorAll('[data-pane]').forEach(function(p){p.style.display=p.getAttribute('data-pane')===g?'':'none';});});});
})();
/* ---------- gallery uploads ---------- */
(function(){
var drop=document.getElementById('drop'),inp=document.getElementById('up-file'),list=document.getElementById('up-list');if(!drop||!inp)return;
var csrf=function(){return document.body.getAttribute('data-csrf');};
function posterFor(file){return new Promise(function(res){if(!file.type.startsWith('video/'))return res(null);var v=document.createElement('video');v.preload='metadata';v.muted=true;v.playsInline=true;var url=URL.createObjectURL(file);var done=false;function finish(blob,meta){if(done)return;done=true;URL.revokeObjectURL(url);res({blob:blob,meta:meta});}
v.addEventListener('loadedmetadata',function(){var meta={w:v.videoWidth,h:v.videoHeight,dur:v.duration};v.currentTime=Math.min(1,v.duration/2||0.5);v.addEventListener('seeked',function(){try{var c=document.createElement('canvas');var s=Math.min(1,960/Math.max(v.videoWidth,v.videoHeight));c.width=Math.round(v.videoWidth*s);c.height=Math.round(v.videoHeight*s);c.getContext('2d').drawImage(v,0,0,c.width,c.height);c.toBlob(function(b){finish(b,meta);},'image/jpeg',.82);}catch(e){finish(null,meta);}},{once:true});});
v.addEventListener('error',function(){finish(null,{});});setTimeout(function(){finish(null,{});},8000);v.src=url;});}
function send(fd,onProgress){return new Promise(function(res,rej){var x=new XMLHttpRequest();x.open('POST','/admin/media-upload.php');x.upload.onprogress=function(e){if(e.lengthComputable&&onProgress)onProgress(e.loaded/e.total);};x.onload=function(){try{var j=JSON.parse(x.responseText);if(j.ok)res(j);else rej(j.error||'Fel');}catch(e){rej('Oväntat svar ('+x.status+')');}};x.onerror=function(){rej('Nätverksfel');};x.send(fd);});}
function row(file){var d=document.createElement('div');d.className='upi';d.innerHTML='<img alt=""><span class="nm"></span><span class="pb"><i></i></span><span class="st">Väntar…</span>';d.querySelector('.nm').textContent=file.name+' ('+(file.size/1048576).toFixed(1)+' MB)';if(file.type.startsWith('image/'))d.querySelector('img').src=URL.createObjectURL(file);list.prepend(d);return d;}
var queue=[],busy=false;
function next(){if(busy||!queue.length)return;busy=true;var it=queue.shift();var r=it.row,file=it.file;r.querySelector('.st').textContent='Förbereder…';
posterFor(file).then(function(p){var fd=new FormData();fd.append('_csrf',csrf());fd.append('act',it.id?'replace':'add');if(it.id)fd.append('id',it.id);fd.append('cap',document.getElementById('up-cap').value);fd.append('cat',document.getElementById('up-cat').value);fd.append('file',file,file.name);if(p&&p.blob)fd.append('poster',p.blob,'poster.jpg');if(p&&p.meta){fd.append('w',p.meta.w||0);fd.append('h',p.meta.h||0);fd.append('dur',p.meta.dur||0);}
r.querySelector('.st').textContent='Laddar upp…';return send(fd,function(f){r.querySelector('.pb i').style.width=Math.round(f*100)+'%';});}).then(function(j){r.classList.add('ok');r.querySelector('.pb i').style.width='100%';r.querySelector('.st').textContent=it.id?'Utbytt ✓':'Klar ✓ ('+j.item.id+')';if(it.id&&it.rowEl)it.rowEl.classList.remove('row-busy');busy=false;if(!queue.length)setTimeout(function(){location.reload();},900);next();}).catch(function(e){r.classList.add('err');r.querySelector('.st').textContent='Fel';var m=document.createElement('div');m.className='flash err';m.textContent=(file.name+': '+e);r.after(m);if(it.rowEl)it.rowEl.classList.remove('row-busy');busy=false;next();});}
function add(files,id,rowEl){[].forEach.call(files,function(f){queue.push({file:f,row:row(f),id:id,rowEl:rowEl});});next();}
inp.addEventListener('change',function(){add(inp.files);inp.value='';});
['dragenter','dragover'].forEach(function(ev){drop.addEventListener(ev,function(e){e.preventDefault();drop.classList.add('over');});});
['dragleave','drop'].forEach(function(ev){drop.addEventListener(ev,function(e){e.preventDefault();drop.classList.remove('over');});});
drop.addEventListener('drop',function(e){add(e.dataTransfer.files);});
document.querySelectorAll('input.repl').forEach(function(el){el.addEventListener('change',function(){if(!el.files.length)return;var tr=el.closest('tr');tr.classList.add('row-busy');document.getElementById('up').scrollIntoView({behavior:'smooth'});add([el.files[0]],el.getAttribute('data-id'),tr);el.value='';});});
document.querySelectorAll('button.del').forEach(function(b){b.addEventListener('click',function(){var id=b.getAttribute('data-id');if(!confirm('Radera '+id+' permanent från galleriet och servern?'))return;var fd=new FormData();fd.append('_csrf',csrf());fd.append('act','delete');fd.append('id',id);var tr=b.closest('tr');tr.classList.add('row-busy');send(fd).then(function(){tr.remove();toast('Raderat');}).catch(function(e){tr.classList.remove('row-busy');toast(e,true);});});});
})();
