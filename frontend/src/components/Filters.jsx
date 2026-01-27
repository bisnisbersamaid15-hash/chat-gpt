import React from "react";

export function Filters({ filters, onChange }) {
  return (
    <div className="mb-4 flex flex-wrap gap-3 text-sm">
      <label className="flex flex-col gap-1">
        <span className="text-xs text-slate-400">App</span>
        <input
          className="rounded border border-slate-700 bg-slate-950 px-2 py-1"
          value={filters.app}
          onChange={(event) => onChange({ ...filters, app: event.target.value })}
          placeholder="com.example.app"
        />
      </label>
      <label className="flex flex-col gap-1">
        <span className="text-xs text-slate-400">Event Type</span>
        <input
          className="rounded border border-slate-700 bg-slate-950 px-2 py-1"
          value={filters.type}
          onChange={(event) => onChange({ ...filters, type: event.target.value })}
          placeholder="TYPE_VIEW_CLICKED"
        />
      </label>
    </div>
  );
}
