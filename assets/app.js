import  './scss/app.scss'
import preactCustomElement from './functions/preact.js'
import {Swipper} from  './utils/Swipper.js'
import {SentryClient} from  './utils/SentryClient.js'
import {DisplayEmail} from './utils/DisplayEmail.js'
import {CaptachaForm} from './elements/CaptachaForm'

document.addEventListener('DOMContentLoaded',()=>{
  new SentryClient();
  new DisplayEmail();
  new Swipper();
  customElements.define('captacha-form',CaptachaForm);
}, false);

if ('serviceWorker' in navigator) {
  window.addEventListener('load', async () => {
    try {
      const registration = await navigator.serviceWorker.register('/service-worker.js');
      console.log('ServiceWorker registered:', registration);
    } catch (error) {
      console.error('ServiceWorker registration failed:', error);
    }
  });
}

window.addEventListener('pageshow', (event) => {
  if (event.persisted) {
    new SentryClient();
    new DisplayEmail();
    new Swipper();
  } else {
    console.log('This page was loaded normally.');
  }
});
