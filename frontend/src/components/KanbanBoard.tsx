import { useState, useMemo } from 'react';

const COLUMNS = [
  { id: 'NEW', title: 'New Orders' },
  { id: 'CONFIRMED', title: 'Confirmed' },
  { id: 'PROCESSING', title: 'Processing' },
  { id: 'READY', title: 'Ready' },
  { id: 'WITH_DRIVER', title: 'With Driver' },
  { id: 'DELIVERED', title: 'Delivered' }
];

// Mock data
const mockOrders = [
  { id: 1, tracking_code: 'YMN-2026-001', customer_name: 'Ahmed', status: 'NEW', total: 150, currency: 'USD' },
  { id: 2, tracking_code: 'YMN-2026-002', customer_name: 'Ali', status: 'PROCESSING', total: 42000, currency: 'YER', is_urgent: true },
];

export const KanbanBoard = () => {
  const [orders, setOrders] = useState(mockOrders);

  const handleDragStart = (e: React.DragEvent, id: number) => {
    e.dataTransfer.setData('orderId', id.toString());
  };

  const handleDrop = (e: React.DragEvent, status: string) => {
    const id = parseInt(e.dataTransfer.getData('orderId'));
    setOrders(orders.map(o => o.id === id ? { ...o, status } : o));
  };

  // ⚡ Bolt Optimization: Memoize grouped orders to avoid O(N*C) filtering on every render
  // This reduces re-renders by grouping orders once per data change instead of 12 times (2x per 6 columns)
  const ordersByStatus = useMemo(() => {
    const grouped: Record<string, typeof orders> = {};
    // Initialize all columns with empty arrays
    COLUMNS.forEach(col => {
      grouped[col.id] = [];
    });
    // Group orders in a single pass O(N)
    orders.forEach(order => {
      if (!grouped[order.status]) {
        grouped[order.status] = [];
      }
      grouped[order.status].push(order);
    });
    return grouped;
  }, [orders]);

  return (
    <div className="flex space-x-4 overflow-x-auto pb-4 h-[calc(100vh-140px)]">
      {COLUMNS.map(col => {
        const columnOrders = ordersByStatus[col.id] || [];
        return (
          <div
            key={col.id}
            className="bg-gray-50 flex-shrink-0 w-80 rounded-xl p-4 flex flex-col border border-gray-200"
            onDragOver={(e) => e.preventDefault()}
            onDrop={(e) => handleDrop(e, col.id)}
          >
            <div className="flex justify-between items-center mb-4 px-1">
              <h3 className="font-semibold text-gray-700">{col.title}</h3>
              <span className="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full font-medium">
                {columnOrders.length}
              </span>
            </div>
            <div className="flex-1 overflow-y-auto space-y-3">
              {columnOrders.map(order => (
                <div
                  key={order.id}
                  draggable
                  onDragStart={(e) => handleDragStart(e, order.id)}
                  className={`bg-white p-4 rounded-lg shadow-sm border border-gray-100 cursor-move hover:shadow-md transition-shadow ${order.is_urgent ? 'border-l-4 border-l-red-500' : ''}`}
                >
                  <div className="flex justify-between items-start mb-2">
                    <span className="text-xs text-gray-500 font-mono">{order.tracking_code}</span>
                    {order.is_urgent && <span className="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Urgent</span>}
                  </div>
                  <h4 className="font-bold text-gray-800 mb-1">{order.customer_name}</h4>
                  <div className="text-sm font-medium text-blue-600">{order.total} {order.currency}</div>
                </div>
              ))}
            </div>
          </div>
        );
      })}
    </div>
  );
};
