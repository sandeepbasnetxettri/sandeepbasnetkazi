"use strict";
const { PI: π, sin, cos, exp } = Math;
let c, ctx, W, H;

let paused = false;
let fc = 0;
let fid = 0;

let step = 0.02;
let points = [];

let infoBtn;

const setup = () => {
    c = document.getElementById("Canvas");
    ctx = c.getContext("2d");
    [W, H] = setSize(c, ctx);
    window.onresize = () => {
        [W, H] = setSize(c, ctx);
        fc = 0;
        points = [];
    }
    
    infoBtn = document.getElementById("Info");
    infoBtn.onclick = () => alert("Animated Butterfly Pattern - Parametric design inspired by butterfly wings.")

    c.onclick = () => {
        paused ? window.requestAnimationFrame(animate) : window.cancelAnimationFrame(fid);
        paused = !paused;
    }

    c.ondblclick = () => {
        clear(ctx);
        fc = 0;
        points = [];
    }

    window.requestAnimationFrame(animate);
};

const animate = () => {
    let θ = fc * step;
    if (θ <= 12 * π) {
        // Butterfly function inspired by sinusoidal patterns
        let scale = 100;
        let x = scale * sin(θ) * (exp(cos(θ)) - 2 * cos(4 * θ) - sin(θ / 12) ** 5);
        let y = scale * cos(θ) * (exp(cos(θ)) - 2 * cos(4 * θ) - sin(θ / 12) ** 5);
        points.push({ x, y, color: `hsl(${(fc * 4) % 360}, 100%, 70%)` });
    }

    clear(ctx, "rgba(0, 0, 0, 1)");
    ctx.save();
    ctx.translate(W / 2, H / 2);
    
    if (points.length > 1) {
        ctx.lineJoin = "round";
        ctx.lineCap = "round";
        for (let i = 1; i < points.length; i++) {
            ctx.strokeStyle = points[i].color;
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(points[i - 1].x, points[i - 1].y);
            ctx.lineTo(points[i].x, points[i].y);
            ctx.stroke();
        }
    }
    
    ctx.restore();
    fc++;
    fid = window.requestAnimationFrame(animate);
};

const clear = (ctx, color = "rgba(0, 0, 0, 1)", w = window.innerWidth, h = window.innerHeight) => {
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, w, h);
};

const setSize = (c, ctx, w = window.innerWidth, h = window.innerHeight, pd = devicePixelRatio) => {
    c.style.width = `${w}px`;
    c.style.height = `${h}px`;
    c.width = w * pd;
    c.height = h * pd;
    ctx.scale(pd, pd);
    return [w, h];
};

window.onload = setup;
