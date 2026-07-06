import React from 'react';
import Toast, { ToastProps } from '@/src/components/sharedComponents/Toast/Toast';

interface ToastContainerProps {
  toasts: ToastProps[];
}

const ToastContainer: React.FC<ToastContainerProps> = ({ toasts }) => {
  if (toasts.length === 0) return null;

  return (
    <div className='fixed top-16 left-1/2 -translate-x-1/2 px-5 w-full z-[1000002] pointer-events-none'>
      {toasts.map((toast) => (
        <Toast key={toast.id} {...toast} />
      ))}
    </div>
  );
};

export default React.memo(ToastContainer);
