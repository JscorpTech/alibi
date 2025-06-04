// Firebase SDK import qilish kerak bo'lgan joy. Biroq, Service Worker'da importScripts orqali amalga oshiriladi.
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js');

// Firebase loyiha konfiguratsiyasini o'rnatish. Bu ma'lumotlar Firebase loyiha sozlamalaringizdan olinadi.
firebase.initializeApp({
    apiKey: "AIzaSyCe6h8QJgJFRv-n3G_KcZJcqMTpsoyZ_AU",
    authDomain: "alibi-store.firebaseapp.com",
    projectId: "alibi-store",
    storageBucket: "alibi-store.appspot.com",
    messagingSenderId: "582982193026",
    appId: "1:582982193026:web:829f766dad7e614147899b",
    measurementId: "G-B9G4K899S8"
  });

// Firebase Messaging obyektini olish va uni sozlash
const messaging = firebase.messaging();
