'use client';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import clsx from 'clsx';

interface SlideDownProps {
  children: React.ReactNode;
  isOpen: boolean;
}

export default function SlideDown({ children, isOpen }: SlideDownProps) {
  const [contentHeight, setContentHeight] = useState<number | null>(null);
  const contentRef = useRef<HTMLDivElement | null>(null);
  const resizeObserverRef = useRef<ResizeObserver | null>(null);

  const updateHeight = useCallback(() => {
    if (contentRef.current) {
      const element = contentRef.current;
      const newHeight = element.scrollHeight;
      setContentHeight(newHeight);
    }
  }, []);

  useEffect(() => {
    if (!isOpen) return;
    if (typeof window === 'undefined' || !window.ResizeObserver) {
      updateHeight();
      return;
    }

    if (contentRef.current) {
      resizeObserverRef.current = new ResizeObserver(() => {
        updateHeight();
      });

      resizeObserverRef.current.observe(contentRef.current);
    }

    return () => {
      if (resizeObserverRef.current) {
        resizeObserverRef.current.disconnect();
      }
    };
  }, [updateHeight, isOpen, contentHeight]);

  return (
    <div
      className={clsx(
        'transition-[height,opacity] duration-300 ease-global',
        isOpen ? 'opacity-100' : 'opacity-0 overflow-hidden'
      )}
      style={{
        height: isOpen ? `${contentHeight}px` : '0px',
      }}
    >
      <div ref={contentRef}>{children}</div>
    </div>
  );
}
