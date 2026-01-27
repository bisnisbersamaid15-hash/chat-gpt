import React from "react";

export function DeviceList({ devices, selectedDevice, onSelect }) {
  if (!devices.length) {
    return <p className="text-sm text-slate-400">No devices connected.</p>;
  }

  return (
    <ul className="space-y-2">
      {devices.map((device) => (
        <li
          key={device.device_id}
          className={`rounded-lg border px-3 py-2 text-sm transition ${
            selectedDevice === device.device_id
              ? "border-cyan-400/70 bg-cyan-500/10"
              : "border-slate-800 bg-slate-950/40"
          }`}
        >
          <button
            type="button"
            className="flex w-full flex-col items-start gap-1"
            onClick={() => onSelect(device.device_id)}
          >
            <div className="flex w-full items-center justify-between gap-2">
              <span className="font-semibold">{device.device_id}</span>
              <span
                className={`rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase ${
                  device.status === "online"
                    ? "bg-emerald-500/20 text-emerald-300"
                    : "bg-slate-700/40 text-slate-300"
                }`}
              >
                {device.status}
              </span>
            </div>
            <span className="text-xs text-slate-400">
              IP: {device.ip || "unknown"} • Last seen {new Date(device.last_seen).toLocaleString()}
            </span>
          </button>
        </li>
      ))}
    </ul>
  );
}
