var tempUrl = _tempContentData.url + ":" + _tempContentData.port;
var socket = io.connect(window.location.hostname + ":3000/punch");

socket.on("new punch detected", (data) => {
    if (data.reload == true) {
        triggerRealtimeAttendance();
    }
});
socket.on("connected", (data) => {
    console.log(data);
});

socket.on("welcome", (msg) => { console.log(msg) });