<script src="https://www.gstatic.com/firebasejs/9.16.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.16.0/firebase-database-compat.js"></script>
<script>
    let notification_counter = 0;
    let speaker = new SpeechSynthesisUtterance();
    speaker.text = "New Request!";

    toastr.options = {
        "positionClass": "toast-bottom-right",
    }

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

    firebase.initializeApp(firebaseConfig);
    window.__firebaseListeners = [];

    function insert(child, data, callback) {
        firebase.database().ref(child).push().set(data, function (err) {
            if (err) {
                callback("Something went wrong");
            } else {
                callback("Success");
            }
        });
    }

    function observe(child, callback) {
        const ref = firebase.database().ref(child);
        const handler = (data) => callback(data);
        ref.on('child_added', handler);
        window.__firebaseListeners.push({ref: ref, event: 'child_added', handler: handler});
    }

    function observeRemoved(child, callback) {
        const ref = firebase.database().ref(child);
        const handler = (data) => callback(data);
        ref.on('child_removed', handler);
        window.__firebaseListeners.push({ref: ref, event: 'child_removed', handler: handler});
    }

    function get(child, callback) {
        firebase.database().ref(child).once("value", (data) => {
            callback(data);
        });
    }

    function deleteChild(child) {
        firebase.database().ref(child).remove();
    }

    function clearFirebaseListeners() {
        if (!window.__firebaseListeners || window.__firebaseListeners.length < 1) return;
        window.__firebaseListeners.forEach(item => {
            try {
                item.ref.off(item.event, item.handler);
            } catch (e) {}
        });
        window.__firebaseListeners = [];
    }

</script>
