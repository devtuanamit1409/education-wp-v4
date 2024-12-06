document.addEventListener("DOMContentLoaded", function () {	
	const downloadButtons = document.querySelectorAll(".foxgget");
    let timers = new Map();
    let pageVisible = true; 
	const titleElement = document.querySelector(".foxggetpro");
	if (!titleElement) return;
    const waitingText = titleElement.dataset.secon;
    const continueText = titleElement.dataset.next;
	
	const decodeBase64 = (base64String) => {
        try {
            return atob(base64String);
        } catch (e) {
            return base64String; 
        }
    };
	
    const initTimer = (button) => {
        if (button.classList.contains("timer")) return; 
        let timer = parseInt(button.dataset.timer);
        const fileLink = decodeBase64(button.dataset.link);
        const showLinkOnly = button.dataset.window === "true";
		button.classList.remove("foxgetskin");
        button.classList.add("foxgetshow");
        button.classList.add("timer");
        button.innerHTML = `${waitingText} <b>${timer}</b> s`;
        const initCounter = setInterval(() => {
            if (!pageVisible) return;
            if (timer > 0) {
                timer--;
                button.innerHTML = `${waitingText} <b>${timer}</b> s`;
                timers.set(button, { interval: initCounter, remainingTime: timer });
            } else {
                clearInterval(initCounter);
                timers.delete(button); 

                if (showLinkOnly) {
                    button.innerHTML = `<a href="${fileLink}" target="_blank" class="download-link">${fileLink}</a>`;
                    button.classList.replace("timer", "disable-timer");
                } else {
                    location.href = fileLink;
                }
            }
        }, 1000);
        timers.set(button, { interval: initCounter, remainingTime: timer });
    };
    const pauseTimers = () => {
        timers.forEach((timerInfo) => {
            clearInterval(timerInfo.interval); 
        });
    };
    const resumeTimers = () => {
        timers.forEach((timerInfo, button) => {
            let remainingTime = timerInfo.remainingTime;
            button.innerHTML = `${continueText} <b>${remainingTime}</b> s`;
            const resumeCounter = setInterval(() => {
                if (!pageVisible) return; 

                if (remainingTime > 0) {
                    remainingTime--;
                    button.innerHTML = `${continueText} <b>${remainingTime}</b> s`;
                    timers.set(button, { interval: resumeCounter, remainingTime: remainingTime }); 
                } else {
                    clearInterval(resumeCounter);
                    timers.delete(button); 

                    const fileLink = decodeBase64(button.dataset.link);
                    const showLinkOnly = button.dataset.window === "true";
                    if (showLinkOnly) {
                        button.innerHTML = `<a href="${fileLink}" target="_blank" class="download-link">${fileLink}</a>`;
                        button.classList.replace("timer", "disable-timer");
                    } else {
                        location.href = fileLink;
                    }
                }
            }, 1000);

            timers.set(button, { interval: resumeCounter, remainingTime: remainingTime });
        });
    };
    document.addEventListener("visibilitychange", () => {
        pageVisible = !document.hidden; 

        if (!pageVisible) {
            pauseTimers(); 
        } else {
            resumeTimers(); 
        }
    });
    downloadButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            if (!button.classList.contains("disable-timer")) {
                initTimer(button);
            }
        });
    });
});