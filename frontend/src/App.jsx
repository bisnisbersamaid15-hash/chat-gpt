import React, { useEffect, useMemo, useState } from "react";
import { DeviceList } from "./components/DeviceList.jsx";
import { EventTable } from "./components/EventTable.jsx";
import { InteractionOverlay } from "./components/InteractionOverlay.jsx";
import { Filters } from "./components/Filters.jsx";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";

export default function App() {
  const [devices, setDevices] = useState([]);
  const [events, setEvents] = useState([]);
  const [selectedDevice, setSelectedDevice] = useState("");
  const [filters, setFilters] = useState({ app: "", type: "" });
  const [deviceSearch, setDeviceSearch] = useState("");

  useEffect(() => {
    const loadDevices = async () => {
      const response = await fetch(`${API_BASE}/api/devices`);
      if (response.ok) {
        setDevices(await response.json());
      }
    };
    loadDevices();
    const interval = setInterval(loadDevices, 8000);
    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    const params = new URLSearchParams();
    if (selectedDevice) {
      params.set("device_id", selectedDevice);
    }
    if (filters.app) {
      params.set("app", filters.app);
    }
    if (filters.type) {
      params.set("type", filters.type);
    }
    const loadEvents = async () => {
      const response = await fetch(`${API_BASE}/api/events?${params.toString()}`);
      if (response.ok) {
        setEvents(await response.json());
      }
    };
    loadEvents();
  }, [selectedDevice, filters]);

  useEffect(() => {
    const token = localStorage.getItem("adminToken") || "admin-token";
    const ws = new WebSocket(`${API_BASE.replace("http", "ws")}/ws/admin?token=${token}`);
    ws.onmessage = (event) => {
      const payload = JSON.parse(event.data);
      setEvents((prev) => [payload, ...prev].slice(0, 200));
    };
    return () => ws.close();
  }, []);

  const filteredEvents = useMemo(() => {
    return events.filter((event) => {
      if (selectedDevice && event.device_id !== selectedDevice) {
        return false;
      }
      if (filters.app && event.app !== filters.app) {
        return false;
      }
      if (filters.type && event.type !== filters.type) {
        return false;
      }
      return true;
    });
  }, [events, selectedDevice, filters]);

  const filteredDevices = useMemo(() => {
    return devices.filter((device) => {
      if (!deviceSearch) return true;
      return (
        device.device_id.toLowerCase().includes(deviceSearch.toLowerCase()) ||
        (device.ip && device.ip.includes(deviceSearch))
      );
    });
  }, [devices, deviceSearch]);

  const activeDevice = devices.find((device) => device.device_id === selectedDevice);

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100">
      <header className="border-b border-slate-900 bg-slate-950/80 px-6 py-5 backdrop-blur">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <div>
            <p className="text-xs uppercase tracking-[0.3em] text-cyan-400">Backend Admin</p>
            <h1 className="text-3xl font-semibold">Interaction Control Center</h1>
            <p className="text-sm text-slate-400">
              Manage connected devices, review live interaction telemetry, and inspect UI touch
              bounds in real time.
            </p>
          </div>
          <div className="flex items-center gap-4 rounded-xl border border-slate-800 bg-slate-900/60 px-4 py-3">
            <div>
              <p className="text-xs text-slate-400">Devices Online</p>
              <p className="text-lg font-semibold text-cyan-300">
                {devices.filter((device) => device.status === "online").length}
              </p>
            </div>
            <div>
              <p className="text-xs text-slate-400">Total Events</p>
              <p className="text-lg font-semibold text-slate-200">{events.length}</p>
            </div>
          </div>
        </div>
      </header>

      <div className="grid gap-6 px-6 py-6 lg:grid-cols-[320px_1fr]">
        <aside className="space-y-4">
          <section className="rounded-xl border border-slate-900 bg-slate-900/70 p-4 shadow">
            <h2 className="mb-3 text-lg font-semibold">Devices</h2>
            <input
              className="mb-3 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200"
              placeholder="Search device or IP"
              value={deviceSearch}
              onChange={(event) => setDeviceSearch(event.target.value)}
            />
            <DeviceList
              devices={filteredDevices}
              selectedDevice={selectedDevice}
              onSelect={setSelectedDevice}
            />
          </section>

          <section className="rounded-xl border border-slate-900 bg-slate-900/70 p-4 shadow">
            <h2 className="mb-3 text-lg font-semibold">Selected Device</h2>
            {activeDevice ? (
              <div className="space-y-2 text-sm text-slate-300">
                <p className="text-base font-semibold text-slate-100">{activeDevice.device_id}</p>
                <p>Status: {activeDevice.status}</p>
                <p>IP: {activeDevice.ip || "unknown"}</p>
                <p>Last seen: {new Date(activeDevice.last_seen).toLocaleString()}</p>
              </div>
            ) : (
              <p className="text-sm text-slate-400">Select a device to view its status.</p>
            )}
          </section>
        </aside>

        <main className="space-y-6">
          <section className="rounded-xl border border-slate-900 bg-slate-900/70 p-4 shadow">
            <div className="flex flex-wrap items-center justify-between gap-3">
              <h2 className="text-lg font-semibold">Live Events</h2>
              <div className="text-xs text-slate-400">
                Streaming updates via <span className="text-cyan-300">/ws/admin</span>
              </div>
            </div>
            <Filters filters={filters} onChange={setFilters} />
            <EventTable events={filteredEvents} />
          </section>

          <section className="rounded-xl border border-slate-900 bg-slate-900/70 p-4 shadow">
            <h2 className="mb-3 text-lg font-semibold">Interaction Overlay View</h2>
            <InteractionOverlay events={filteredEvents} />
          </section>
        </main>
      </div>
    </div>
  );
}
