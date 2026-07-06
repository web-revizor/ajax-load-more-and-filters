import { ReactNode, useEffect, useRef } from 'react';

interface IClickOutsideProps {
  children: ReactNode;
  onOutsideClick?: () => void;
  className?: string;
  id?: string;
}

export default function ClickOutsideComponent({
  children,
  onOutsideClick,
  className,
  id,
}: IClickOutsideProps) {
  const wrapperRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (
        wrapperRef.current &&
        !wrapperRef.current.contains(event.target as Node) &&
        onOutsideClick
      ) {
        onOutsideClick();
      }
    }

    // Bind the event listener
    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      // Unbind the event listener on cleanup
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [onOutsideClick]);

  return (
    <div ref={wrapperRef} className={className} id={id}>
      {children}
    </div>
  );
}
