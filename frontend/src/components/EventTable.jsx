import React from "react";

export function EventTable({ events }) {
  if (!events.length) {
    return <p className="text-sm text-slate-400">No events yet.</p>;
  }

  return (
    <div className="max-h-[360px] overflow-auto rounded-lg border border-slate-800">
      <table className="w-full text-left text-xs">
        <thead className="sticky top-0 bg-slate-950 text-slate-400">
          <tr>
            <th className="px-3 py-2">Time</th>
            <th className="px-3 py-2">Device</th>
            <th className="px-3 py-2">App</th>
            <th className="px-3 py-2">Type</th>
            <th className="px-3 py-2">Text</th>
            <th className="px-3 py-2">Content Desc</th>
            <th className="px-3 py-2">View ID</th>
          </tr>
        </thead>
        <tbody>
          {events.map((event, index) => (
            <tr key={`${event.device_id}-${index}`} className="border-t border-slate-900">
              <td className="px-3 py-2 text-slate-300">
                {new Date(event.ts).toLocaleString()}
              </td>
              <td className="px-3 py-2 text-slate-200">{event.device_id}</td>
              <td className="px-3 py-2 text-slate-200">{event.app}</td>
              <td className="px-3 py-2 text-cyan-300">{event.type}</td>
              <td className="px-3 py-2 text-slate-300">{event.text || "-"}</td>
              <td className="px-3 py-2 text-slate-300">{event.content_description || "-"}</td>
              <td className="px-3 py-2 text-slate-300">{event.view_id || "-"}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
