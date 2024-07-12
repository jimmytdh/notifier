// Import the scripts needed for Firebase
importScripts('https://www.gstatic.com/firebasejs/9.16.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.16.0/firebase-messaging-compat.js');

// Your Firebase config
const firebaseConfig = {
    apiKey: "AIzaSyB_vRWWDwfiJVCA7RWOyP4lxyWn5QLYKmA",
    authDomain: "notifier-5e4e8.firebaseapp.com",
    databaseURL: "https://notifier-5e4e8-default-rtdb.firebaseio.com",
    projectId: "notifier-5e4e8",
    storageBucket: "notifier-5e4e8.appspot.com",
    messagingSenderId: "359294836752",
    appId: "1:359294836752:web:87c854779366d0f11d2a95",
    measurementId: "G-HEYDWWHLKV"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Cloud Messaging
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('Received background message ', payload);

    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
