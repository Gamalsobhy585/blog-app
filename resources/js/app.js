import './bootstrap';
window.Echo.channel('test-channel')
    .listen('.test.event', (e) => {
        console.log('Pusher test event:', e);
        alert('Pusher works! Message: ' + e.message);
    });
