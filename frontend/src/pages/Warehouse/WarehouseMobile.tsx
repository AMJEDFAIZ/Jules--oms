import { useState } from 'react';
import { MobileOrderCard } from '../../components/MobileOrderCard';

const TABS = ['PROCESSING', 'READY', 'ASSIGNED'];

export const WarehouseMobile = () => {
  const [activeTab, setActiveTab] = useState('PROCESSING');
  const [orders, setOrders] = useState([
    { id: 1, tracking_code: 'YMN-2026-001', customer_name: 'Ahmed', status: 'PROCESSING', total: 150, currency: 'USD' },
  ]);

  const handleAction = (id: number) => {
    setOrders(orders.map(o => o.id === id ? { ...o, status: 'READY' } : o));
    setActiveTab('READY');
  };

  return (
    <div>
      <h2 className="text-xl font-bold mb-4">Warehouse Tasks</h2>

      {/* Segmented Control */}
      <div className="flex bg-gray-200 p-1 rounded-lg mb-6">
        {TABS.map(tab => (
          <button
            key={tab}
            onClick={() => setActiveTab(tab)}
            className={`flex-1 text-sm font-medium py-2 rounded-md ${activeTab === tab ? 'bg-white shadow text-blue-600' : 'text-gray-600'}`}
          >
            {tab}
          </button>
        ))}
      </div>

      <div>
        {orders.filter(o => o.status === activeTab).map(order => (
          <MobileOrderCard
            key={order.id}
            order={order}
            actionLabel={activeTab === 'PROCESSING' ? 'Mark Ready' : 'Assign Driver'}
            onActionClick={handleAction}
          />
        ))}
        {orders.filter(o => o.status === activeTab).length === 0 && (
          <div className="text-center text-gray-500 py-10">No orders in this status.</div>
        )}
      </div>
    </div>
  );
};
