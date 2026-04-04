import React from 'react';

interface MobileOrderCardProps {
  order: any;
  onActionClick: (id: number) => void;
  actionLabel: string;
}

export const MobileOrderCard = ({ order, onActionClick, actionLabel }: MobileOrderCardProps) => {
  return (
    <div className={`bg-white rounded-xl p-4 shadow-sm mb-3 border ${order.is_urgent ? 'border-red-200' : 'border-gray-100'}`}>
      <div className="flex justify-between items-start mb-3">
        <div>
          <div className="text-xs text-gray-500 font-mono mb-1">{order.tracking_code}</div>
          <h3 className="font-bold text-lg text-gray-800">{order.customer_name}</h3>
        </div>
        <div className="flex flex-col items-end">
          {order.is_urgent && <span className="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-md font-bold mb-1">URGENT</span>}
          <span className="font-semibold text-blue-600">{order.total} {order.currency}</span>
        </div>
      </div>

      <div className="flex justify-between items-center mt-4">
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
          {order.status}
        </span>
        <div className="space-x-2">
          <button className="text-sm px-3 py-1.5 text-gray-600 bg-gray-50 border border-gray-200 rounded-lg font-medium">
            View
          </button>
          <button
            onClick={() => onActionClick(order.id)}
            className="text-sm px-4 py-1.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
          >
            {actionLabel}
          </button>
        </div>
      </div>
    </div>
  );
};
