importScripts("https://www.gstatic.com/firebasejs/8.2.6/firebase-app.js")
importScripts("https://www.gstatic.com/firebasejs/8.2.6/firebase-messaging.js")


var firebaseConfig = {
    apiKey: "AIzaSyC97YERH7lUQCEAOtT4nzjB9ybk0zMagx8",
    authDomain: "bookingdoctors-b3ddb.firebaseapp.com ",
    databaseURL: "https://bookingdoctors-b3ddb.firebaseio.com ",
    projectId: "bookingdoctors-b3dd",
    storageBucket: "bookingdoctors-b3ddb.appspot.com ",
    messagingSenderId: "1088799478892",
    appId: "1:1088799478892:web:3ad016506e6f4867707449",
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: '/firebase-logo.png'
    };

    self.registration.showNotification(notificationTitle,
        notificationOptions);
});