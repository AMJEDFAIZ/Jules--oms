
import { Outlet, Link } from 'react-router-dom';

export const MobileLayout = ({ roleTabs }: { roleTabs: { path: string; label: string; icon: string }[] }) => {
  return (
    <div className="flex flex-col h-screen bg-gray-100">
      <main className="flex-1 overflow-y-auto pb-16 px-4 pt-4">
        <Outlet />
      </main>

      {/* Bottom Navigation */}
      <nav className="fixed bottom-0 w-full bg-white border-t border-gray-200 flex justify-around p-3 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        {roleTabs.map((tab) => (
          <Link key={tab.path} to={tab.path} className="flex flex-col items-center text-gray-500 hover:text-blue-600">
            <span className="text-xl mb-1">{tab.icon}</span>
            <span className="text-xs font-medium">{tab.label}</span>
          </Link>
        ))}
      </nav>
    </div>
  );
};
