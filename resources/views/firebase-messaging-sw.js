// // Import and configure the Firebase SDK
// // These scripts are made available when the app is served or deployed on Firebase Hosting
// // If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup
// importScripts("https://www.gstatic.com/firebasejs/8.2.6/firebase-app.js")
// importScripts("https://www.gstatic.com/firebasejs/8.2.6/firebase-messaging.js")


// // Your web app's Firebase configuration
// var firebaseConfig = {
//     apiKey: "AIzaSyC97YERH7lUQCEAOtT4nzjB9ybk0zMagx8",
//     authDomain: "bookingdoctors-b3ddb.firebaseapp.com ",
//     databaseURL: "https://bookingdoctors-b3ddb.firebaseio.com ",
//     projectId: "bookingdoctors-b3dd",
//     storageBucket: "bookingdoctors-b3ddb.appspot.com ",
//     messagingSenderId: "1088799478892",
//     appId: "1:1088799478892:web:3ad016506e6f4867707449",
// };


// // Initialize Firebase
// firebase.initializeApp(firebaseConfig);

// const messaging = firebase.messaging();
// /**
//  * Here is is the code snippet to initialize Firebase Messaging in the Service
//  * Worker when your app is not hosted on Firebase Hosting.
//  // [START initialize_firebase_in_sw]
//  // Give the service worker access to Firebase Messaging.
//  // Note that you can only use Firebase Messaging here, other Firebase libraries
//  // are not available in the service worker.
//  importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js');
//  importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js');
//  // Initialize the Firebase app in the service worker by passing in the
//  // messagingSenderId.
//  firebase.initializeApp({
//    'messagingSenderId': 'YOUR-SENDER-ID'
//  });
//  // Retrieve an instance of Firebase Messaging so that it can handle background
//  // messages.
//  const messaging = firebase.messaging();
//  // [END initialize_firebase_in_sw]
//  **/

// // If you would like to customize notifications that are received in the
// // background (Web app is closed or not in browser focus) then you should
// // implement this optional method.
// // [START background_handler]

// // [END background_handler]

// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('D:/ITI/firebase/firebase-messaging-sw.js')
//         .then(function(registration) {
//             console.log('Registration successful, scope is:', registration.scope);
//         }).catch(function(err) {
//             console.log('Service worker registration failed, error:', err);
//         });
// }