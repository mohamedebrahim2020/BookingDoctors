<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Html for Beginners">
    <link rel="stylesheet" type="text/css" href="bootstrap-4.4.1-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h2> requestfirebase token</h2>
        <div class="row">

            <div class="col-6">
                <ul></ul>
            </div>
        </div>

    </div>

    <script src="https://www.gstatic.com/firebasejs/8.2.6/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.6/firebase-messaging.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.6/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.6/firebase-functions.js"></script>


    <script>
        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        var firebaseConfig = {
            apiKey: "AIzaSyC97YERH7lUQCEAOtT4nzjB9ybk0zMagx8",
            authDomain: "bookingdoctors-b3ddb.firebaseapp.com",
            databaseURL: "https://bookingdoctors-b3ddb-default-rtdb.firebaseio.com",
            projectId: "bookingdoctors-b3ddb",
            storageBucket: "bookingdoctors-b3ddb.appspot.com",
            messagingSenderId: "1088799478892",
            appId: "1:1088799478892:web:3ad016506e6f4867707449",
            measurementId: "G-QJT2HFY3YQ"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        // firebase.analytics();
        const messaging = firebase.messaging();
        messaging.requestPermission().then(function() {
                //MsgElem.innerHTML = "Notification permission granted." 
                console.log("Notification permission ok");

                // get the token in the form of promise
                return messaging.getToken();
            })
            .then(function(token) {
                // print the token on the HTML page     
                console.log(token);



            })
            .catch(function(err) {
                console.log("Unable to get permission to notify.", err);
            });

            if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('service-worker.js').then(function(registration) {
      console.log('ServiceWorker registration successful with scope: ', registration.scope);
    }).catch(function(err) {
      //registration failed :(
      console.log('ServiceWorker registration failed: ', err);
    });
  }else {
    console.log('No service-worker on this browser');
  }
    </script>





</body>

</html>