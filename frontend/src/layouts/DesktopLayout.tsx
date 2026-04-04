import React from 'react';
import { Outlet, Link } from 'react-router-dom';

export const DesktopLayout = ({ roleTabs }: { roleTabs: { path: string; label: string; icon: string }[] }) => {
  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <aside className="w-64 bg-white border-r border-gray-200 flex flex-col shadow-sm">
        <div className="p-6 border-b border-gray-200">
          <h1 className="text-2xl font-bold text-gray-800 tracking-tight">OMS Pro</h1>
        </div>
        <nav className="flex-1 p-4 space-y-2">
          {roleTabs.map((tab) => (
            <Link key={tab.path} to={tab.path} className="flex items-center p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors">
              <span className="text-xl mr-3">{tab.icon}</span>
              <span className="font-medium">{tab.label}</span>
            </Link>
          ))}
        </nav>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-x-hidden overflow-y-auto">
        <header className="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10 h-16 flex items-center px-6">
          <div className="flex-1"></div>
          <div className="flex items-center space-x-4">
            <div className="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">U</div>
          </div>
        </header>
        <div className="p-6">
          <Outlet />
        </div>
      </main>
    </div>
  );
};
