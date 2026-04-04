import React, { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { DesktopLayout } from './layouts/DesktopLayout';
import { MobileLayout } from './layouts/MobileLayout';
import { KanbanBoard } from './components/KanbanBoard';
import { WarehouseMobile } from './pages/Warehouse/WarehouseMobile';

function App() {
  const [isMobile, setIsMobile] = useState(window.innerWidth < 768);

  useEffect(() => {
    const handleResize = () => setIsMobile(window.innerWidth < 768);
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  // Example role tabs
  const warehouseTabs = [
    { path: '/warehouse/tasks', label: 'Tasks', icon: '📦' },
    { path: '/warehouse/history', label: 'History', icon: '🕒' },
  ];

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Navigate to="/warehouse/tasks" replace />} />

        {isMobile ? (
          <Route element={<MobileLayout roleTabs={warehouseTabs} />}>
            <Route path="/warehouse/tasks" element={<WarehouseMobile />} />
            <Route path="/warehouse/history" element={<div>History</div>} />
          </Route>
        ) : (
          <Route element={<DesktopLayout roleTabs={warehouseTabs} />}>
            <Route path="/warehouse/tasks" element={<KanbanBoard />} />
            <Route path="/warehouse/history" element={<div>History</div>} />
          </Route>
        )}
      </Routes>
    </BrowserRouter>
  );
}

export default App;
