// hieu ung tuyet roi
const LIFE_PER_TICK = 900 / 60;
const MAX_FLAKES = Math.min(75, screen.width / 1280 * 5);
const flakes = [];
const period = [
    n => 5 * (Math.sin(n)),
    n => 8 * (Math.cos(n)),
    n => 5 * (Math.sin(n) * Math.cos(2 * n)),
    n => 2 * (Math.sin(0.25 * n) - Math.cos(0.75 * n) + 1),
    n => 5 * (Math.sin(0.75 * n) + Math.cos(0.25 * n) - 1)
];
const fun = ["<img style='width:30px;height:30px;' src='data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBvbHlnb24gc3R5bGU9ImZpbGw6I0ZDQzE1MzsiIHBvaW50cz0iMjU2LDEyLjY1MiAxNzcuMTU3LDE3My4yNjEgMCwxOTguNTY5IDEyOC40ODYsMzIzLjE2NCA5OC4zMTQsNDk5LjM0OCAyNTYsNDE2LjYwOSANCgk0MTMuNjg2LDQ5OS4zNDggMzgzLjUxNCwzMjMuMTY0IDUxMiwxOTguNTY5IDMzNC44NDMsMTczLjI2MSAiLz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K'/>", "<img style='width:30px;height:30px;' src='data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPjxwYXRoIGQ9Ik00OTguNDExLDE4My4xMjVsLTE1Ny4zOTgsLTI0LjA0MmwtNzAuNTQ5LC0xNTAuMjcyYy01LjI2OSwtMTEuMjIyIC0yMy42OCwtMTEuMjIyIC0yOC45NDksLTBsLTcwLjUyOCwxNTAuMjcybC0xNTcuMzk4LDI0LjA0MmMtMTIuOTI4LDEuOTg0IC0xOC4wOSwxNy43MDcgLTkuMDI0LDI2Ljk4N2wxMTQuMzQ3LDExNy4yMDVsLTI3LjAyOSwxNjUuNjk2Yy0yLjE1NSwxMy4xNjMgMTEuOTA0LDIzLjA0IDIzLjUzLDE2LjU3NmwxNDAuNTg3LC03Ny42OTZsMTQwLjU4Nyw3Ny43MThjMTEuNTIsNi40IDI1LjcwNiwtMy4yODYgMjMuNTMsLTE2LjU3NmwtMjcuMDI5LC0xNjUuNjk2bDExNC4zNDcsLTExNy4yMDZjOS4wNjYsLTkuMzAxIDMuODgyLC0yNS4wMjQgLTkuMDI0LC0yNy4wMDhaIiBzdHlsZT0iZmlsbDojZjAwO2ZpbGwtcnVsZTpub256ZXJvOyIvPjxnPjxwYXRoIGQ9Ik0yNDIuMTE3LDI1MS42MThsMjQuMjM3LC0yNC4yMzhjMS42ODgsLTEuNjg4IDIuMjU5LC00LjE5NCAxLjQ2OSwtNi40NDdjLTAuNzksLTIuMjUyIC0yLjgwMSwtMy44NTIgLTUuMTc0LC00LjExNmwtMzkuNjYzLC00LjQwN2MtMS44NzYsLTAuMjA4IC0zLjc0NCwwLjQ0NyAtNS4wNzcsMS43ODFsLTQ0LjA3LDQ0LjA3Yy0yLjQyNSwyLjQyNSAtMi40MjUsNi4zNTcgMCw4Ljc4M2wyMi4wMzYsMjIuMDM1YzEuMTY0LDEuMTY1IDIuNzQzLDEuODE5IDQuMzkxLDEuODE5YzEuNjQ3LC0wIDMuMjI2LC0wLjY1NCA0LjM5MSwtMS44MTlsMTkuODMxLC0xOS44MzFsOTQuNzE4LDk0LjcxOGM0Ljg2NSw0Ljg2NSAxMi43NTgsNC44NjUgMTcuNjI2LC0wLjAwM2M0Ljg2OCwtNC44NjggNC44NjgsLTEyLjc2MSAwLjAwMywtMTcuNjI2bC05NC43MTgsLTk0LjcxOVoiIHN0eWxlPSJmaWxsOiNmZmVhMDA7ZmlsbC1ydWxlOm5vbnplcm87Ii8+PHBhdGggZD0iTTIwMi40NTcsMzI2LjU2OGMtMi4zMiwtMi40MDEgLTYuNDY5LC0yLjM5OCAtOC43OSwtMGwtMTcuNjI0LDE3LjYyOGMtMC4wMSwwLjAxIC0wLjAxOCwwLjAyMSAtMC4wMjgsMC4wMzFsLTE5LjA1NiwxOS4wNTdjLTIuNDI1LDIuNDI1IC0yLjQyNSw2LjM1NyAwLDguNzgyYzEuMjEzLDEuMjEzIDIuODAyLDEuODE5IDQuMzkxLDEuODE5YzEuNTksMCAzLjE3OSwtMC42MDYgNC4zOTEsLTEuODE5bDE0LjgyNiwtMTQuODI2YzE5LjkxOSwxNy43MTkgNDUuMTUyLDI2Ljc1MSA3MC40NzgsMjYuNzUxYzE5LjA3MywtMC4wMDQgMzguMTk2LC01LjEyMiA1NS4xMzMsLTE1LjQ5MmwtMjcuNjAyLC0yNy42MDJjLTI1LjQ1MywxMS4yODEgLTU1LjcsNi4wOTQgLTc2LjExOSwtMTQuMzI5WiIgc3R5bGU9ImZpbGw6I2ZmZWEwMDtmaWxsLXJ1bGU6bm9uemVybzsiLz48cGF0aCBkPSJNMzI1Ljg1MSwyMDMuMTc0Yy0yNC40NzYsLTI0LjQ3MiAtNTQuNTUzLC0zNC44MzUgLTg2Ljk4NywtMjkuOTY2Yy0yLjgzNiwwLjQyMiAtNS4wMDUsMi43MzYgLTUuMjU3LDUuNTkzYy0wLjI1NywyLjg1NyAxLjQ4Miw1LjUxNSA0LjIwMiw2LjQzNGMwLjA5MSwwLjAyOSA5LjU3NiwzLjI2NyAyMS43ODUsMTAuMjNjMTEuMzE5LDYuNDU4IDI3LjMyLDE3LjUwNCAzOS4yNjQsMzMuNDQ3YzE5LjAyMywyNS4zOTEgMjQuMTgyLDUzLjU5OCAxNC43NTksNzYuNjQ1bDI3Ljc1MSwyNy43NDdjMjUuMjA5LC00MS4xNzcgMTkuMzY3LC05NS4yNDYgLTE1LjUxNywtMTMwLjEzWiIgc3R5bGU9ImZpbGw6I2ZmZWEwMDtmaWxsLXJ1bGU6bm9uemVybzsiLz48L2c+PC9zdmc+'/>"];
const cssString = `.snowfall-container {
    display: block;
    height: 100vh;
    left: 0;
    margin: 0;
    padding: 0;
    -webkit-perspective-origin: top center;
            perspective-origin: top center;
    -webkit-perspective: 150px;
            perspective: 150px;
    pointer-events: none;
    position: fixed;
    top: 0;
    -webkit-transform-style: preserve-3d;
            transform-style: preserve-3d;
    width: 100%;
    z-index: 99999; 
	}
  .snowflake {
    pointer-events: none;
    color: #ddf;
    display: block;
    font-size: 24px;
    left: -12px;
    line-height: 24px;
    position: absolute;
    top: -12px;
    -webkit-transform-origin: center;
    transform-origin: center; 
	animation: snowflakeScale 1s infinite ease-in-out;
	}
	@keyframes snowflakeScale {
    0%, 100% {
        opacity:0.1;
    }
    50% {
        opacity:1;
    }
	}`;
function ready(fn) {
    if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
        fn();
    }
    else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}
function resetFlake(flake) {
    let x = flake.dataset.origX = (Math.random() * 100);
    let y = flake.dataset.origY = 100; // Thay đổi vị trí khởi tạo y từ 0 thành 100 (dưới cùng)
    let z = flake.dataset.origZ = (Math.random() < 0.1) ? (Math.ceil(Math.random() * 100) + 25) : 0;
    let life = flake.dataset.life = (Math.ceil(Math.random() * 4000) + 6000); 
    flake.dataset.origLife = life;
    flake.style.transform = `translate3d(${x}vw, ${y}vh, ${z}px)`;
    flake.style.opacity = 1.0;
    flake.dataset.periodFunction = Math.floor(Math.random() * period.length);

    if (Math.random() < 0.1) {
        flake.innerHTML = fun[Math.floor(Math.random() * fun.length)];
    }
}

function updatePositions() {
    flakes.forEach((flake) => {
        let origLife = parseFloat(flake.dataset.origLife);
        let curLife = parseFloat(flake.dataset.life);
        let dt = (origLife - curLife) / origLife;

        if (dt <= 1.0) {
            let p = period[parseInt(flake.dataset.periodFunction)];
            let x = p(dt * 2 * Math.PI) + parseFloat(flake.dataset.origX);
            let y = 100 * (1 - dt); // Thay đổi hướng di chuyển của y để bay từ dưới lên
            let z = parseFloat(flake.dataset.origZ);
            flake.style.transform = `translate3d(${x}vw, ${y}vh, ${z}px)`;
            if (dt >= 0.5) {
                flake.style.opacity = (1.0 - ((dt - 0.5) * 2));
            }
            curLife -= LIFE_PER_TICK;
            flake.dataset.life = curLife;
        } else {
            resetFlake(flake);
        }
    });
    window.requestAnimationFrame(updatePositions);
}

function appendSnow() {
    let styles = document.createElement('style');
    styles.innerText = cssString;
    document.querySelector('head').appendChild(styles);
    let field = document.createElement('div');
    field.classList.add('snowfall-container');
    field.setAttribute('aria-hidden', 'true');
    field.setAttribute('role', 'presentation');
    document.body.appendChild(field);
    let i = 0;
    const addFlake = () => {
        let flake = document.createElement('span');
        flake.classList.add('snowflake');
        flake.setAttribute('aria-hidden', 'true');
        flake.setAttribute('role', 'presentation');
        flake.innerHTML = "<img style='width:30px;height:30px;' src='data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNTEyIDUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjU2IiBjeT0iMjU2IiBmaWxsPSIjZDgwMDI3IiByPSIyNTYiLz48cGF0aCBkPSJtMjU2IDEzMy41NjUgMjcuNjI4IDg1LjAyOWg4OS40MDVsLTcyLjMzMSA1Mi41NSAyNy42MjggODUuMDMtNzIuMzMtNTIuNTUxLTcyLjMzIDUyLjU1MSAyNy42MjgtODUuMDMtNzIuMzMtNTIuNTVoODkuNDA0eiIgZmlsbD0iI2ZmZGE0NCIvPjxnLz48Zy8+PGcvPjxnLz48Zy8+PGcvPjxnLz48Zy8+PGcvPjxnLz48Zy8+PGcvPjxnLz48Zy8+PGcvPjwvc3ZnPg=='/>";
        resetFlake(flake);
        flakes.push(flake);
        field.appendChild(flake);
        if (i++ <= MAX_FLAKES) {
            setTimeout(addFlake, Math.ceil(Math.random() * 300) + 100);
        }
    };
    addFlake();
    updatePositions();
}
ready(appendSnow);