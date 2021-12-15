if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
  	var base_url = document.getElementsByTagName('base')[0].getAttribute('href');
    navigator.serviceWorker.register(base_url+'pwasw.js').then( () => {
    	console.log('Service Worker By VTH - Tech5s Registered');
    }).catch(()=>{
    	console.log('Service Worker By VTH - Tech5s Failed');
    });
  })
}