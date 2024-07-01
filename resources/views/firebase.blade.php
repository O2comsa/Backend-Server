<html>
<head>
    <title>Client app</title>
    <style>
        div {
            margin-bottom: 15px;
        }
    </style>
    <script src="https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.15.0/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.15.0/firebase-messaging.js"></script>


    <script>
        // web app's firebase config
        var firebaseConfig = {
            apiKey: "AIzaSyCvz3MR4PubFUBZq6HWmWsOye55HuDZZtc",
            authDomain: "eshartiapp.firebaseapp.com",
            projectId: "eshartiapp",
            storageBucket: "eshartiapp.appspot.com",
            messagingSenderId: "333932333949",
            appId: "1:333932333949:web:5e4bc235f67daa3e581a14",
            measurementId: "G-K68SC86FNM"
        };

        // initialize firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();
    </script>
</head>

<body>
<div id="token"></div>
<div id="msg"></div>
<div id="notis"></div>
<div id="err"></div>
<script>
    MsgElem = document.getElementById('msg');
    TokenElem = document.getElementById('token');
    NotisElem = document.getElementById('notis');
    ErrElem = document.getElementById('err');

    const messaging = firebase.messaging();
    messaging.requestPermission()
        .then(function () {
            MsgElem.innerHTML = 'Notification permission granted.'
            console.log('Notification permission granted.');

            // get the token in the form of promise
            return messaging.getToken()
        })
        .then(function(token) {
            TokenElem.innerHTML = 'token: ' + token
        })
        .catch(function (err) {
            ErrElem.innerHTML =  ErrElem.innerHTML + '; ' + err
            console.log('Unable to get permission to notify.', err);
        });

    messaging.onMessage(function(payload) {
        console.log('Message received. ', payload);
        NotisElem.innerHTML = NotisElem.innerHTML + JSON.stringify(payload)

        response = payload.hasOwnProperty('notification') ? payload.notification : payload.data;

        notificationTitle = response.title;
        notificationOptions = {
            body: response.body,
            icon: response.icon,
            image: response.image
        };
        var notification = new Notification(notificationTitle,notificationOptions);
    });
</script>
</body>
</html>
