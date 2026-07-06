import React, { useCallback, useEffect, useRef, useState } from 'react';
import clsx from 'clsx';
import { useToast } from '@/src/context/ToastContext';
import { Icon } from '@/src/components/sharedComponents/Icon';

export type ToastType = 'success' | 'error' | 'info';

export interface ToastProps {
  id: string;
  message: string;
  type: ToastType;
  duration: number;
}

const typeStyles = {
  success: '!bg-background/80 text-on-surface',
  error: '!bg-primary-container/[0.3] text-on-surface',
  info: '!bg-secondary/[0.3] text-on-surface',
} as const;

const Toast: React.FC<ToastProps> = ({ id, message, type, duration }) => {
  const [isRemoving, setIsRemoving] = useState(false);
  const { removeToast } = useToast();
  const wrapperRef = useRef<HTMLDivElement>(null);

  const handleRemove = useCallback(() => {
    const wrapper = wrapperRef.current;
    if (!wrapper) return;

    const height = wrapper.getBoundingClientRect().height;
    wrapper.style.height = `${height}px`;
    setIsRemoving(true);
    requestAnimationFrame(() => {
      wrapper.style.height = '0px';
    });

    setTimeout(() => removeToast(id), 300);
  }, [id, removeToast]);

  useEffect(() => {
    const timer = setTimeout(handleRemove, duration);
    return () => clearTimeout(timer);
  }, [duration, handleRemove]);

  return (
    <div
      ref={wrapperRef}
      className={clsx(
        'transition-[height,margin] duration-300 ease-bounce w-fit ml-auto',
        isRemoving ? '!mb-0' : 'mb-2.5'
      )}
    >
      <div
        className={clsx(
          'glass-card max-w-72 min-w-52 ps-4 pe-10 py-2 rounded-3xl shadow-lg relative pointer-events-auto',
          isRemoving ? 'animate-to-left' : 'animate-to-right',
          typeStyles[type]
        )}
      >
        <button
          className='absolute top-1/2 -translate-y-1/2 right-2 coloredText'
          onClick={handleRemove}
        >
          <Icon name={'common/close-toast'} width={16} height={16} />
        </button>
        {message}
      </div>
    </div>
  );
};

export default React.memo(Toast);
