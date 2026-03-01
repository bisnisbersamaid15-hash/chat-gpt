import React, { useEffect, useRef } from "react";

const SCREEN_WIDTH = 1080;
const SCREEN_HEIGHT = 1920;

export function InteractionOverlay({ events }) {
  const canvasRef = useRef(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "rgba(15, 23, 42, 0.6)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const recent = events.slice(0, 25).reverse();
    recent.forEach((event, index) => {
      if (
        event.l === null ||
        event.t === null ||
        event.r === null ||
        event.b === null
      ) {
        return;
      }
      const scaleX = canvas.width / SCREEN_WIDTH;
      const scaleY = canvas.height / SCREEN_HEIGHT;
      const x = event.l * scaleX;
      const y = event.t * scaleY;
      const width = (event.r - event.l) * scaleX;
      const height = (event.b - event.t) * scaleY;
      ctx.strokeStyle = `rgba(34, 211, 238, ${0.2 + index / recent.length})`;
      ctx.lineWidth = 2;
      ctx.strokeRect(x, y, width, height);
    });
  }, [events]);

  return (
    <div className="flex flex-col gap-2">
      <canvas
        ref={canvasRef}
        width={360}
        height={640}
        className="w-full max-w-md rounded-lg border border-slate-800 bg-slate-950"
      />
      <p className="text-xs text-slate-500">
        Overlay renders last 25 interaction bounds scaled to 1080x1920 reference.
      </p>
    </div>
  );
}
