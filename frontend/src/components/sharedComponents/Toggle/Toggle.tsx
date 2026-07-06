'use client';
import clsx from 'clsx';

interface IToggleProps {
  label: string;
  value?: boolean;
  onChange?: (value: boolean) => void;
  disabled?: boolean;
  className?: string;
  size?: 'default' | 'small';
}

export default function Toggle({
  label,
  value = false,
  onChange,
  disabled,
  className,
  size = 'default',
}: IToggleProps) {
  const isSmall = size === 'small';

  const toggle = () => {
    if (disabled) return;

    onChange?.(!value);
  };

  return (
    <label
      className={clsx(
        'flex items-center gap-3 cursor-pointer select-none',
        className
      )}
    >
      <span className='425:text-sm text-[12px] text-white'>{label}</span>

      <button
        type='button'
        onClick={toggle}
        disabled={disabled}
        className={clsx(
          'globalTransition cursor-pointer flex-shrink-0 flex-grow-0 relative rounded-full border border-solid border-outline-variant/30 focus:ring-primary focus:border-primary hover:border-primary',
          isSmall ? 'w-[46px] py-3' : 'w-[62px] py-4',
          value ? 'bg-primary-container' : 'bg-surface-container',
          disabled && 'opacity-50 cursor-not-allowed'
        )}
      >
        <span
          className={clsx(
            'absolute top-0.5 left-0.5 bg-on-surface rounded-full magenta-glow globalTransition',
            isSmall ? 'w-5 h-5' : 'w-7 h-7',
            value && (isSmall ? 'translate-x-5' : 'translate-x-7')
          )}
        />
      </button>
    </label>
  );
}
