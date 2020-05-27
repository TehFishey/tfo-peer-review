export function throttle(func, ms) {
    let timestamp;
    return () => {
        let now = Date.now();
        if(timestamp === undefined || now-timestamp > ms) {
            timestamp = now;
            func.apply(this,arguments);
        }
    };
}

export function debounce(func, ms) {
    let timer
    return () => {
        clearTimeout(timer)
        timer = setTimeout(() => {
            timer = null;
            func.apply(this,arguments);
        }, ms);
    };
}