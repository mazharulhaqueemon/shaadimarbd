import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb", // emulate Pusher
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("accessToken")}`,
        },
    },
});
